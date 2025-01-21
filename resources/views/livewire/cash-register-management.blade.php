<div class="flex flex-col h-screen bg-gray-100">
    <!-- Barra superior fija con breadcrumb -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-b border-gray-200">
            <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="flex-1 overflow-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Mensajes de Alerta -->
            @if (session()->has('message'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Estado Actual de la Caja -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Estado de la Caja</h2>

                @if($current_register)
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Estado:</span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Abierta
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Abierta por:</span>
                            <span class="text-sm text-gray-900">{{ $current_register->user->name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Fecha de apertura:</span>
                            <span class="text-sm text-gray-900">{{ $current_register->opened_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Monto inicial:</span>
                            <span class="text-sm text-gray-900">Gs. {{ number_format($current_register->initial_cash, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">No hay una caja abierta actualmente.</p>
                @endif
            </div>

            <!-- Formulario de Apertura/Cierre -->
            <div class="bg-white rounded-lg shadow p-6">
                @if(!$current_register)
                    <!-- Formulario de Apertura -->
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Abrir Caja</h3>
                    <form wire:submit.prevent="openRegister" class="space-y-4">
                        <div>
                            <label for="initial_cash" class="block text-sm font-medium text-gray-700">
                                Monto Inicial (Gs.)
                            </label>
                            <div x-data="{
                                formatNumber(e) {
                                    const value = e.target.value.replace(/\D/g, '');
                                    @this.set('initial_cash', value);
                                    e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                }
                            }">
                                <input type="text"
                                    id="initial_cash"
                                    @input="formatNumber($event)"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="0">
                            </div>
                            @error('initial_cash')
                                <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Abrir Caja
                            </button>
                        </div>
                    </form>
                @else
                    <!-- Formulario de Cierre -->
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Cerrar Caja</h3>
                    <form wire:submit.prevent="closeRegister" class="space-y-4">
                        <div>
                            <label for="final_cash" class="block text-sm font-medium text-gray-700">
                                Monto Final (Gs.)
                            </label>
                            <div x-data="{
                                formatNumber(e) {
                                    const value = e.target.value.replace(/\D/g, '');
                                    @this.set('final_cash', value);
                                    e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                }
                            }">
                                <input type="text"
                                    id="final_cash"
                                    @input="formatNumber($event)"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="0">
                            </div>
                            @error('final_cash')
                                <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="closing_notes" class="block text-sm font-medium text-gray-700">
                                Notas de Cierre
                            </label>
                            <textarea id="closing_notes"
                                      wire:model="closing_notes"
                                      rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            @error('closing_notes')
                                <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Cerrar Caja
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
