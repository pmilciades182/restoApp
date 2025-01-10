<!-- resources/views/livewire/product-form.blade.php -->
<div>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ $editMode ? 'Editar Producto' : 'Nuevo Producto' }}
                    </h2>
                </div>

                <form class="space-y-6">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Producto
                        </label>
                        <input type="text" id="name" wire:model="name"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Barcode Field -->
                    <div>
                        <label for="barcode" class="block text-sm font-medium text-gray-700 mb-2">
                            Código de Barras
                        </label>
                        <div class="flex gap-2">
                            <input type="text" id="barcode" wire:model="barcode"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">


                            <!-- Barcode Field Button -->
                            <button type="button"
                            wire:click="generateBarcode"
                            wire:loading.attr="disabled"
                            wire:target="generateBarcode"
                            class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-300 shadow-sm text-sm font-semibold rounded-lg text-gray-700 transition-all duration-200
                                hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                                disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3zm0 5h16M7 4v16m3-16v16m3-16v16m3-16v16" />
                                </svg>
                                Generar
                            </button>


                            </button>
                        </div>
                        @error('barcode')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description Field -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción
                        </label>
                        <textarea id="description" wire:model="description" rows="3"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        @error('description')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Category Field en product-form.blade.php -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Categoría
                        </label>
                        <div class="flex gap-2">
                            <select id="category_id" wire:model="category_id"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Seleccionar categoría</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>

                            <!-- Category Add Button -->
                            <button type="button" wire:click="addCategory"
                                class="inline-flex items-center px-4 py-2 bg-gray-50 border border-gray-300 shadow-sm text-sm font-semibold rounded-lg text-gray-700 transition-all duration-200
    hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Nueva Categoría
                            </button>


                        </div>
                        @error('category_id')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Price and Cost Fields (Grid) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Price Field -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                Precio de Venta (Gs.)
                            </label>
                            <input type="number" id="price" wire:model="price"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('price')
                                <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Cost Field -->
                        <div>
                            <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">
                                Costo (Gs.)
                            </label>
                            <input type="number" id="cost" wire:model="cost"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('cost')
                                <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Stock Field -->
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                            Stock
                        </label>
                        <input type="number" id="stock" wire:model="stock"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('stock')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Is Kitchen Field -->
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="is_kitchen" wire:model="is_kitchen"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <label for="is_kitchen" class="text-sm font-medium text-gray-700">
                            ¿Producto para cocina?
                        </label>
                        @error('is_kitchen')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Action Buttons con diseño moderno -->
                    <div class="flex justify-end items-center gap-4 mt-10 pt-6 border-t border-gray-100">
                        <button type="button" wire:click="cancel"
                            class="inline-flex items-center justify-center rounded-lg px-6 py-3 text-sm font-semibold text-gray-700 transition-all duration-200
            hover:text-gray-900 hover:scale-[1.02] focus:outline-none">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancelar
                        </button>
                        <button type="button" wire:click="{{ $editMode ? 'update' : 'store' }}"
                            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all duration-200
            hover:bg-indigo-500 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
            active:scale-[0.98]">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $editMode ? 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15' : 'M5 13l4 4L19 7' }}" />
                            </svg>
                            {{ $editMode ? 'Actualizar Producto' : 'Guardar Producto' }}
                        </button>
                    </div>


                </form>
            </div>
        </div>
    </div>
</div>
