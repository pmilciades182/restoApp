<div>
    <div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-8 sm:px-20 bg-white">
                <div class="text-center max-w-xl mx-auto">
                    <!-- Icono de advertencia - Ligeramente más grande -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-6">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <!-- Título con mejor espaciado -->
                    <h3 class="text-xl leading-6 font-medium text-gray-900 mb-6">
                        No hay una caja abierta
                    </h3>

                    <!-- Mensaje con mejor legibilidad -->
                    <p class="text-base text-gray-500 mb-8">
                        Para crear una factura, primero debe abrir una caja. Por favor, diríjase al módulo de administración de caja para abrir una nueva caja.
                    </p>

                    <!-- Botones con mejor espaciado -->
                    <div class="mt-8 flex justify-center space-x-6">
                        <a href="{{ route('cash-register.management') }}" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                         style="margin:10px">
                            ABRIR CAJA
                        </a>

                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        style="margin:10px"
                        >
                            SALIR
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
