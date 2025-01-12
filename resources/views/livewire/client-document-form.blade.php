<!-- resources/views/livewire/client-document-form.blade.php -->
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
                    {{ $editMode ? 'Editar Documento' : 'Nuevo Documento' }}
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
                        {{ $editMode ? 'Actualizar Documento' : 'Guardar Documento' }}
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
                    <!-- Cliente -->
                    <div>
                        <p class="text-sm font-medium text-gray-700">Cliente</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $client->client_type === 'business' ? $client->business_name : "{$client->first_name} {$client->last_name}" }}</p>
                    </div>

                    <!-- Tipo de Documento -->
                    <div>
                        <div class="flex justify-between items-center">
                            <label for="document_type_id" class="block text-sm font-medium text-gray-700">
                                Tipo de Documento
                            </label>
                            <button type="button" wire:click="addDocumentType"
                                class="text-sm text-indigo-600 hover:text-indigo-900">
                                Agregar nuevo
                            </button>
                        </div>
                        <select id="document_type_id"
                                wire:model.live="document_type_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccionar tipo</option>
                            @foreach ($documentTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('document_type_id')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Grid para Número de Documento y Dígito Verificador -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Número de Documento -->
                        <div>
                            <label for="document_number" class="block text-sm font-medium text-gray-700">
                                Número de Documento
                            </label>
                            <input type="text"
                                   id="document_number"
                                   wire:model="document_number"
                                   placeholder="{{ $selectedDocumentType?->format }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('document_number')
                                <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Dígito Verificador (solo si es requerido) -->
                        @if ($selectedDocumentType && $selectedDocumentType->requires_verification_digit)
                            <div>
                                <label for="verification_digit" class="block text-sm font-medium text-gray-700">
                                    Dígito Verificador
                                </label>
                                <input type="text"
                                       id="verification_digit"
                                       wire:model="verification_digit"
                                       maxlength="1"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('verification_digit')
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <!-- Fecha de Vencimiento -->
                    <div>
                        <label for="expiration_date" class="block text-sm font-medium text-gray-700">
                            Fecha de Vencimiento
                        </label>
                        <input type="date"
                               id="expiration_date"
                               wire:model="expiration_date"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('expiration_date')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Documento Principal -->
                    <div class="flex items-center">
                        <input type="checkbox"
                               id="is_primary"
                               wire:model="is_primary"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <label for="is_primary" class="ml-2 block text-sm text-gray-700">
                            Establecer como documento principal
                        </label>
                    </div>
                    @error('is_primary')
                        <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                    @enderror
                </form>
            </div>
        </div>
    </div>
</div>
