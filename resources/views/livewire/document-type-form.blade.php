<!-- resources/views/livewire/document-type-form.blade.php -->
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
                    {{ $editMode ? 'Editar Tipo de Documento' : 'Nuevo Tipo de Documento' }}
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="{{ $editMode ? 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15' : 'M5 13l4 4L19 7' }}" />
                        </svg>
                        {{ $editMode ? 'Actualizar Tipo de Documento' : 'Guardar Tipo de Documento' }}
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
                            Nombre del Tipo de Documento
                        </label>
                        <input type="text"
                               id="name"
                               wire:model="name"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name')
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

                    <!-- Format Field -->
                    <div>
                        <label for="format" class="block text-sm font-medium text-gray-700">
                            Formato
                        </label>
                        <input type="text"
                               id="format"
                               wire:model="format"
                               placeholder="Ej: ####-###"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('format')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            Especifica el formato esperado del documento usando # para dígitos. Por ejemplo: ####-### para CI
                        </p>
                    </div>

                    <!-- Requires Verification Digit Field -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox"
                                   id="requires_verification_digit"
                                   wire:model="requires_verification_digit"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="ml-3">
                            <label for="requires_verification_digit" class="text-sm font-medium text-gray-700">
                                Requiere dígito verificador
                            </label>
                            <p class="text-sm text-gray-500">
                                Marque esta opción si el documento requiere un dígito verificador adicional (como RUC)
                            </p>
                        </div>
                    </div>
                    @error('requires_verification_digit')
                        <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </form>
            </div>
        </div>
    </div>
</div>
