<!-- resources/views/livewire/client-form.blade.php -->
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
                    {{ $editMode ? 'Editar Cliente' : 'Nuevo Cliente' }}
                </h2>
                <div class="flex items-center gap-4">
                    <button type="button" wire:click="cancel"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-gray-700 hover:text-gray-900 focus:outline-none">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancelar
                    </button>
                    <button type="button" wire:click="{{ $editMode ? 'update' : 'store' }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-sm font-semibold text-white rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $editMode ? 'Actualizar Cliente' : 'Guardar Cliente' }}
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
                    <!-- Tipo de Cliente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Tipo de Cliente
                        </label>
                        <div class="mt-2 space-x-6">
                            <label class="inline-flex items-center">
                                <input type="radio"
                                       wire:model="client_type"
                                       name="client_type"
                                       value="individual"
                                       class="form-radio">
                                <span class="ml-2">Persona Física</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio"
                                       wire:model="client_type"
                                       name="client_type"
                                       value="business"
                                       class="form-radio">
                                <span class="ml-2">Empresa</span>
                            </label>
                        </div>
                        @error('client_type')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Campos para Empresa -->
                    @if ($client_type === 'business')
                        <div>
                            <label for="business_name" class="block text-sm font-medium text-gray-700">
                                Razón Social
                            </label>
                            <input type="text" id="business_name" wire:model="business_name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('business_name')
                                <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <!-- Campos para Persona Física -->
                    @if ($client_type === 'individual')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">
                                    Nombre
                                </label>
                                <input type="text" id="first_name" wire:model="first_name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('first_name')
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">
                                    Apellido
                                </label>
                                <input type="text" id="last_name" wire:model="last_name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('last_name')
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Nombre de Fantasía (opcional para ambos tipos) -->
                    <div>
                        <label for="fantasy_name" class="block text-sm font-medium text-gray-700">
                            Nombre de Fantasía (opcional)
                        </label>
                        <input type="text" id="fantasy_name" wire:model="fantasy_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('fantasy_name')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Información de Documento -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Documento Principal</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                                <select id="document_type_id" wire:model.live="document_type_id"
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

                            <div>
                                <label for="document_number" class="block text-sm font-medium text-gray-700">
                                    Número de Documento
                                </label>
                                <input type="text" id="document_number" wire:model="document_number"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('document_number')
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            @if ($selectedDocumentType && $selectedDocumentType->requires_verification_digit)
                                <div>
                                    <label for="verification_digit" class="block text-sm font-medium text-gray-700">
                                        Dígito Verificador
                                    </label>
                                    <input type="text" id="verification_digit" wire:model="verification_digit"
                                        maxlength="1"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('verification_digit')
                                        <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Información de Contacto -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Información de Contacto</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Correo Electrónico
                                </label>
                                <input type="email" id="email" wire:model="email"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('email')
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">
                                    Teléfono
                                </label>
                                <input type="text" id="phone" wire:model="phone"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('phone')
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="mobile_phone" class="block text-sm font-medium text-gray-700">
                                    Celular
                                </label>
                                <input type="text" id="mobile_phone" wire:model="mobile_phone"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('mobile_phone')
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Dirección</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700">
                                    Dirección
                                </label>
                                <input type="text" id="address" wire:model="address"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('address')
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="district" class="block text-sm font-medium text-gray-700">
                                    Barrio
                                </label>
                                <input type="text" id="district" wire:model="district"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('district')
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">
                                    Ciudad
                                </label>
                                <input type="text" id="city" wire:model="city"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('city')
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700">
                                    Departamento
                                </label>
                                <input type="text" id="state" wire:model="state"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('state')
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700">
                                    Código Postal
                                </label>
                                <input type="text" id="postal_code" wire:model="postal_code"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('postal_code')
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700">
                                    País
                                </label>
                                <input type="text" id="country" wire:model="country" value="Paraguay"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50"
                                    readonly>
                                @error('country')
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Notas -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">
                            Notas
                        </label>
                        <textarea id="notes" wire:model="notes" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        @error('notes')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" wire:model="is_active"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">
                            Cliente activo
                        </label>
                        @error('is_active')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
