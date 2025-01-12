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
                    Detalles del Cliente
                </h2>
                <div class="flex items-center gap-4">
                    <a href="{{ route('clients.edit', $client->id) }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-sm font-semibold text-white rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar Cliente
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Principal Scrolleable -->
    <div class="flex-1 overflow-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <!-- Información del Cliente -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información General</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($client->client_type === 'business')
                            <div>
                                <p class="text-sm font-medium text-gray-500">Razón Social</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $client->business_name }}</p>
                            </div>
                            @if($client->fantasy_name)
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Nombre de Fantasía</p>
                                    <p class="mt-1 text-sm text-gray-900">{{ $client->fantasy_name }}</p>
                                </div>
                            @endif
                        @else
                            <div>
                                <p class="text-sm font-medium text-gray-500">Nombre</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $client->first_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Apellido</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $client->last_name }}</p>
                            </div>
                        @endif

                        @if($client->email)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Email</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $client->email }}</p>
                            </div>
                        @endif

                        @if($client->phone)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Teléfono</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $client->phone }}</p>
                            </div>
                        @endif

                        @if($client->mobile_phone)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Celular</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $client->mobile_phone }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Dirección -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dirección</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($client->address)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Dirección</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $client->address }}</p>
                            </div>
                        @endif

                        @if($client->city)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Ciudad</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $client->city }}</p>
                            </div>
                        @endif

                        @if($client->state)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Departamento</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $client->state }}</p>
                            </div>
                        @endif

                        @if($client->district)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Barrio</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $client->district }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Documentos -->
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Documentos</h3>
                        <a href="{{ route('client-documents.create', ['clientId' => $client->id]) }}"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                             <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                             </svg>
                             Agregar Documento
                         </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($documents as $document)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $document->documentType->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $document->document_number }}
                                            @if($document->verification_digit)
                                                <span class="text-gray-500">-{{ $document->verification_digit }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $document->expiration_date ? $document->expiration_date->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($document->is_primary)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Principal
                                                </span>
                                            @endif
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
</div>
