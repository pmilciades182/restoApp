<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DocumentType;

class DocumentTypeTable extends Component
{
    use WithPagination;

    public $search = '';

    public function edit($id)
    {
        return redirect()->route('document-types.edit', ['documentTypeId' => $id]);
    }

    public function deleteDocumentType($id)
    {
        try {
            $documentType = DocumentType::findOrFail($id);

            // Verificar si el tipo de documento está siendo usado
            if ($documentType->clientDocuments()->exists()) {
                session()->flash('error', 'No se puede eliminar el tipo de documento porque está siendo utilizado por uno o más clientes.');
                return;
            }

            $documentType->delete();
            session()->flash('message', 'Tipo de documento eliminado exitosamente.');

        } catch (\Exception $e) {
            session()->flash('error', 'Ha ocurrido un error al intentar eliminar el tipo de documento.');
        }
    }

    public function render()
    {
        return view('livewire.document-type-table', [
            'documentTypes' => DocumentType::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            'breadcrumbs' => [['name' => 'Tipos de Documento']]
        ])->layout('layouts.app');
    }
}
