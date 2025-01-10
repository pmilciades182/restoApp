<!-- resources/views/livewire/category-form.blade.php -->
<div>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ $editMode ? 'Editar Categoría' : 'Nueva Categoría' }}
                    </h2>
                </div>

                <form class="space-y-6">
                    <!-- Category Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de la Categoría
                        </label>
                        <input type="text" id="name" wire:model="name"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description Field -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción
                        </label>
                        <textarea id="description" wire:model="description" rows="4"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        @error('description')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Action Buttons con diseño moderno y alineación horizontal -->
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
                            {{ $editMode ? 'Actualizar Categoría' : 'Guardar Categoría' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
