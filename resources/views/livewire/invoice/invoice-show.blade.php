<!-- resources/views/livewire/invoice/invoice-show.blade.php -->
<div>
    <div class="flex flex-col h-screen bg-gray-100" id="normal">
        <!-- Barra superior fija con breadcrumb -->
        <div class="bg-white shadow">
            <div class="border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
                </div>
            </div>

            <!-- Título y botones -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <h2 class="text-2xl font-bold text-gray-800">
                        Factura {{ $invoice->invoice_number }}
                    </h2>

                    <div class="flex items-center gap-4">
                        @if ($invoice->status === 'paid')
                            <button type="button" wire:click="printInvoice"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Imprimir
                            </button>
                        @endif

                        @if ($invoice->status === 'cancelled' || $invoice->status === 'paid')
                            <a href="{{ route('invoices.create') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Nueva Factura
                            </a>
                        @endif

                        @if ($this->canBeCancelled)
                            <button type="button" wire:click="cancelInvoice"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancelar Factura
                            </button>
                        @endif

                        @if ($this->canBeMarkedAsPaid)
                            <button type="button" wire:click="markAsPaid"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Marcar como Pagada
                            </button>
                        @endif
                    </div>


                </div>
            </div>
        </div>

        <!-- Contenido Principal Scrolleable -->
        <div class="flex-1 overflow-auto py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <!-- Información del Encabezado -->
                    <div class="border-b border-gray-200 bg-white px-4 py-5 sm:px-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Detalles de la Factura
                                </h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                    Emitida el {{ $invoice->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $this->statusBadgeClasses }}">
                                {{ $this->statusText }}
                            </span>
                        </div>
                    </div>

                    <!-- Información del Cliente -->
                    <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                        <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Cliente</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $client->client_type === 'business' ? $client->business_name : $client->full_name }}
                                </dd>
                                @if ($client->fantasy_name)
                                    <dd class="mt-1 text-sm text-gray-500">{{ $client->fantasy_name }}</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Documento</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if ($client->primaryDocument)
                                        {{ $client->primaryDocument->documentType->name }}:
                                        {{ $client->primaryDocument->document_number }}
                                        @if ($client->primaryDocument->verification_digit)
                                            -{{ $client->primaryDocument->verification_digit }}
                                        @endif
                                    @else
                                        No especificado
                                    @endif
                                </dd>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Productos -->
                    <div class="px-4 py-5 sm:px-6">
                        <div class="flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                    <div class="overflow-hidden border rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Producto
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Cantidad
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Precio Unit.
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Subtotal
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($details as $detail)
                                                    <tr>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            {{ $detail->product->name }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $detail->quantity }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            Gs. {{ number_format($detail->unit_price, 0, ',', '.') }}
                                                        </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                                            Gs. {{ number_format($detail->subtotal, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Totales -->
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        <div class="flex justify-end">
                            <div class="w-64 space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium text-gray-500">Subtotal:</span>
                                    <span class="text-gray-900">Gs.
                                        {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium text-gray-500">IVA (10%):</span>
                                    <span class="text-gray-900">Gs.
                                        {{ number_format($invoice->tax, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-base pt-3 border-t border-gray-200">
                                    <span class="font-bold text-gray-900">Total:</span>
                                    <span class="font-bold text-gray-900">Gs.
                                        {{ number_format($invoice->total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6 bg-gray-50">
                        <div class="text-xs text-gray-500">
                            <p>Creado por: {{ $invoice->creator->name ?? 'Sistema' }} -
                                {{ $invoice->created_at->format('d/m/Y H:i') }}</p>
                            @if ($invoice->updated_by)
                                <p class="mt-1">Última actualización por: {{ $invoice->updater->name }} -
                                    {{ $invoice->updated_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Template para impresión de ticket (agregar al final del archivo) -->
    <div id="ticket-print" class="oculto">
        <div class="ticket text-xs" style="width: 80mm; font-family: monospace;">
            <!-- Header -->
            <div class="text-center mb-2">
                <div class="font-bold">FARMACIA EJEMPLO S.A.</div>
                <div>RUC: 80099999-1</div>
                <div>Tel: (021) 999-999</div>
            </div>

            <!-- Datos de la factura -->
            <div class="mb-2">
                <div>Ticket #: {{ $invoice->invoice_number }}</div>
                <div>Fecha: {{ $invoice->created_at->format('d/m/Y H:i') }}</div>
                <div>Cliente: {{ Str::limit($client->full_name, 30) }}</div>
                <div>RUC: {{ $client->primaryDocument ? $client->primaryDocument->document_number : '--' }}</div>
            </div>

            <!-- Línea divisoria -->
            <div>--------------------------------</div>

            <!-- Productos -->
            <div class="mb-2">
                @foreach ($details as $detail)
                    <div>
                        {{ Str::limit($detail->product->name, 20) }}
                        <div class="flex justify-between">
                            <span>{{ $detail->quantity }} x
                                {{ number_format($detail->unit_price, 0, ',', '.') }}</span>
                            <span>{{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Línea divisoria -->
            <div>--------------------------------</div>

            <!-- Totales -->
            <div class="mb-2">
                <div class="flex justify-between">
                    <span>SUBTOTAL:</span>
                    <span>{{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>IVA (10%):</span>
                    <span>{{ number_format($invoice->tax, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold">
                    <span>TOTAL:</span>
                    <span>{{ number_format($invoice->total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-2">
                <div>¡Gracias por su preferencia!</div>
                <div>www.farmaciaejemplo.com.py</div>
                <div>Ticket no válido como factura</div>
            </div>
        </div>
    </div>

    <style>
        /* Estilos normales */


        #ticket-print.hidden {
            display: none !important;
        }

        .oculto{
                display: none;
            }

        @media print {

            /* Debug con outline */
            #ticket-print {
                outline: 2px solid red;
                /* Para debug visual */
                display: block !important;
                visibility: visible !important;
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm;
                padding: 0mm;
                z-index: 9999;
            }

            #normal {
                display: none;
            }

            #super_nav {
                display: none;
            }

            /* Asegurar que el contenido del ticket sea visible */
            #ticket-print * {
                visibility: visible !important;
                display: block !important;
            }

            /* Ajustes para el contenido del ticket */
            .ticket {
                width: 80mm !important;
                padding: 5mm !important;
                font-family: monospace !important;
                font-size: 12px !important;
                line-height: 1.2 !important;
            }

            /* Remover márgenes de título y hora */
            .ticket-title,
            .ticket-time {
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>

</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            console.log('Livewire initialized');

            Livewire.on('print-ticket', () => {
                console.log('Print ticket event received');

                const ticketElement = document.querySelector('#ticket-print');
                console.log('Ticket element:', ticketElement);

                if (ticketElement) {
                    // console.log('Before removing hidden class');
                    //console.log('Current classes:', ticketElement.classList);
                    //ticketElement.classList.remove('hidden');
                    //console.log('After removing hidden class');
                    //console.log('Updated classes:', ticketElement.classList);

                    // Verificar visibilidad
                    console.log('Ticket visibility:', window.getComputedStyle(ticketElement).visibility);
                    console.log('Ticket display:', window.getComputedStyle(ticketElement).display);

                    setTimeout(() => {
                        console.log('Starting print process');
                        window.print();

                        console.log('Print process completed');
                        console.log('Before adding hidden class');
                        //ticketElement.classList.add('hidden');
                        console.log('After adding hidden class');
                        console.log('Final classes:', ticketElement.classList);
                    }, 100);
                } else {
                    console.error('Ticket element not found');
                }
            });
        });

        // Listener para el evento de impresión
        /*window.onbeforeprint = function() {
            console.log('Before print event triggered');
            const ticketElement = document.querySelector('#ticket-print');
            console.log('Ticket element visibility before print:', window.getComputedStyle(ticketElement).visibility);
        };*/

        /*window.onafterprint = function() {
            console.log('After print event triggered');
            const ticketElement = document.querySelector('#ticket-print');
            console.log('Ticket element visibility after print:', window.getComputedStyle(ticketElement).visibility);
        };*/
    </script>
@endpush
