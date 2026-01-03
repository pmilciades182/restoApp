<div class="flex flex-col h-screen bg-gray-100">
    <!-- Barra superior fija con breadcrumb -->
    <div class="bg-white shadow">
        <!-- Breadcrumb section -->
        <div class="border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
            </div>
        </div>

        <!-- Título y filtros -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-center py-4 gap-4">
                <!-- Rango de fechas -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Desde:</span>
                        <input type="date" wire:model.live="dateFrom"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Hasta:</span>
                        <input type="date" wire:model.live="dateTo"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Filtro de cliente y botones de exportar -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full">
                    <div class="w-full sm:w-64">
                        <select wire:model.live="clientId"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos los clientes</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Botones de exportar -->
                    <div class="flex gap-2 sm:ml-auto">
                        <button wire:click="export" type="button"
                            style="background-color: #16a34a; color: white;"
                            class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-25 transition shadow-sm"
                            onmouseover="this.style.backgroundColor='#15803d'"
                            onmouseout="this.style.backgroundColor='#16a34a'">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Exportar CSV
                        </button>
                        <button wire:click="exportTxt" type="button"
                            style="background-color: #2563eb; color: white;"
                            class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-25 transition shadow-sm"
                            onmouseover="this.style.backgroundColor='#1d4ed8'"
                            onmouseout="this.style.backgroundColor='#2563eb'">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Exportar TXT
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Principal Scrolleable -->
    <div class="flex-1 overflow-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Tarjetas de resumen -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Total vendido -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-indigo-500 p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Vendido
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        Gs. {{ number_format($this->totalAmount, 0, ',', '.') }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Total unidades -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-md bg-green-500 p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Unidades
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ number_format($this->totalQuantity, 0, ',', '.') }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de ventas por cliente -->
            <div class="bg-white rounded-lg shadow">
                <div class="overflow-x-auto">
                    @forelse ($this->clientSales as $clientName => $clientSales)
                        <div class="border-b border-gray-200 last:border-b-0">
                            <!-- Header del cliente -->
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $clientName }}
                                </h3>
                                @php
                                    $clientTotal = 0;
                                    $clientQuantity = 0;
                                    foreach ($clientSales as $dateSales) {
                                        $clientTotal += $dateSales->sum('total_amount');
                                        $clientQuantity += $dateSales->sum('total_quantity');
                                    }
                                @endphp
                                <div class="mt-2 text-sm text-gray-600">
                                    <span class="font-medium">Total:</span> Gs.
                                    {{ number_format($clientTotal, 0, ',', '.') }}
                                    <span class="ml-4 font-medium">Unidades:</span>
                                    {{ number_format($clientQuantity, 0, ',', '.') }}
                                </div>
                            </div>

                            <!-- Datos por fecha -->
                            @foreach ($clientSales as $date => $dateSales)
                                <div class="px-6 py-3 bg-gray-100 border-b border-gray-200">
                                    <h4 class="text-sm font-semibold text-gray-700">
                                        {{ date('d/m/Y', strtotime($date)) }}
                                    </h4>
                                    @php
                                        $dateTotal = $dateSales->sum('total_amount');
                                        $dateQuantity = $dateSales->sum('total_quantity');
                                    @endphp
                                    <div class="mt-1 text-xs text-gray-600">
                                        <span class="font-medium">Subtotal:</span> Gs.
                                        {{ number_format($dateTotal, 0, ',', '.') }}
                                        <span class="ml-3 font-medium">Unidades:</span>
                                        {{ number_format($dateQuantity, 0, ',', '.') }}
                                    </div>
                                </div>

                                <!-- Tabla de productos -->
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Producto
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Cantidad
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Total
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($dateSales as $sale)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-3 text-sm text-gray-900">
                                                    {{ $sale->product_name }}
                                                </td>
                                                <td class="px-6 py-3 text-sm text-gray-900">
                                                    {{ number_format($sale->total_quantity, 0, ',', '.') }}
                                                </td>
                                                <td class="px-6 py-3 text-sm text-gray-900">
                                                    Gs. {{ number_format($sale->total_amount, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-sm text-gray-500">
                            No hay datos disponibles para el período seleccionado.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
