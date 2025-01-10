<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;
use App\Models\ClientDocument;
use App\Traits\HasBreadcrumbs;
use Illuminate\Support\Facades\DB;

class ClientDocumentTable extends Component
{
    use WithPagination, HasBreadcrumbs;

    public $clientId;
    public $client;
    public $search = '';

    protected $listeners = [
        'refreshComponent' => '$refresh'
    ];

    public function mount($clientId)
    {
        $this->clientId = $clientId;
        $this->client = Client::findOrFail($clientId);
    }

    public function edit($id)
    {
        return redirect()->route('client-documents.edit', [
            'clientId' => $this->clientId,
            'documentId' => $id
        ]);
    }

    public function deleteDocument($id)
    {
        try {
            DB::beginTransaction();

            $document = ClientDocument::findOrFail($id);

            // Verificar si es el único documento del cliente
            $documentsCount = $this->client->documents()->count();
            if ($documentsCount <= 1) {
                session()->flash('error', 'No se puede eliminar el documento porque el cliente debe tener al menos un documento.');
                return;
            }

            // Si el documento a eliminar es el principal, establecer otro como principal
            if ($document->is_primary) {
                $newPrimaryDoc = $this->client->documents()
                    ->where('id', '!=', $id)
                    ->first();

                if ($newPrimaryDoc) {
                    $newPrimaryDoc->update(['is_primary' => true]);
                }
            }

            $document->delete();

            DB::commit();
            session()->flash('message', 'Documento eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al eliminar el documento: ' . $e->getMessage());
        }
    }

    public function setPrimaryDocument($id)
    {
        try {
            DB::beginTransaction();

            // Quitar el flag de principal de todos los documentos del cliente
            $this->client->documents()->update(['is_primary' => false]);

            // Establecer el nuevo documento principal
            ClientDocument::where('id', $id)->update(['is_primary' => true]);

            DB::commit();
            session()->flash('message', 'Documento principal actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al actualizar el documento principal: ' . $e->getMessage());
        }
    }

    protected function getBaseBreadcrumbs()
    {
        return [
            ['name' => 'Clientes', 'route' => 'clients.index'],
            ['name' => $this->client->full_name, 'route' => 'clients.show', 'params' => ['clientId' => $this->client->id]],
            ['name' => 'Documentos']
        ];
    }

    public function render()
    {
        $documents = $this->client->documents()
            ->with('documentType')
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('document_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('documentType', function ($query) {
                            $query->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.client-document-table', [
            'documents' => $documents,
            'breadcrumbs' => $this->getBaseBreadcrumbs()
        ])->layout('layouts.app');
    }
}
