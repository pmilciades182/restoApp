<div class="flex flex-col h-screen bg-gray-100">
    <div class="bg-white shadow">
        <!-- Breadcrumb section -->
        <div class="border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
            </div>
        </div>

        <!-- Barra de búsqueda y acciones -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">
                <!-- Filtros -->
                <div class="flex items-center gap-4">
                    <!-- Rango de fechas -->
                    <input type="date"
                           wire:model.live="filterDateFrom"
                           class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <input type="date"
                           wire:model.live="filterDateTo"
                           class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Botón nueva factura -->
                <a href="{{ route('invoices.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Factura
                </a>
            </div>
        </div>
    </div>

    <!-- Contenido Principal Scrolleable -->
    <div class="flex-1 overflow-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow">
                <!-- Tabla -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Número/Cliente
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($invoices as $invoice)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $invoice->invoice_number }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $invoice->client->full_name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            Gs. {{ number_format($invoice->total, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span @class([
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                            'bg-green-100 text-green-800' => $invoice->status === 'paid',
                                            'bg-yellow-100 text-yellow-800' => $invoice->status === 'pending',
                                            'bg-red-100 text-red-800' => in_array($invoice->status, ['cancelled', 'void']),
                                        ])>
                                            {{ $statuses[$invoice->status] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $invoice->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('invoices.show', $invoice->id) }}"
                                                class="inline-flex items-center px-3 py-2 bg-indigo-100 text-indigo-700 hover:bg-indigo-200 rounded-md">
                                                Ver
                                            </a>
                                            @if($invoice->status === 'pending')
                                                <button wire:click="cancelInvoice({{ $invoice->id }})"
                                                    class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-md">
                                                    Cancelar
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
