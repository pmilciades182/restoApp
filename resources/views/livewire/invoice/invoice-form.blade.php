<!-- resources/views/livewire/invoice-form.blade.php -->
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
                <button type="button" wire:click="cancel" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="button" wire:click="{{ $editMode ? 'update' : 'store' }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700">
                    {{ $editMode ? 'Actualizar Factura' : 'Guardar Factura' }}
                </button>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-auto p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto space-y-4">
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Información del Cliente</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="document_number" class="block text-sm font-medium text-gray-700">Número de Documento</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" wire:model.live.debounce.300ms="document_number" wire:keydown.enter="searchClient"
                                   class="rounded-l-md focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full border-gray-300 sm:text-sm" placeholder="Buscar cliente">
                            <button wire:click="searchClient" class="rounded-r-md px-3 py-2 bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                                Buscar
                            </button>
                        </div>
                        @if ($available_documents)
                            <ul class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg" style="max-width:500px">
                                @foreach ($available_documents as $doc)
                                    <li wire:click="selectDocument({{ $doc['id'] }})" class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer">
                                        <span class="font-medium">{{ $doc['document_number'] }}</span> - {{ $doc['client_name'] }} ({{ $doc['document_type'] }})
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if ($client)
                            <div class="mt-2 p-2 bg-gray-100 rounded-md">
                                Cliente Seleccionado: <span class="font-medium">{{ $client->full_name }}</span> ({{ $selected_document_type }} - {{ $document_number }})
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Productos</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <div>
                        <label for="barcode" class="block text-sm font-medium text-gray-700">Código de Barras</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" id="barcode" wire:model="barcode" wire:keydown.enter="addProductByBarcode"
                                   class="rounded-l-md focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full border-gray-300 sm:text-sm" placeholder="Ingrese código de barras">
                            <button wire:click="addProductByBarcode" class="rounded-r-md px-3 py-2 bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                                Agregar
                            </button>
                        </div>
                        @error('barcode') <span class="error">{{ $message }}</span> @enderror
                    </div>

                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($items as $index => $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item['product_name'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" wire:model.live.debounce.500ms="items.{{ $index }}.quantity"
                                               wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                               class="w-24 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" min="1">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">Gs. {{ number_format($item['price'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Gs. {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button wire:click="removeItem({{ $index }})" class="text-red-600 hover:text-red-900">Eliminar</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">No hay productos agregados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-end">
                    <div>
                        <div class="flex justify-between">
                            <span class="font-bold">Subtotal:</span>
                            <span>Gs. {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-bold">IVA (10%):</span>
                            <span>Gs. {{ number_format($tax, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total:</span>
                            <span>Gs. {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('productAdded', () => {
            const barcodeInput = document.getElementById('barcode');
            if (barcodeInput) {
                barcodeInput.value = ''; // Limpiar el campo
                barcodeInput.focus();    // Mantener el foco
            }
        });
    });
</script>
