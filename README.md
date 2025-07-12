# RestoApp - Sistema de Gestión para Restaurantes

Sistema completo de gestión para restaurantes desarrollado con Laravel 10, que incluye punto de venta (POS), gestión de inventario, facturación, reportes y más.

## 🚀 Características Principales

- **Punto de Venta (POS)** - Interfaz intuitiva para tomar órdenes
- **Gestión de Inventario** - Control de productos y categorías
- **Facturación Electrónica** - Generación de facturas y tickets
- **Gestión de Clientes** - Base de datos de clientes con documentos
- **Control de Caja** - Apertura/cierre de cajas registradoras
- **Reportes de Ventas** - Análisis diario, mensual y por productos
- **Gestión de Mesas** - Control de mesas y meseros
- **Pantalla de Cocina** - Display para preparación de órdenes

## 🛠️ Tecnologías

- **Backend**: Laravel 10 + Jetstream
- **Frontend**: Livewire 3 + Alpine.js + Tailwind CSS
- **Base de Datos**: MySQL 8.0
- **Caché**: Redis
- **Containerización**: Docker + Docker Compose

## 📋 Requisitos Previos

- Docker y Docker Compose instalados
- Git
- Puertos disponibles: 8080 (app), 3307 (MySQL), 6380 (Redis), 1026 (mail), 8026 (mail dashboard)

## 🚀 Instalación Rápida con Docker

### 1. Clonar el repositorio
```bash
git clone <repository-url>
cd restoApp
```

### 2. Configurar el entorno
```bash
# Copiar archivo de configuración
cp .env.example .env

# Opcional: Editar .env si necesitas cambiar puertos o credenciales
nano .env
```

### 3. Levantar los servicios
```bash
# Construir e iniciar contenedores
docker-compose up -d --build

# Verificar que todos los servicios estén ejecutándose
docker-compose ps
```

### 4. Configurar la aplicación Laravel
```bash
# Generar clave de aplicación
docker-compose exec app php artisan key:generate

# Instalar dependencias de Composer
docker-compose exec app composer install

# Instalar dependencias de Node.js
docker-compose exec app npm install

# Ejecutar migraciones
docker-compose exec app php artisan migrate

# Opcional: Ejecutar seeders para datos de prueba
docker-compose exec app php artisan db:seed
```

### 5. Compilar assets frontend
```bash
# Para desarrollo (con watch)
docker-compose exec app npm run dev

# Para producción
docker-compose exec app npm run build
```

## 🌐 Acceso a la Aplicación

Una vez instalado, puedes acceder a:

- **Aplicación Principal**: http://localhost:8080
- **Mailpit (Mail Testing)**: http://localhost:8026
- **Base de Datos MySQL**: `localhost:3307`
- **Redis**: `localhost:6380`

### Credenciales por Defecto

**Base de Datos:**
- Host: `localhost:3307`
- Usuario: `restoapp_user`
- Contraseña: `restoapp_pass`
- Base de datos: `restoapp`

## 🔧 Comandos Útiles de Desarrollo

### Gestión de contenedores
```bash
# Iniciar servicios
docker-compose up -d

# Detener servicios
docker-compose down

# Ver logs
docker-compose logs -f

# Acceder al contenedor de la aplicación
docker-compose exec app bash
```

### Comandos Laravel
```bash
# Ejecutar migraciones
docker-compose exec app php artisan migrate

# Limpiar cachés
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear

# Ejecutar tests
docker-compose exec app php artisan test

# Generar documentación API
docker-compose exec app php artisan route:list
```

### Frontend
```bash
# Modo desarrollo con hot reload
docker-compose exec app npm run dev

# Build para producción
docker-compose exec app npm run build

# Instalar nueva dependencia
docker-compose exec app npm install package-name
```

## 🗄️ Estructura de Base de Datos

El sistema incluye las siguientes tablas principales:

- `users` - Usuarios del sistema
- `categories` - Categorías de productos
- `products` - Inventario de productos
- `clients` - Base de datos de clientes
- `invoices` - Facturas/órdenes
- `invoice_details` - Detalles de factura
- `cash_registers` - Control de cajas registradoras
- `invoice_payments` - Pagos de facturas

## 🧪 Testing

```bash
# Ejecutar todos los tests
docker-compose exec app php artisan test

# Ejecutar tests específicos
docker-compose exec app php artisan test --testsuite=Feature
docker-compose exec app php artisan test --testsuite=Unit

# Con coverage (requiere Xdebug)
docker-compose exec app php artisan test --coverage
```

## 🐛 Troubleshooting

### Problemas Comunes

**1. Error de conexión a MySQL**
```bash
# Verificar que MySQL esté saludable
docker-compose exec mysql mysqladmin ping -h localhost -u restoapp_user -p

# Reiniciar servicios de base de datos
docker-compose restart mysql
```

**2. Permisos de archivos**
```bash
# Ajustar permisos (Linux/macOS)
sudo chown -R $USER:$USER .
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache
```

**3. Puertos ocupados**
```bash
# Verificar puertos en uso
sudo netstat -tulpn | grep :8080

# Cambiar puertos en .env si es necesario
APP_PORT=8081
FORWARD_DB_PORT=3308
```

**4. Limpiar todo y empezar de nuevo**
```bash
# Detener y eliminar contenedores
docker-compose down -v

# Eliminar imágenes
docker-compose down --rmi all

# Reconstruir desde cero
docker-compose up -d --build
```

## 📚 Documentación Adicional

- [Laravel Documentation](https://laravel.com/docs)
- [Livewire Documentation](https://livewire.laravel.com)
- [Tailwind CSS](https://tailwindcss.com)

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.
