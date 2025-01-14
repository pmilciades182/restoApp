<div class="flex flex-col h-screen bg-gray-100">
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-b border-gray-200">
            <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">
                {{ $editMode ? 'Editar Factura' : 'Nueva Factura' }}
            </h2>
            <div class="flex items-center gap-4">
                <button type="button" wire:click="cancel"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="button" wire:click="{{ $editMode ? 'update' : 'store' }}"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700">
                    {{ $editMode ? 'Actualizar Factura' : 'Guardar Factura' }}
                </button>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-auto p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto space-y-4">
            <!-- Sección de Cliente -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-medium text-gray-900">Información del Cliente</h3>
                    <button wire:click="addClient"
                        class="px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Cliente
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="document_number" class="block text-sm font-medium text-gray-700">Número de
                            Documento</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" id="document_number" wire:model.live.debounce.300ms="document_number"
                                wire:keydown.enter="searchClient"
                                class="rounded-l-md focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full border-gray-300 sm:text-sm"
                                placeholder="Buscar cliente">
                            <button wire:click="searchClient"
                                class="rounded-r-md px-3 py-2 bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                        @error('document_number')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror

                        @if ($available_documents)
                            <ul class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-auto"
                                style="max-width:500px">
                                @foreach ($available_documents as $doc)
                                    <li wire:click="selectDocument({{ $doc['id'] }})"
                                        class="px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer">
                                        <span class="font-medium">{{ $doc['document_number'] }}</span>
                                        <div class="text-gray-600">{{ $doc['client_name'] }}
                                            ({{ $doc['document_type'] }})</div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    @if ($client)
                        <div class="col-span-2 p-4 bg-gray-50 rounded-md">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900">Cliente Seleccionado</h4>
                                    <p class="mt-1 text-sm text-gray-600">{{ $client->full_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $selected_document_type }} -
                                        {{ $document_number }}</p>
                                </div>
                                @if ($client->is_active === false)
                                    <span class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">
                                        Cliente Inactivo
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sección de Productos -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Productos</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <div>
                        <label for="barcode" class="block text-sm font-medium text-gray-700">Código de Barras</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" id="barcode" wire:model="barcode"
                                wire:keydown.enter="addProductByBarcode" x-data x-init="$el.focus()"
                                @focus-barcode.window="$el.focus()"
                                class="rounded-l-md focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full border-gray-300 sm:text-sm"
                                placeholder="Ingrese código de barras">
                            <button wire:click="addProductByBarcode"
                                class="rounded-r-md px-3 py-2 bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                        @error('barcode')
                            <span class="mt-1 text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Producto</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cantidad</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Precio Unit.</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subtotal</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($items as $index => $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item['product_name'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number"
                                            wire:model.live.debounce.500ms="items.{{ $index }}.quantity"
                                            wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                            class="w-24 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            min="1">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">Gs.
                                        {{ number_format($item['price'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Gs.
                                        {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button wire:click="removeItem({{ $index }})"
                                            class="text-red-600 hover:text-red-900">
                                            <svg class="inline-block w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No hay productos agregados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-end">
                    <div class="w-64 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-700">Subtotal:</span>
                            <span class="text-gray-900">Gs. {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-700">IVA (10%):</span>
                            <span class="text-gray-900">Gs. {{ number_format($tax, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between text-lg font-bold">
                                <span class="text-gray-900">Total:</span>
                                <span class="text-gray-900">Gs. {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Mantener el foco en el input de código de barras
        Livewire.on('productAdded', () => {
            const barcodeInput = document.getElementById('barcode');
            if (barcodeInput) {
                barcodeInput.value = '';
                barcodeInput.focus();
            }
        });

        // Eventos para manejo de cliente
        Livewire.on('clientAdded', clientId => {
            @this.handleClientAdded(clientId);
        });
    });

    // Mantener el foco en el input de código de barras cuando se presiona Enter
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.id !== 'barcode') {
            e.preventDefault();
            document.getElementById('barcode').focus();
        }
    });
</script>
