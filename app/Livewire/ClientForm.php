<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Client;
use App\Models\DocumentType;
use App\Traits\HasBreadcrumbs;
use Illuminate\Support\Facades\DB;

class ClientForm extends Component
{
    use HasBreadcrumbs;

    // Información general
    public $business_name = '';
    public $first_name = '';
    public $last_name = '';
    public $fantasy_name = '';
    public $client_type = 'individual';

    // Información de contacto
    public $email = '';
    public $phone = '';
    public $mobile_phone = '';

    // Dirección
    public $address = '';
    public $city = '';
    public $state = '';
    public $district = '';
    public $country = 'Paraguay';
    public $postal_code = '';

    // Información adicional
    public $notes = '';
    public $is_active = true;

    // Documento principal
    public $document_type_id = '';
    public $document_number = '';
    public $verification_digit = '';

    // Control
    public $clientId;
    public $editMode = false;
    public $parent_breadcrumbs;

    public function mount($clientId = null, $parent_breadcrumbs = null)
    {
        $this->parent_breadcrumbs = $parent_breadcrumbs;

        if ($clientId) {
            $this->clientId = $clientId;
            $this->editMode = true;
            $this->loadClient();
        }
    }

    protected function loadClient()
    {
        $client = Client::with('documents.documentType')->findOrFail($this->clientId);

        // Cargar información general
        $this->business_name = $client->business_name;
        $this->first_name = $client->first_name;
        $this->last_name = $client->last_name;
        $this->fantasy_name = $client->fantasy_name;
        $this->client_type = $client->client_type;

        // Cargar información de contacto
        $this->email = $client->email;
        $this->phone = $client->phone;
        $this->mobile_phone = $client->mobile_phone;

        // Cargar dirección
        $this->address = $client->address;
        $this->city = $client->city;
        $this->state = $client->state;
        $this->district = $client->district;
        $this->country = $client->country;
        $this->postal_code = $client->postal_code;

        // Cargar información adicional
        $this->notes = $client->notes;
        $this->is_active = $client->is_active;

        // Cargar documento principal
        if ($primaryDoc = $client->primaryDocument) {
            $this->document_type_id = $primaryDoc->document_type_id;
            $this->document_number = $primaryDoc->document_number;
            $this->verification_digit = $primaryDoc->verification_digit;
        }
    }

    protected function getBaseBreadcrumbs()
    {
        $parentBreadcrumbs = $this->decodeBreadcrumbs($this->parent_breadcrumbs);

        $baseBreadcrumbs = [
            ['name' => 'Clientes', 'route' => 'clients.index'],
            ['name' => $this->editMode ? 'Editar Cliente' : 'Nuevo Cliente']
        ];

        return $parentBreadcrumbs ? array_merge($parentBreadcrumbs, $baseBreadcrumbs) : $baseBreadcrumbs;
    }

    public function addDocumentType()
    {
        $breadcrumbs = [
            ['name' => 'Clientes', 'route' => 'clients.index'],
            ['name' => $this->editMode ? 'Editar Cliente' : 'Nuevo Cliente']
        ];

        $encodedBreadcrumbs = base64_encode(json_encode($breadcrumbs));

        return response()->redirectToRoute('document-types.create', [
            'redirect_to' => 'client',
            'parent_breadcrumbs' => $encodedBreadcrumbs
        ]);
    }

    public function store()
    {
        $validatedData = $this->validate([
            'client_type' => 'required|in:individual,business',
            'business_name' => 'required_if:client_type,business|nullable|string|max:255',
            'first_name' => 'required_if:client_type,individual|nullable|string|max:255',
            'last_name' => 'required_if:client_type,individual|nullable|string|max:255',
            'fantasy_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'document_type_id' => 'required|exists:document_types,id',
            'document_number' => 'required|string|max:50',
            'verification_digit' => 'nullable|string|max:2',
        ]);

        try {
            DB::beginTransaction();

            $client = Client::create([
                'business_name' => $this->business_name,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'fantasy_name' => $this->fantasy_name,
                'client_type' => $this->client_type,
                'email' => $this->email,
                'phone' => $this->phone,
                'mobile_phone' => $this->mobile_phone,
                'address' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'district' => $this->district,
                'country' => $this->country,
                'postal_code' => $this->postal_code,
                'notes' => $this->notes,
                'is_active' => $this->is_active,
            ]);

            // Crear documento principal
            $client->documents()->create([
                'document_type_id' => $this->document_type_id,
                'document_number' => $this->document_number,
                'verification_digit' => $this->verification_digit,
                'is_primary' => true,
            ]);

            DB::commit();
            session()->flash('message', 'Cliente creado exitosamente.');
            return redirect()->route('clients.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al crear el cliente: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $validatedData = $this->validate([
            'client_type' => 'required|in:individual,business',
            'business_name' => 'required_if:client_type,business|nullable|string|max:255',
            'first_name' => 'required_if:client_type,individual|nullable|string|max:255',
            'last_name' => 'required_if:client_type,individual|nullable|string|max:255',
            'fantasy_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'mobile_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'document_type_id' => 'required|exists:document_types,id',
            'document_number' => 'required|string|max:50',
            'verification_digit' => 'nullable|string|max:2',
        ]);

        try {
            DB::beginTransaction();

            $client = Client::findOrFail($this->clientId);
            $client->update([
                'business_name' => $this->business_name,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'fantasy_name' => $this->fantasy_name,
                'client_type' => $this->client_type,
                'email' => $this->email,
                'phone' => $this->phone,
                'mobile_phone' => $this->mobile_phone,
                'address' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'district' => $this->district,
                'country' => $this->country,
                'postal_code' => $this->postal_code,
                'notes' => $this->notes,
                'is_active' => $this->is_active,
            ]);

            // Actualizar o crear documento principal
            $client->documents()->updateOrCreate(
                ['is_primary' => true],
                [
                    'document_type_id' => $this->document_type_id,
                    'document_number' => $this->document_number,
                    'verification_digit' => $this->verification_digit,
                    'is_primary' => true,
                ]
            );

            DB::commit();
            session()->flash('message', 'Cliente actualizado exitosamente.');
            return redirect()->route('clients.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al actualizar el cliente: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('clients.index');
    }

    public function render()
    {
        return view('livewire.client-form', [
            'documentTypes' => DocumentType::orderBy('name')->get(),
            'breadcrumbs' => $this->getBaseBreadcrumbs()
        ])->layout('layouts.app');
    }
}
