<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Traits\HasBreadcrumbs;
use Illuminate\Support\Facades\Log;

class CategoryForm extends Component
{
    use HasBreadcrumbs;

    public $name = '';
    public $description = '';
    public $categoryId;
    public $editMode = false;
    public $redirect_to;
    public $parent_breadcrumbs;
    public $decodedBreadcrumbs = null;
    public function mount($categoryId = null, $redirect_to = null, $parent_breadcrumbs = null)
    {
        /*Log::info('CategoryForm::mount - Raw params', [
            'categoryId' => $categoryId,
            'redirect_to' => $redirect_to,
            'parent_breadcrumbs' => $parent_breadcrumbs,
            'request_all' => request()->all()  // Agregar esto para ver todos los parámetros
        ]);*/

        $this->redirect_to = $redirect_to;

        // Intentar obtener parent_breadcrumbs de diferentes formas
        $this->parent_breadcrumbs = $parent_breadcrumbs ?? request()->get('parent_breadcrumbs');

        if ($this->parent_breadcrumbs) {
            try {
                $decoded = base64_decode($this->parent_breadcrumbs);
                $breadcrumbs = json_decode($decoded, true);

                Log::info('CategoryForm::mount - Decoding breadcrumbs', [
                    'decoded_string' => $decoded,
                    'decoded_breadcrumbs' => $breadcrumbs,
                    'json_error' => json_last_error(),
                    'json_error_msg' => json_last_error_msg()
                ]);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->decodedBreadcrumbs = $breadcrumbs;
                }
            } catch (\Exception $e) {
                Log::error('CategoryForm::mount - Error decoding breadcrumbs', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        if ($categoryId) {
            $this->categoryId = $categoryId;
            $this->editMode = true;
            $this->loadCategory();
        }
    }

    public function getBaseBreadcrumbs()
    {
        /*Log::info('CategoryForm::getBaseBreadcrumbs', [
            'redirect_to' => $this->redirect_to,
            'decodedBreadcrumbs' => $this->decodedBreadcrumbs,
            'parent_breadcrumbs' => $this->parent_breadcrumbs
        ]);*/

        if ($this->redirect_to === 'product' && $this->parent_breadcrumbs) {
            try {
                $decoded = base64_decode($this->parent_breadcrumbs);
                $parentBreadcrumbs = json_decode($decoded, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $mergedBreadcrumbs = array_merge($parentBreadcrumbs, [
                        ['name' => 'Nueva Categoría']
                    ]);

                    /*Log::info('CategoryForm::getBaseBreadcrumbs - Using merged breadcrumbs', [
                        'merged' => $mergedBreadcrumbs
                    ]);*/

                    return $mergedBreadcrumbs;
                }
            } catch (\Exception $e) {
                Log::error('CategoryForm::getBaseBreadcrumbs - Error processing breadcrumbs', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        $defaultBreadcrumbs = [
            ['name' => 'Categorías', 'route' => 'categories.index'],
            ['name' => $this->editMode ? 'Editar Categoría' : 'Nueva Categoría']
        ];

        /*Log::info('CategoryForm::getBaseBreadcrumbs - Using default breadcrumbs', [
            'default' => $defaultBreadcrumbs
        ]);*/

        return $defaultBreadcrumbs;
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

        Category::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Categoría creada exitosamente.');

        /*Log::info('CategoryForm::store - Before redirect', [
            'redirect_to' => $this->redirect_to,
            'parent_breadcrumbs' => $this->parent_breadcrumbs
        ]);*/

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

    public function cancel()
    {

        /*Log::info('CategoryForm::cancel - Before redirect', [
            'redirect_to' => $this->redirect_to,
            'parent_breadcrumbs' => $this->parent_breadcrumbs
        ]);*/

        if ($this->redirect_to === 'product') {
            return redirect()->route('products.create');
        }

        return redirect()->route('categories.index');
    }
    public function render()
    {
        $breadcrumbs = $this->getBaseBreadcrumbs();

        /*Log::info('CategoryForm::render', [
            'breadcrumbs' => $breadcrumbs,
            'redirect_to' => $this->redirect_to,
            'parent_breadcrumbs' => $this->parent_breadcrumbs
        ]);*/

        return view('livewire.category-form', [
            'breadcrumbs' => $breadcrumbs
        ])->layout('layouts.app');
    }
}
