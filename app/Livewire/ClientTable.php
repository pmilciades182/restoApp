<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;

class ClientTable extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = '';
    public $filterStatus = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        return redirect()->route('clients.edit', ['clientId' => $id]);
    }

    public function deleteClient($id)
    {
        try {
            $client = Client::findOrFail($id);

            // Eliminar el cliente (soft delete)
            $client->delete();

            session()->flash('message', 'Cliente eliminado exitosamente.');

        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->update(['is_active' => !$client->is_active]);

            $status = $client->is_active ? 'activado' : 'desactivado';
            session()->flash('message', "Cliente {$status} exitosamente.");

        } catch (\Exception $e) {
            session()->flash('error', 'Error al cambiar el estado del cliente.');
        }
    }

    public function render()
    {
        $query = Client::with(['documents.documentType'])
            ->where(function ($query) {
                $query->where('business_name', 'like', '%' . $this->search . '%')
                    ->orWhere('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('fantasy_name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('documents', function ($query) {
                        $query->where('document_number', 'like', '%' . $this->search . '%');
                    });
            });

        // Aplicar filtro por tipo de cliente
        if ($this->filterType) {
            $query->where('client_type', $this->filterType);
        }

        // Aplicar filtro por estado
        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus === 'active');
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.client-table', [
            'clients' => $clients,
            'breadcrumbs' => [['name' => 'Clientes']]
        ])->layout('layouts.app');
    }

    public function getClientDisplayName($client)
    {
        if ($client->client_type === 'business') {
            return $client->business_name;
        }
        return trim("{$client->first_name} {$client->last_name}");
    }

    public function getPrimaryDocument($client)
    {
        return $client->documents()
            ->where('is_primary', true)
            ->with('documentType')
            ->first();
    }
}
