<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\ClientDocument;
use App\Models\DocumentType;
use App\Traits\HasBreadcrumbs;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
class ClientDocumentForm extends Component
{
    use HasBreadcrumbs;

    // Propiedades del documento
    public $document_type_id = '';
    public $document_number = '';
    public $verification_digit = '';
    public $expiration_date = null;
    public $is_primary = false;

    // Propiedades de control
    public $clientId;
    public $documentId;
    public $editMode = false;
    public $client;
    public $selectedDocumentType;

    public function mount($clientId, $documentId = null)
    {
        Log::info('ClientDocumentForm::mount - Starting mount', [
            'clientId' => $clientId,
            'documentId' => $documentId
        ]);

        try {
            if (!$clientId) {
                throw new \Exception('Client ID is required');
            }

            $this->clientId = $clientId;
            $this->client = Client::findOrFail($clientId);

            Log::info('ClientDocumentForm::mount - Client loaded', [
                'client_id' => $this->client->id,
                'client_name' => $this->client->full_name
            ]);

            if ($documentId) {
                $this->documentId = $documentId;
                $this->editMode = true;
                $this->loadDocument();
            }

        } catch (\Exception $e) {
            Log::error('ClientDocumentForm::mount - Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    private function loadDocument()
    {
        Log::info('ClientDocumentForm::loadDocument - Loading document', [
            'documentId' => $this->documentId
        ]);

        try {
            $document = ClientDocument::with('documentType')->findOrFail($this->documentId);

            $this->document_type_id = $document->document_type_id;
            $this->document_number = $document->document_number;
            $this->verification_digit = $document->verification_digit;
            $this->expiration_date = $document->expiration_date ? $document->expiration_date->format('Y-m-d') : null;
            $this->is_primary = $document->is_primary;
            $this->selectedDocumentType = $document->documentType;

            Log::info('ClientDocumentForm::loadDocument - Document loaded', [
                'document' => $document->toArray()
            ]);
        } catch (\Exception $e) {
            Log::error('ClientDocumentForm::loadDocument - Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }

    }

    public function updatedDocumentTypeId()
    {
        if ($this->document_type_id) {
            $this->selectedDocumentType = DocumentType::find($this->document_type_id);
        } else {
            $this->selectedDocumentType = null;
        }
        $this->verification_digit = '';
    }

    protected function getBaseBreadcrumbs()
    {
        Log::info('ClientDocumentForm::getBaseBreadcrumbs - Validating client data', [
            'clientId' => $this->clientId,
            'client_properties' => [
                'id' => $this->client->id ?? null,
                'full_name' => $this->client->full_name ?? null
            ]
        ]);

        $clientParams = ['clientId' => $this->client->id];

        Log::info('ClientDocumentForm::getBaseBreadcrumbs - Route params', [
            'params' => $clientParams
        ]);

        return [
            [
                'name' => 'Clientes',
                'route' => 'clients.index'
            ],
            [
                'name' => $this->client->full_name ?? 'Cliente',
                'route' => 'clients.show',
                'params' => $clientParams // Asegurarnos de que este objeto contiene exactamente lo que la ruta espera
            ],
            [
                'name' => $this->editMode ? 'Editar Documento' : 'Nuevo Documento'
            ]
        ];
    }

    public function addDocumentType()
    {
        $breadcrumbs = [
            ['name' => 'Clientes', 'route' => 'clients.index'],
            ['name' => $this->client->full_name, 'route' => 'clients.show', 'params' => ['clientId' => $this->clientId]],
            ['name' => $this->editMode ? 'Editar Documento' : 'Nuevo Documento']
        ];

        $encodedBreadcrumbs = base64_encode(json_encode($breadcrumbs));

        return redirect()->route('document-types.create', [
            'redirect_to' => 'client-document',
            'parent_breadcrumbs' => $encodedBreadcrumbs,
            'clientId' => $this->clientId
        ]);
    }

    public function store()
    {

        Log::info('ClientDocumentForm::store - Starting store process', [
            'clientId' => $this->clientId,
            'document_data' => [
                'document_type_id' => $this->document_type_id,
                'document_number' => $this->document_number,
                'is_primary' => $this->is_primary
            ]
        ]);

        $this->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'document_number' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    // Verificar duplicados solo para el mismo tipo de documento
                    $exists = ClientDocument::where('document_type_id', $this->document_type_id)
                        ->where('document_number', $value)
                        ->where('client_id', '!=', $this->clientId)
                        ->exists();

                    if ($exists) {
                        $fail('Este número de documento ya está registrado para otro cliente.');
                    }
                },
            ],
            'verification_digit' => [
                'nullable',
                'string',
                'max:2',
                function ($attribute, $value, $fail) {
                    if (
                        $this->selectedDocumentType &&
                        $this->selectedDocumentType->requires_verification_digit &&
                        empty($value)
                    ) {
                        $fail('El dígito verificador es requerido para este tipo de documento.');
                    }
                },
            ],
            'expiration_date' => 'nullable|date',
            'is_primary' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Si este documento será primario, quitar el flag de los demás
            if ($this->is_primary) {
                $this->client->documents()->update(['is_primary' => false]);
            }

            // Crear el nuevo documento
            $this->client->documents()->create([
                'document_type_id' => $this->document_type_id,
                'document_number' => $this->document_number,
                'verification_digit' => $this->verification_digit,
                'expiration_date' => $this->expiration_date,
                'is_primary' => $this->is_primary,
            ]);

            DB::commit();
            session()->flash('message', 'Documento creado exitosamente.');
            return redirect()->route('clients.show', ['clientId' => $this->clientId]);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al crear el documento: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'document_number' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    $exists = ClientDocument::where('document_type_id', $this->document_type_id)
                        ->where('document_number', $value)
                        ->where('client_id', '!=', $this->clientId)
                        ->where('id', '!=', $this->documentId)
                        ->exists();

                    if ($exists) {
                        $fail('Este número de documento ya está registrado para otro cliente.');
                    }
                },
            ],
            'verification_digit' => [
                'nullable',
                'string',
                'max:2',
                function ($attribute, $value, $fail) {
                    if (
                        $this->selectedDocumentType &&
                        $this->selectedDocumentType->requires_verification_digit &&
                        empty($value)
                    ) {
                        $fail('El dígito verificador es requerido para este tipo de documento.');
                    }
                },
            ],
            'expiration_date' => 'nullable|date',
            'is_primary' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $document = ClientDocument::findOrFail($this->documentId);

            // Si este documento será primario, quitar el flag de los demás
            if ($this->is_primary && !$document->is_primary) {
                $this->client->documents()
                    ->where('id', '!=', $this->documentId)
                    ->update(['is_primary' => false]);
            }

            // Actualizar el documento
            $document->update([
                'document_type_id' => $this->document_type_id,
                'document_number' => $this->document_number,
                'verification_digit' => $this->verification_digit,
                'expiration_date' => $this->expiration_date,
                'is_primary' => $this->is_primary,
            ]);

            DB::commit();
            session()->flash('message', 'Documento actualizado exitosamente.');
            return redirect()->route('clients.show', ['clientId' => $this->clientId]);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al actualizar el documento: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('clients.show', ['clientId' => $this->clientId]);
    }

    public function render()
    {

        Log::info('ClientDocumentForm::render', [
            'clientId' => $this->clientId,
            'documentTypes' => DocumentType::count(),
            'hasClient' => isset($this->client)
        ]);

        return view('livewire.client-document-form', [
            'documentTypes' => DocumentType::orderBy('name')->get(),
            'breadcrumbs' => $this->getBaseBreadcrumbs()
        ])->layout('layouts.app');
    }
}
