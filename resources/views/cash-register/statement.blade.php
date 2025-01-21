<!-- resources/views/cash-register/statement.blade.php -->
<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Encabezado -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        Rendición de Caja #{{ $cashRegister->id }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Cajero: {{ $cashRegister->user->name }}
                    </p>
                </div>
                <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z" />
                    </svg>
                    Imprimir Rendición
                </button>
            </div>

            <div class="bg-white shadow-sm rounded-lg">
                <!-- Datos de la Caja -->
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <h3 class="text-xs font-medium text-gray-500">APERTURA</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $cashRegister->opened_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-xs font-medium text-gray-500">CIERRE</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $cashRegister->closed_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div>
                            <h3 class="text-xs font-medium text-gray-500">DURACIÓN</h3>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $cashRegister->opened_at->diffForHumans($cashRegister->closed_at, true) }}</p>
                        </div>
                        <div>
                            <h3 class="text-xs font-medium text-gray-500">TOTAL FACTURAS</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $invoices->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Balance de la Caja -->
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Balance de Caja</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Efectivo -->
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <h4 class="text-sm font-medium text-gray-500 mb-3">EFECTIVO</h4>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Monto Inicial:</dt>
                                    <dd class="text-sm text-gray-900">Gs.
                                        {{ number_format($balance['initial_cash'], 0, ',', '.') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Ventas en Efectivo:</dt>
                                    <dd class="text-sm text-gray-900">Gs.
                                        {{ number_format($paymentTotals->where('method', 'Efectivo')->first()['total'] ?? 0, 0, ',', '.') }}
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Monto Final:</dt>
                                    <dd class="text-sm text-gray-900">Gs.
                                        {{ number_format($balance['final_cash'], 0, ',', '.') }}</dd>
                                </div>
                                <div class="pt-2 border-t border-gray-200">
                                    <div class="flex justify-between font-medium">
                                        <dt class="text-sm text-gray-500">Diferencia:</dt>
                                        <dd
                                            class="text-sm {{ $balance['cash_difference'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            Gs. {{ number_format($balance['cash_difference'], 0, ',', '.') }}
                                        </dd>
                                    </div>
                                </div>
                            </dl>
                        </div>

                        <!-- Otros Medios de Pago -->
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <h4 class="text-sm font-medium text-gray-500 mb-3">OTROS MEDIOS DE PAGO</h4>
                            <dl class="space-y-2">
                                @foreach ($paymentTotals->where('method', '!=', 'Efectivo') as $payment)
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-500">{{ $payment['method'] }}:</dt>
                                        <dd class="text-sm text-gray-900">Gs.
                                            {{ number_format($payment['total'], 0, ',', '.') }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>

                        <!-- Total General -->
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <h4 class="text-sm font-medium text-gray-500 mb-3">TOTAL GENERAL</h4>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Total Ventas:</dt>
                                    <dd class="text-sm text-gray-900">Gs.
                                        {{ number_format($balance['total_sales'], 0, ',', '.') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Total Cobrado:</dt>
                                    <dd class="text-sm text-gray-900">Gs.
                                        {{ number_format($balance['total_collected'], 0, ',', '.') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Listado de Facturas -->
                <div class="px-4 py-5 sm:px-6" id="ffx">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detalle de Facturas</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                        Factura</th>
                                    <th
                                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                        Cliente</th>
                                    <th
                                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                        Monto</th>
                                    <th
                                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                        Forma de Pago</th>
                                    <th
                                        class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                        Hora</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $invoice->invoice_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $invoice->client->full_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            Gs. {{ number_format($invoice->total, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @foreach ($invoice->payments as $payment)
                                                <div>
                                                    {{ $payment->paymentMethod->name }}: Gs.
                                                    {{ number_format($payment->amount, 0, ',', '.') }}
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $invoice->created_at->format('H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Notas de Cierre -->
                @if ($cashRegister->closing_notes)
                    <div class="px-4 py-5 sm:px-6 border-t border-gray-200 bg-gray-50">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">NOTAS DE CIERRE</h3>
                        <p class="text-sm text-gray-900">{{ $cashRegister->closing_notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>



    <style>
        @media print {

            #ffx {
                display: none
            }

            /* Reset básico para impresión */
            * {
                visibility: hidden;
                margin: 0 !important;

                padding: 0 !important;
                box-sizing: border-box;
                font-size: 8pt;
            }

            /* Eliminar elementos del layout por defecto */
            header,
            footer,
            nav,
            x-app-layout>*:not(.py-6) {
                display: none !important;
            }

            /* Configuración general del documento */
            body {
                width: 70mm;
                margin-left: 10px;
                padding: 0 !important;

                line-height: 1.2;
                font-family: monospace;
            }

            /* Hacer visible solo el contenido principal */
            .max-w-7xl,
            .max-w-7xl * {
                visibility: visible;
                position: relative !important;
                overflow: visible !important;
            }

            /* Ajustes de contenedor principal */
            .max-w-7xl {
                width: 100% !important;
                max-width: none !important;
                padding: 0 2mm !important;
                margin: 0 !important;
            }

            .py-6 {
                padding: 0 !important;
            }

            /* Ocultar elementos innecesarios y detalle de facturas */
            button,
            nav,
            footer,
            .hidden-print,
            table,
            [x-data="{ open: false }"],
            h3:contains("Detalle de Facturas"),
            div:has(> table) {
                display: none !important;
            }

            /* Ajustes de encabezado */
            h2 {

                font-weight: bold;
                text-align: center;
                margin-bottom: 2mm !important;
            }

            /* Ajustes de texto */
            p {

                margin: 1mm 0 !important;
            }

            /* Eliminar sombras y bordes decorativos */
            .shadow-sm,
            .rounded-lg {
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            /* Ajustes de grid para formato lineal */
            .grid {
                display: block !important;
                margin-bottom: 2mm !important;
            }

            .grid>div {
                margin-bottom: 1mm !important;
            }

            /* Separadores */
            .border-t,
            .border-b {
                border: none !important;
                border-top: 1px dashed #000 !important;
                margin: 1mm 0 !important;
            }

            /* Ajustes para los totales */
            .bg-white {
                background: none !important;
                padding: 1mm 0 !important;
            }

            /* Formato para montos */
            .text-gray-900 {
                font-weight: bold;
            }

            /* Ajuste para notas de cierre */
            .bg-gray-50 {
                background: none !important;
                margin-top: 2mm !important;
                padding-top: 2mm !important;
                border-top: 1px dashed #000;
            }

            /* Eliminar márgenes y cabecera/pie por defecto */
            @page {
                margin: 0mm !important;
                padding: 0mm !important;
                size: 80mm auto;
            }

            @page :first {
                margin-top: 0mm !important;
            }

            @page :left {
                margin-left: 0mm !important;
            }

            @page :right {
                margin-right: 0mm !important;
            }
        }
    </style>
</x-app-layout>
