# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 10 restaurant management application ("restoApp") built with:
- **Backend**: Laravel 10 with Jetstream for authentication
- **Frontend**: Livewire 3 for reactive components, Alpine.js, Tailwind CSS
- **Database**: MySQL (configured via .env)
- **Testing**: PHPUnit with Feature and Unit test suites

## Key Development Commands

### Environment Setup
```bash
# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# Install dependencies
composer install
npm install

# Database operations
php artisan migrate
php artisan db:seed
```

### Development Workflow
```bash
# Start development server
php artisan serve

# Build frontend assets
npm run dev          # Development with hot reload
npm run build        # Production build

# Run tests
php artisan test              # Run all tests
php artisan test --testsuite=Feature  # Feature tests only
php artisan test --testsuite=Unit     # Unit tests only
vendor/bin/phpunit            # Alternative test runner

# Code quality
vendor/bin/pint              # Laravel Pint for code formatting (if available)

# Clear caches (useful for debugging)
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## Application Architecture

### Core Business Models
- **Invoice**: Main transaction entity with payment tracking, status management
- **InvoiceDetail**: Line items for each invoice
- **Product**: Catalog items with categories and stock management
- **Client**: Customer management with document tracking
- **CashRegister**: Point-of-sale session management
- **Category**: Product categorization

### Key Application Areas

**Point of Sale (POS)**
- Route: `/invoices/pos/create` → `PointOfSale` Livewire component
- Handles order creation, payment processing, table management

**Kitchen Management**
- Route: `/invoices/kitchen` → `KitchenDisplay` component
- Order status tracking, preparation workflow

**Reporting System**
- Daily/Monthly sales reports
- Product performance analytics
- Waiter performance tracking
- Routes under `/invoices/reports/`

**Cash Register Management**
- Session-based sales tracking
- Opening/closing balance management
- Financial reporting

### Livewire Component Pattern
The application uses Livewire extensively with a consistent pattern:
- Table components (e.g., `ProductTable`, `ClientTable`) for listing/management
- Form components (e.g., `ProductForm`, `ClientForm`) for create/edit operations
- Show components for detailed views

### API Endpoints
Internal API routes under `/invoices/api/` for:
- Product search and filtering
- Client search
- Real-time calculations and stock verification

## Database Structure

Key migrations show the evolution:
- Standard Laravel auth tables (2014-2019)
- Core business entities (2025-01-07 to 2025-01-12)
- Payment system enhancements (2025-01-20)

## File Organization

- **Models**: Standard Eloquent models in `app/Models/`
- **Livewire Components**: `app/Livewire/` with corresponding Blade views in `resources/views/livewire/`
- **Controllers**: Traditional controllers in `app/Http/Controllers/` and API controllers in `app/Http/Controllers/Api/`
- **Routes**: Web routes in `routes/web.php` with logical grouping by feature area

## Development Notes

- Uses Laravel Jetstream for authentication and user management
- Implements soft deletes on key models (Invoice model shows `SoftDeletes` trait)
- Spanish localization present (`resources/lang/es/`)
- Docker support available (`docker-compose.yml`)
- Uses Vite for asset compilation with Tailwind CSS and Alpine.js integration

## Testing Strategy

- Feature tests for user workflows and API endpoints
- Unit tests for business logic and model methods
- Test environment configured to use array drivers and separate database