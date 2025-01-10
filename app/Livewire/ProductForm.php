<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;

class ProductForm extends Component
{
    public $name = '';
    public $barcode = '';
    public $description = '';
    public $price = '';
    public $cost = '';
    public $category_id = '';
    public $stock = 0;
    public $is_kitchen = false;
    public $productId;
    public $editMode = false;

    // Agregar método para guardar estado temporal
    private function saveTemporaryState()
    {
        session([
            'temp_product_form' => [
                'name' => $this->name,
                'barcode' => $this->barcode,
                'description' => $this->description,
                'price' => $this->price,
                'cost' => $this->cost,
                'category_id' => $this->category_id,
                'stock' => $this->stock,
                'is_kitchen' => $this->is_kitchen,
                'edit_mode' => $this->editMode,
                'product_id' => $this->productId
            ]
        ]);
    }

    public function loadProduct()
    {
        $product = Product::findOrFail($this->productId);
        $this->name = $product->name;
        $this->barcode = $product->barcode;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->cost = $product->cost;
        $this->category_id = $product->category_id;
        $this->stock = $product->stock;
        $this->is_kitchen = $product->is_kitchen;
    }

    // Agregar método para limpiar estado temporal
    private function clearTemporaryState()
    {
        session()->forget('temp_product_form');
    }

    // Modificar mount para cargar estado temporal si existe
    public function mount($productId = null)
    {
        // Primero verificamos si hay estado temporal
        if (session()->has('temp_product_form')) {
            $state = session('temp_product_form');
            $this->name = $state['name'];
            $this->barcode = $state['barcode'];
            $this->description = $state['description'];
            $this->price = $state['price'];
            $this->cost = $state['cost'];
            $this->category_id = $state['category_id'];
            $this->stock = $state['stock'];
            $this->is_kitchen = $state['is_kitchen'];
            $this->editMode = $state['edit_mode'];
            $this->productId = $state['product_id'];
        }
        // Si no hay estado temporal y hay productId, cargamos del producto
        elseif ($productId) {
            $this->productId = $productId;
            $this->editMode = true;
            $this->loadProduct();
        }
    }

    // Modificar los métodos de actualización de propiedades
    public function updatedName()
    {
        $this->saveTemporaryState();
    }
    public function updatedBarcode()
    {
        $this->saveTemporaryState();
    }
    public function updatedDescription()
    {
        $this->saveTemporaryState();
    }
    public function updatedPrice()
    {
        $this->saveTemporaryState();
    }
    public function updatedCost()
    {
        $this->saveTemporaryState();
    }
    public function updatedCategoryId()
    {
        $this->saveTemporaryState();
    }
    public function updatedStock()
    {
        $this->saveTemporaryState();
    }
    public function updatedIsKitchen()
    {
        $this->saveTemporaryState();
    }

    public function addCategory()
    {
        $this->saveTemporaryState();
        return redirect()->route('categories.create', ['redirect_to' => 'product']);
    }

    public function generateBarcode()
    {
        $barcode = Product::generateUniqueBarcode();
        $this->barcode = $barcode;
        $this->saveTemporaryState(); // Mantener el estado temporal actualizado
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|unique:products,barcode',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'is_kitchen' => 'boolean',
        ]);

        Product::create([
            'name' => $this->name,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'price' => $this->price,
            'cost' => $this->cost,
            'category_id' => $this->category_id,
            'stock' => $this->stock,
            'is_kitchen' => $this->is_kitchen,
        ]);

        $this->clearTemporaryState();
        session()->flash('message', 'Producto creado exitosamente.');
        return redirect()->route('products.index');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|unique:products,barcode,' . $this->productId,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'is_kitchen' => 'boolean',
        ]);

        Product::find($this->productId)->update([
            'name' => $this->name,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'price' => $this->price,
            'cost' => $this->cost,
            'category_id' => $this->category_id,
            'stock' => $this->stock,
            'is_kitchen' => $this->is_kitchen,
        ]);

        $this->clearTemporaryState();
        session()->flash('message', 'Producto actualizado exitosamente.');
        return redirect()->route('products.index');
    }

    public function cancel()
    {
        $this->clearTemporaryState();
        return redirect()->route('products.index');
    }

    public function render()
    {
        $breadcrumbs = [
            ['name' => 'Productos', 'route' => 'products.index'],
            ['name' => $this->editMode ? 'Editar Producto' : 'Nuevo Producto']
        ];

        return view('livewire.product-form', [
            'categories' => Category::all(),
            'breadcrumbs' => $breadcrumbs
        ])->layout('layouts.app');
    }
}
