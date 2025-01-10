<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DocumentType;
use App\Traits\HasBreadcrumbs;

class DocumentTypeForm extends Component
{
    use HasBreadcrumbs;

    public $name = '';
    public $description = '';
    public $format = '';
    public $requires_verification_digit = false;
    public $documentTypeId;
    public $editMode = false;
    public $redirect_to;
    public $parent_breadcrumbs;

    public function mount($documentTypeId = null, $redirect_to = null, $parent_breadcrumbs = null)
    {
        $this->redirect_to = $redirect_to;
        $this->parent_breadcrumbs = $parent_breadcrumbs;

        if ($documentTypeId) {
            $this->documentTypeId = $documentTypeId;
            $this->editMode = true;
            $this->loadDocumentType();
        }
    }

    public function loadDocumentType()
    {
        $documentType = DocumentType::findOrFail($this->documentTypeId);
        $this->name = $documentType->name;
        $this->description = $documentType->description;
        $this->format = $documentType->format;
        $this->requires_verification_digit = $documentType->requires_verification_digit;
    }

    protected function getBaseBreadcrumbs()
    {
        $parentBreadcrumbs = $this->decodeBreadcrumbs($this->parent_breadcrumbs);

        $baseBreadcrumbs = [
            ['name' => 'Tipos de Documento', 'route' => 'document-types.index'],
            ['name' => $this->editMode ? 'Editar Tipo de Documento' : 'Nuevo Tipo de Documento']
        ];

        return $parentBreadcrumbs ? array_merge($parentBreadcrumbs, $baseBreadcrumbs) : $baseBreadcrumbs;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:document_types,name',
            'description' => 'nullable|string',
            'format' => 'nullable|string|max:255',
            'requires_verification_digit' => 'boolean',
        ]);

        DocumentType::create([
            'name' => $this->name,
            'description' => $this->description,
            'format' => $this->format,
            'requires_verification_digit' => $this->requires_verification_digit,
        ]);

        session()->flash('message', 'Tipo de documento creado exitosamente.');

        if ($this->redirect_to === 'client') {
            return redirect()->route('clients.create');
        }

        return redirect()->route('document-types.index');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:document_types,name,' . $this->documentTypeId,
            'description' => 'nullable|string',
            'format' => 'nullable|string|max:255',
            'requires_verification_digit' => 'boolean',
        ]);

        DocumentType::find($this->documentTypeId)->update([
            'name' => $this->name,
            'description' => $this->description,
            'format' => $this->format,
            'requires_verification_digit' => $this->requires_verification_digit,
        ]);

        session()->flash('message', 'Tipo de documento actualizado exitosamente.');
        return redirect()->route('document-types.index');
    }

    public function cancel()
    {
        if ($this->redirect_to === 'client') {
            return redirect()->route('clients.create');
        }

        return redirect()->route('document-types.index');
    }

    public function render()
    {
        return view('livewire.document-type-form', [
            'breadcrumbs' => $this->getBaseBreadcrumbs()
        ])->layout('layouts.app');
    }
}
