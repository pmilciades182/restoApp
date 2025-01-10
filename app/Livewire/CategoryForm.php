<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;

class CategoryForm extends Component
{
    public $name = '';
    public $description = '';
    public $categoryId;
    public $editMode = false;

    public $redirect_to;

    public function mount($categoryId = null, $redirect_to = null)
    {
        $this->redirect_to = $redirect_to;

        if ($categoryId) {
            $this->categoryId = $categoryId;
            $this->editMode = true;
            $this->loadCategory();
        }
    }

    public function loadCategory()
    {
        $category = Category::findOrFail($this->categoryId);
        $this->name = $category->name;
        $this->description = $category->description;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Categoría creada exitosamente.');

        if ($this->redirect_to === 'product') {
            return redirect()->route('products.create');
        }

        return redirect()->route('categories.index');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Category::find($this->categoryId)->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Categoría actualizada exitosamente.');
        return redirect()->route('categories.index');
    }

    public function render()
    {
        $breadcrumbs = [
            ['name' => 'Categorías', 'route' => 'categories.index'],
            ['name' => $this->editMode ? 'Editar Categoría' : 'Nueva Categoría']
        ];

        return view('livewire.category-form', [
            'breadcrumbs' => $breadcrumbs
        ])->layout('layouts.app');
    }

    // Modificar el método cancel para manejar la redirección
    public function cancel()
    {
        if ($this->redirect_to === 'product') {
            return redirect()->route('products.create');
        }

        return redirect()->route('categories.index');
    }




}
