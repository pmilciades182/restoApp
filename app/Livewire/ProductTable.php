<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

class ProductTable extends Component
{
    use WithPagination;

    public $search = '';

    public function deleteProduct($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            session()->flash('message', 'Producto eliminado exitosamente.');
        }
    }

    public function edit($id)
    {
        return redirect()->route('products.edit', ['productId' => $id]);
    }

    public function render()
    {
        $breadcrumbs = [
            ['name' => 'Productos']
        ];

        $products = Product::with('category')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('barcode', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.product-table', [
            'products' => $products,
            'breadcrumbs' => $breadcrumbs
        ])->layout('layouts.app');
    }
}
