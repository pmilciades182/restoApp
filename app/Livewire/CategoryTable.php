<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;

class CategoryTable extends Component
{
    use WithPagination;

    public $search = '';

    // Añade este método
    public function edit($id)
    {
        return redirect()->route('categories.edit', ['categoryId' => $id]);
    }

    public function deleteCategory($id)
    {
        try {
            $category = Category::findOrFail($id);

            // Verificar si la categoría tiene productos asociados
            if ($category->products()->exists()) {
                session()->flash('error', 'No se puede eliminar la categoría porque tiene productos asociados. Por favor, elimine o reasigne los productos primero.');
                return;
            }

            $category->delete();
            session()->flash('message', 'Categoría eliminada exitosamente.');

        } catch (\Exception $e) {
            session()->flash('error', 'No se puede eliminar la categoría porque está siendo utilizada en el sistema.');
        }
    }

    public function render()
    {
        return view('livewire.category-table', [
            'categories' => Category::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            'breadcrumbs' => [['name' => 'Categorías']]
        ])->layout('layouts.app');
    }
}
