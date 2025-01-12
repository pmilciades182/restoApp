<!-- resources/views/livewire/client-table.blade.php -->
<div class="flex flex-col h-screen bg-gray-100">
    <!-- Barra superior fija con breadcrumb y controles -->
    <div class="bg-white shadow">
        <!-- Breadcrumb section -->
        <div class="border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
            </div>
        </div>

        <!-- Barra de búsqueda y acciones -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-center py-4 gap-4">
                <div class="flex items-center gap-4 w-full sm:w-auto">
                    <!-- Buscador -->
                    <div class="w-full sm:w-96">
                        <input wire:model.live="search"
                            type="search"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Buscar clientes...">
                    </div>
                    <!-- Filtros -->
                    <select wire:model.live="filterType"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Todos los tipos</option>
                        <option value="individual">Persona Física</option>
                        <option value="business">Empresa</option>
                    </select>
                    <select wire:model.live="filterStatus"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Todos los estados</option>
                        <option value="active">Activos</option>
                        <option value="inactive">Inactivos</option>
                    </select>
                </div>
                <div class="flex-none">
                    <a href="{{ route('clients.create') }}"
                        class="inline-flex justify-center items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Cliente
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Principal Scrolleable -->
    <div class="flex-1 overflow-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow">
                <!-- Mensajes de alerta -->
                @if (session()->has('message'))
                    <div class="p-4 border-b border-gray-200 bg-green-100 rounded-t-lg"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-green-700">{{ session('message') }}</span>
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="p-4 border-b border-gray-200 bg-red-50 rounded-t-lg"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-red-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-red-700">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Tabla -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nombre/Razón Social
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Documento
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Contacto
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($clients as $client)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($client->client_type === 'business')
                                                {{ $client->business_name }}
                                                @if($client->fantasy_name)
                                                    <div class="text-xs text-gray-500">{{ $client->fantasy_name }}</div>
                                                @endif
                                            @else
                                                {{ $client->first_name }} {{ $client->last_name }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($client->primaryDocument)
                                            <div class="text-sm text-gray-900">
                                                {{ $client->primaryDocument->documentType->name }}:
                                                {{ $client->primaryDocument->document_number }}
                                                @if($client->primaryDocument->verification_digit)
                                                    -{{ $client->primaryDocument->verification_digit }}
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">Sin documento</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            @if($client->email)
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ $client->email }}
                                                </div>
                                            @endif
                                            @if($client->phone)
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                    </svg>
                                                    {{ $client->phone }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $client->client_type === 'business' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $client->client_type === 'business' ? 'Empresa' : 'Individual' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button wire:click="toggleStatus({{ $client->id }})"
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $client->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $client->is_active ? 'Activo' : 'Inactivo' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <!-- Botón Ver -->
                                            <a href="{{ route('clients.show', ['clientId' => $client->id]) }}"
                                                class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-gray-100 hover:bg-blue-600 transition-all duration-200 ease-in-out group">
                                                <svg class="w-5 h-5 text-blue-600 group-hover:text-white transition-colors duration-200"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span class="ml-2 text-gray-700 group-hover:text-white transition-colors duration-200">
                                                    Ver
                                                </span>
                                            </a>

                                            <!-- Botón Editar -->
                                            <button wire:click="edit({{ $client->id }})"
                                                class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-gray-100 hover:bg-indigo-600 transition-all duration-200 ease-in-out group">
                                                <svg class="w-5 h-5 text-indigo-600 group-hover:text-white transition-colors duration-200"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span class="ml-2 text-gray-700 group-hover:text-white transition-colors duration-200">
                                                    Editar
                                                </span>
                                            </button>

                                            <!-- Botón Eliminar -->
                                            <button x-on:click="$dispatch('open-confirm-modal', {
                                                message: '¿Estás seguro que deseas eliminar este cliente? Esta acción no se puede deshacer.',
                                                callback: (confirmed) => {
                                                    if (confirmed) {
                                                        @this.deleteClient({{ $client->id }})
                                                    }
                                                }
                                            })"
                                                class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-gray-100 hover:bg-red-600 transition-all duration-200 ease-in-out group">
                                                <svg class="w-5 h-5 text-red-600 group-hover:text-white transition-colors duration-200"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span class="ml-2 text-gray-700 group-hover:text-white transition-colors duration-200">
                                                    Eliminar
                                                </span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $clients->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
