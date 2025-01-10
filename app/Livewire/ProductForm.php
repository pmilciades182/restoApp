<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Traits\HasBreadcrumbs;
use Illuminate\Support\Facades\Log;

class ProductForm extends Component
{
    use HasBreadcrumbs;

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
    public $parent_breadcrumbs;

    // Agregar método para guardar estado temporal
    private function saveTemporaryState()
    {
        Log::info('Guardando estado temporal', [
            'current_state' => [
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
    private function loadProduct()
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

    private function clearTemporaryState()
    {
        session()->forget('temp_product_form');
    }

    public function mount($productId = null, $parent_breadcrumbs = null)
    {
        /*Log::info('ProductForm::mount', [
            'productId' => $productId,
            'parent_breadcrumbs' => $parent_breadcrumbs
        ]);*/

        $this->parent_breadcrumbs = $parent_breadcrumbs;

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

    // Event handlers para actualizar el estado temporal
    public function updated($propertyName)
    {
        $this->saveTemporaryState();
    }

    public function generateBarcode()
    {
        Log::info('Iniciando generateBarcode en ProductForm');
        try {
            Log::info('Intentando generar código de barras');
            $barcode = Product::generateUniqueBarcode();
            Log::info('Código de barras generado:', ['barcode' => $barcode]);

            // Asignación directa de la propiedad
            $this->barcode = $barcode;

            Log::info('Barcode asignado al componente:', ['this->barcode' => $this->barcode]);

            // Forzar actualización
            $this->dispatch('refresh-input');
        } catch (\Exception $e) {
            Log::error('Error al generar código de barras:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Error al generar el código de barras.');
        }
    }

    protected function getBaseBreadcrumbs()
    {
        $parentBreadcrumbs = $this->decodeBreadcrumbs($this->parent_breadcrumbs);

        $baseBreadcrumbs = [
            ['name' => 'Productos', 'route' => 'products.index'],
            ['name' => $this->editMode ? 'Editar Producto' : 'Nuevo Producto']
        ];

        return $parentBreadcrumbs ? array_merge($parentBreadcrumbs, $baseBreadcrumbs) : $baseBreadcrumbs;
    }

    public function addCategory()
    {
        $this->saveTemporaryState();

        $breadcrumbs = [
            ['name' => 'Productos', 'route' => 'products.index'],
            ['name' => $this->editMode ? 'Editar Producto' : 'Nuevo Producto']
        ];

        $encodedBreadcrumbs = base64_encode(json_encode($breadcrumbs));

        /*Log::info('ProductForm::addCategory - Redirecting to categories.create', [
            'breadcrumbs' => $breadcrumbs,
            'encoded' => $encodedBreadcrumbs,
            'redirect_to' => 'product'
        ]);*/

        // Usar response()->redirectToRoute() para asegurar que los parámetros se pasen correctamente
        return response()->redirectToRoute('categories.create', [
            'redirect_to' => 'product',
            'parent_breadcrumbs' => $encodedBreadcrumbs
        ]);
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

        $barcode = $this->barcode ? strval($this->barcode) : null;

        Product::create([
            'name' => $this->name,
            'barcode' => $barcode,
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

        $barcode = $this->barcode ? strval($this->barcode) : null;

        Product::find($this->productId)->update([
            'name' => $this->name,
            'barcode' => $barcode,
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
        $breadcrumbs = $this->getBaseBreadcrumbs();

        /*Log::info('ProductForm::render', [
            'breadcrumbs' => $breadcrumbs,
            'parent_breadcrumbs' => $this->parent_breadcrumbs
        ]);*/

        return view('livewire.product-form', [
            'categories' => Category::all(),
            'breadcrumbs' => $breadcrumbs
        ])->layout('layouts.app');
    }
}
