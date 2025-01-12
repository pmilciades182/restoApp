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
    Route::get('/clients/{clientId}', ClientShow::class)->name('clients.show'); // Mover esta ruta antes de edit
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


});
