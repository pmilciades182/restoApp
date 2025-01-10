<!-- resources/views/components/confirmation-modal.blade.php -->
<div x-data="{ show: false, message: '', callback: null }"
     x-on:open-confirm-modal.window="show = true; message = $event.detail.message; callback = $event.detail.callback"
     x-cloak
     x-show="show"
     x-trap.noscroll="show"
     class="fixed inset-0 z-[100] overflow-y-auto"
     role="dialog"
     aria-modal="true">

    <!-- Backdrop mejorado con blur más intenso -->
    <div x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/60 backdrop-blur-[6px]"></div>

    <!-- Container centrado con animación de aparición -->
    <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <!-- Modal Panel con sombra y borde brillante -->
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 @click.stop
                 class="relative transform bg-white rounded-xl shadow-2xl ring-1 ring-black/5 w-[450px]
                        animate-bounce-small">

                <!-- Contenido del Modal -->
                <div class="p-8">
                    <!-- Icono con animación de pulso -->
                    <div class="mx-auto w-14 h-14 flex items-center justify-center rounded-full bg-red-100
                                ring-4 ring-red-50 animate-pulse">
                        <svg class="h-7 w-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 9v3.75m9.344 5.569l-1.456-1.456m1.456-13.432l-1.456 1.456M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>

                    <!-- Texto con mejor contraste -->
                    <div class="mt-6 mb-8 text-center">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            Confirmar Acción
                        </h3>
                        <p class="text-base text-gray-600" x-text="message"></p>
                    </div>

                    <!-- Botones con mejor contraste y feedback -->
                    <div class="flex gap-3">
                        <button type="button"
                            @click="show = false"
                            class="flex-1 px-5 py-3 text-sm font-semibold bg-white border-2 border-gray-200
                                   rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200
                                   transition-all duration-200 hover:shadow-md">
                            Cancelar
                        </button>
                        <button type="button"
                            @click="callback(true); show = false"
                            class="flex-1 px-5 py-3 text-sm font-semibold text-white bg-red-500
                                   rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2
                                   focus:ring-red-500 transition-all duration-200 hover:shadow-md
                                   transform hover:scale-[1.02] active:scale-[0.98]">
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
    @keyframes bounce-small {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    .animate-bounce-small {
        animation: bounce-small 2s infinite;
    }
</style>
