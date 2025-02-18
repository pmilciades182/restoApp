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
                            <input type="text" wire:model.live.debounce.300ms="document_number"
                                wire:keydown.enter="searchClient"
                                class="rounded-l-md focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full border-gray-300 sm:text-sm"
                                placeholder="Buscar cliente">
                            <button wire:click="searchClient"
                                class="rounded-r-md px-3 py-2 bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                                Buscar
                            </button>
                        </div>
                        @if ($available_documents)
                            <ul class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg"
                                style="max-width:500px">
                                @foreach ($available_documents as $doc)
                                    <li wire:click="selectDocument({{ $doc['id'] }})"
                                        class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer">
                                        <span class="font-medium">{{ $doc['document_number'] }}</span> -
                                        {{ $doc['client_name'] }} ({{ $doc['document_type'] }})
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if ($client)
                            <div class="mt-2 p-2 bg-gray-100 rounded-md">
                                Cliente Seleccionado: <span class="font-medium">{{ $client->full_name }}</span>
                                ({{ $selected_document_type }} - {{ $document_number }})
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Mensajes de Error -->
            @if ($errors->any())
                <div class="mb-6">
                    <div class="bg-white border border-red-100 rounded-lg shadow-sm">
                        <!-- Encabezado del mensaje de error -->
                        <div class="px-4 py-3 border-b border-red-100 bg-red-50 rounded-t-lg flex items-center gap-3">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <h3 class="text-sm font-semibold text-red-800">
                                Se encontraron los siguientes errores
                            </h3>
                        </div>

                        <!-- Lista de errores -->
                        <div class="px-4 py-3">
                            <ul class="space-y-2">
                                @foreach ($errors->all() as $error)
                                    <li class="flex items-center gap-2 text-sm text-gray-600">
                                        <svg class="h-4 w-4 text-red-400 flex-shrink-0" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>


                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Productos</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                    <!-- Código de Barras -->
                    <div>
                        <label for="barcode" class="block text-sm font-medium text-gray-700">Código de Barras</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" id="barcode" wire:model.live="barcode"
                                wire:keydown.enter="addProductByBarcode"
                                class="rounded-l-md focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full border-gray-300 sm:text-sm"
                                placeholder="Ingrese código de barras">
                            <button wire:click="addProductByBarcode"
                                class="rounded-r-md px-3 py-2 bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                                Agregar
                            </button>
                        </div>
                        @error('barcode')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Búsqueda por nombre/código -->
                    <div style="position: relative;">
                        <label for="search_product" class="block text-sm font-medium text-gray-700">Buscar Producto</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" id="search_product" wire:model.live.debounce.300ms="search_product"
                                class="rounded-l-md focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full border-gray-300 sm:text-sm"
                                placeholder="Buscar por nombre o código">
                            <button wire:click="addProductBySearch"
                                class="rounded-r-md px-3 py-2 bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">
                                Buscar
                            </button>
                        </div>

                        <!-- Lista de productos siempre debajo del input con posición fija -->
                        @if ($available_products && count($available_products) > 0)
                            <div style="position: relative; width: 100%;">
                                <div style="position: absolute; top: 0; left: 0; width: 100%; z-index: 1000;">
                                    <ul style="max-height: 300px; overflow-y: auto; background: white; border: 1px solid #ccc; border-radius: 4px; margin-top: 4px; width: 100%; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                        @foreach ($available_products as $product)
                                            <li wire:click="selectProduct({{ $product['id'] }})"
                                                style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;"
                                                onmouseover="this.style.backgroundColor='#f3f4f6'"
                                                onmouseout="this.style.backgroundColor='transparent'">
                                                <div>
                                                    <span style="font-weight: 500;">{{ $product['name'] }}</span>
                                                    @if (isset($product['barcode']) && $product['barcode'])
                                                        <br><span style="font-size: 0.8em; color: #6b7280;">{{ $product['barcode'] }}</span>
                                                    @endif
                                                </div>
                                                <span style="font-size: 0.9em; color: #374151;">Gs. {{ number_format($product['price'], 0, ',', '.') }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Lista de productos agregados recientemente (opcional) -->
                <div class="mb-4">
                    @if (session()->has('message'))
                        <div class="rounded-md bg-green-50 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
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
                                            class="text-red-600 hover:text-red-900">Eliminar</button>
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


                        <div class="flex justify-between text-lg font-bold">
                            <span>Total:</span>
                            <span>Gs. {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>


                <!-- Sección de Pagos Múltiples -->
                <div class="mt-8 bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Formas de Pago</h3>
                        <button wire:click="addPaymentMethod"
                            class="px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
                            {{ $remainingBalance <= 0 ? 'disabled' : '' }}>
                            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Agregar Forma de Pago
                        </button>
                    </div>

                    <!-- Lista de Pagos -->
                    <div class="space-y-4">
                        @foreach ($paymentMethods as $index => $payment)
                            <div class="bg-white p-4 rounded-lg shadow">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Método de Pago -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Método de Pago</label>
                                        <select wire:model.live="paymentMethods.{{ $index }}.method"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="cash">Efectivo</option>
                                            <option value="credit_card">Tarjeta de Crédito</option>
                                            <option value="debit_card">Tarjeta de Débito</option>
                                            <option value="transfer">Transferencia</option>
                                        </select>
                                    </div>

                                    <!-- Monto -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ $payment['method'] === 'cash' ? 'Monto Recibido' : 'Monto a Pagar' }}
                                        </label>
                                        <input type="number"
                                            wire:model.live="paymentMethods.{{ $index }}.amount"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            min="0"
                                            max="{{ $payment['method'] === 'cash' ? '' : $remainingBalance }}">
                                    </div>

                                    <!-- Referencia (para pagos que no son en efectivo) -->
                                    @if ($payment['method'] !== 'cash')
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Número de
                                                Referencia</label>
                                            <input type="text"
                                                wire:model="paymentMethods.{{ $index }}.reference"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                    @endif

                                    <!-- Botón de Eliminar -->
                                    <div class="flex items-center">
                                        <button wire:click="removePaymentMethod({{ $index }})"
                                            class="text-red-600 hover:text-red-900">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Mostrar vuelto solo para efectivo -->
                                @if ($payment['method'] === 'cash' && $payment['amount'] > 0)
                                    <div class="mt-2 text-right text-sm">
                                        <span class="text-gray-600">Vuelto:</span>
                                        <span
                                            class="ml-2 font-medium {{ $this->getChange($index) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            Gs. {{ number_format($this->getChange($index), 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Resumen de Pagos -->
                    <div class="mt-6 bg-white p-4 rounded-lg shadow">
                        <h4 class="text-lg font-medium text-gray-900 mb-3">Resumen de Pagos</h4>
                        <div class="space-y-2">
                            <div class="grid grid-cols-2 gap-4">
                                <span class="text-gray-600">Total a Pagar:</span>
                                <span class="text-right font-medium">Gs.
                                    {{ number_format($total, 0, ',', '.') }}</span>

                                <span class="text-gray-600">Total Pagado:</span>
                                <span class="text-right font-medium">Gs.
                                    {{ number_format($this->totalPaid, 0, ',', '.') }}</span>

                                <span class="text-gray-600">Saldo Pendiente:</span>
                                <span
                                    class="text-right font-bold {{ $remainingBalance <= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Gs. {{ number_format($remainingBalance, 0, ',', '.') }}
                                </span>
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
        Livewire.on('productAdded', () => {
            const barcodeInput = document.getElementById('barcode');
            if (barcodeInput) {
                barcodeInput.value = ''; // Limpiar el campo
                barcodeInput.focus(); // Mantener el foco
            }
        });
    });
</script>
