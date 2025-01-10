<!-- resources/views/livewire/product-form.blade.php -->
<div class="flex flex-col h-screen bg-gray-100">
    <!-- Barra superior fija con breadcrumb -->
    <div class="bg-white shadow">
        <!-- Breadcrumb section -->
        <div class="border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
            </div>
        </div>

        <!-- Título y botones -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ $editMode ? 'Editar Producto' : 'Nuevo Producto' }}
                </h2>
                <div class="flex items-center gap-4">
                    <button type="button" wire:click="cancel"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-gray-700 hover:text-gray-900 focus:outline-none">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancelar
                    </button>
                    <button type="button" wire:click="{{ $editMode ? 'update' : 'store' }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-sm font-semibold text-white rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $editMode ? 'Actualizar Producto' : 'Guardar Producto' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Principal Scrolleable -->
    <div class="flex-1 overflow-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <form class="space-y-6">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Nombre del Producto
                        </label>
                        <input type="text" id="name" wire:model="name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Barcode Field -->
                    <div>
                        <label for="barcode" class="block text-sm font-medium text-gray-700">
                            Código de Barras
                        </label>
                        <div class="mt-1 flex gap-2">
                            <input type="text"
                                   id="barcode"
                                   wire:model="barcode"
                                   wire:key="barcode-field-{{ now() }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                            <button type="button"
                                    wire:click="generateBarcode"
                                    wire:loading.attr="disabled"
                                    wire:target="generateBarcode"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <span wire:loading.remove wire:target="generateBarcode">Generar</span>
                                <span wire:loading wire:target="generateBarcode">Generando...</span>
                            </button>
                        </div>
                        @error('barcode')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description Field -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Descripción
                        </label>
                        <textarea id="description"
                                 wire:model="description"
                                 rows="3"
                                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        @error('description')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Category Field -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">
                            Categoría
                        </label>
                        <div class="mt-1 flex gap-2">
                            <select id="category_id"
                                    wire:model="category_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Seleccionar categoría</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>

                            <button type="button"
                                    wire:click="addCategory"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Nueva Categoría
                            </button>
                        </div>
                        @error('category_id')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Price and Cost Fields Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Price Field -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">
                                Precio de Venta (Gs.)
                            </label>
                            <input type="number"
                                   id="price"
                                   wire:model="price"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('price')
                                <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Cost Field -->
                        <div>
                            <label for="cost" class="block text-sm font-medium text-gray-700">
                                Costo (Gs.)
                            </label>
                            <input type="number"
                                   id="cost"
                                   wire:model="cost"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('cost')
                                <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Stock Field -->
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700">
                            Stock
                        </label>
                        <input type="number"
                               id="stock"
                               wire:model="stock"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('stock')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Is Kitchen Field -->
                    <div class="flex items-center">
                        <input type="checkbox"
                               id="is_kitchen"
                               wire:model="is_kitchen"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <label for="is_kitchen" class="ml-2 block text-sm text-gray-700">
                            ¿Producto para cocina?
                        </label>
                    </div>
                    @error('is_kitchen')
                        <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </form>
            </div>
        </div>
    </div>
</div>
