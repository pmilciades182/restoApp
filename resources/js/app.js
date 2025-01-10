import './bootstrap';
import Alpine from 'alpinejs';
import Focus from '@alpinejs/focus'

// Registrar el plugin Focus
Alpine.plugin(Focus)

// Hacer Alpine disponible globalmente
window.Alpine = Alpine

// Iniciar Alpine
Alpine.start()
