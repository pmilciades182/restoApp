<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;
use App\Traits\HasBreadcrumbs;

class ClientShow extends Component
{
    use HasBreadcrumbs;

    public $client;
    public $documents;
    public $clientId; // Añadir esta propiedad

    public function mount($clientId)
    {
        $this->clientId = $clientId; // Guardar el ID
        $this->client = Client::with(['documents.documentType'])->findOrFail($clientId);
        $this->documents = $this->client->documents;
    }

    protected function getBaseBreadcrumbs()
    {
        return [
            ['name' => 'Clientes', 'route' => 'clients.index'],
            ['name' => $this->client->client_type === 'business' ? $this->client->business_name : "{$this->client->first_name} {$this->client->last_name}"]
        ];
    }

    public function render()
    {
        return view('livewire.client-show', [
            'breadcrumbs' => $this->getBaseBreadcrumbs()
        ])->layout('layouts.app');
    }
}
