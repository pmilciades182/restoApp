<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\CategoryTable;
use App\Livewire\CategoryForm;
use App\Livewire\ProductTable;
use App\Livewire\ProductForm;
use App\Livewire\ClientTable;
use App\Livewire\ClientForm;
use App\Livewire\ClientShow;
use App\Livewire\ClientDocumentTable;
use App\Livewire\ClientDocumentForm;
use App\Livewire\DocumentTypeTable;
use App\Livewire\DocumentTypeForm;
use App\Livewire\InvoiceTable;
use App\Livewire\InvoiceForm;
use App\Livewire\InvoiceShow;
use App\Livewire\PointOfSale;
use App\Livewire\KitchenDisplay;
use App\Livewire\TableManagement;
use App\Livewire\CashRegisterManagement;
use App\Livewire\CashRegisterTable;
use App\Livewire\TableMap;
use App\Livewire\Reports\DailySales;
use App\Livewire\Reports\MonthlySales;
use App\Livewire\Reports\ProductSales;
use App\Livewire\Reports\WaiterSales;
use App\Http\Controllers\CashRegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard route
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // caja
    Route::get('/cash-register', CashRegisterManagement::class)->name('cash-register.management');
    Route::get('/cash-registers', CashRegisterTable::class)->name('cash-registers.index');
    Route::get('/cash-registers/{id}/statement', [CashRegisterController::class, 'statement'])
    ->name('cash-registers.statement');

    // Categories routes
    Route::get('/categories', CategoryTable::class)->name('categories.index');
    Route::get('/categories/create', CategoryForm::class)->name('categories.create');
    Route::get('/categories/{categoryId}/edit', CategoryForm::class)->name('categories.edit');
    Route::get('/categories/create/{redirect_to?}', CategoryForm::class)->name('categories.create');

    // Products routes
    Route::get('/products', ProductTable::class)->name('products.index');
    Route::get('/products/create', ProductForm::class)->name('products.create');
    Route::get('/products/{productId}/edit', ProductForm::class)->name('products.edit');

    // Clients routes
    Route::get('/clients', ClientTable::class)->name('clients.index');
    Route::get('/clients/create', ClientForm::class)->name('clients.create');
    Route::get('/clients/create/{redirect_to?}', ClientForm::class)->name('clients.create');
    Route::get('/clients/{clientId}', ClientShow::class)->name('clients.show');
    Route::get('/clients/{clientId}/edit', ClientForm::class)->name('clients.edit');

    // Client Documents routes
    Route::get('/clients/{clientId}/documents', ClientDocumentTable::class)->name('client-documents.index');
    Route::get('/clients/{clientId}/documents/create', ClientDocumentForm::class)->name('client-documents.create');
    Route::get('/clients/{clientId}/documents/{documentId}/edit', ClientDocumentForm::class)->name('client-documents.edit');

    // Document Types routes
    Route::get('/document-types', DocumentTypeTable::class)->name('document-types.index');
    Route::get('/document-types/create', DocumentTypeForm::class)->name('document-types.create');
    Route::get('/document-types/create/{redirect_to?}', DocumentTypeForm::class)->name('document-types.create');
    Route::get('/document-types/{documentTypeId}/edit', DocumentTypeForm::class)->name('document-types.edit');

    // Invoices routes
    Route::prefix('invoices')->group(function () {
        // Vista principal de facturas
        Route::get('/', InvoiceTable::class)->name('invoices.index');
        Route::get('/create', InvoiceForm::class)->name('invoices.create');
        Route::get('/{invoiceId}/edit', InvoiceForm::class)->name('invoices.edit');
        Route::get('/{invoiceId}', InvoiceShow::class)->name('invoices.show');

        // POS y Cocina
        Route::get('/pos/create', PointOfSale::class)->name('invoices.pos');
        Route::get('/kitchen', KitchenDisplay::class)->name('invoices.kitchen');

        // Gestión de mesas
        Route::get('/tables', TableManagement::class)->name('invoices.tables.index');
        Route::get('/tables/map', TableMap::class)->name('invoices.tables.map');

        // Reportes
        Route::prefix('reports')->group(function () {
            Route::get('/daily', DailySales::class)->name('invoices.reports.daily');
            Route::get('/monthly', MonthlySales::class)->name('invoices.reports.monthly');
            Route::get('/products', ProductSales::class)->name('invoices.reports.products');
            Route::get('/waiters', WaiterSales::class)->name('invoices.reports.waiters');
        });

        // Exportación de documentos
        Route::controller(App\Http\Controllers\InvoiceController::class)->group(function () {
            Route::get('/{invoiceId}/pdf', 'generatePdf')->name('invoices.pdf');
            Route::get('/{invoiceId}/ticket', 'generateTicket')->name('invoices.ticket');
        });

        // API endpoints para el POS
        Route::prefix('api')->group(function () {
            Route::controller(App\Http\Controllers\Api\ProductController::class)->group(function () {
                Route::get('/products', 'index');
                Route::get('/products/search', 'search');
                Route::get('/products/category/{categoryId}', 'byCategory');
            });

            Route::controller(App\Http\Controllers\Api\ClientController::class)->group(function () {
                Route::get('/clients', 'index');
                Route::get('/clients/search', 'search');
            });

            Route::controller(App\Http\Controllers\Api\InvoiceController::class)->group(function () {
                Route::post('/calculate-totals', 'calculateTotals');
                Route::post('/verify-stock', 'verifyStock');
                Route::post('/update-order-status', 'updateOrderStatus');
            });
        });
    });
});
