<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Client;
use App\Models\ClientDocument;
use App\Traits\HasBreadcrumbs;
use Illuminate\Support\Facades\DB;
use App\Models\CashRegister;
use App\Models\PaymentMethod;

class InvoiceForm extends Component
{
    use HasBreadcrumbs;

    // Propiedades del formulario
    public $client_id = '';
    public $invoice_date;
    public $items = [];
    public $quantity = 1;

    // Propiedades de control
    public $invoiceId;
    public $editMode = false;
    public $subtotal = 0;
    public $tax = 0;
    public $total = 0;

    // Propiedades para búsqueda de cliente
    public $document_number = '';
    public $selected_document_type = '';
    public $available_documents = [];
    public $client = null;

    // Propiedades para búsqueda de productos
    public $search_product = '';
    public $barcode = '';
    public $available_products = [];

    protected $listeners = ['focusBarcode'];
    public $currentCashRegister = null;
    public $hasCashRegister = false;

    public $payment_method = 'cash';
    public $amount_received = 0;
    public $reference_number = '';

    public $paymentMethods = [];
    public $totalPaid = 0;
    public $remainingBalance = 0;



    // Añadir este método al componente para calcular el vuelto
    public function calculateChange()
    {
        return max(0, $this->amount_received - $this->total);
    }

    // Añadir este método para validar el método de pago
    public function updatedPaymentMethod()
    {
        if ($this->payment_method !== 'cash') {
            $this->amount_received = $this->total;
        }
    }


    public function boot()
    {
        // Verificar si hay una caja abierta antes de permitir cualquier operación
        $this->checkCashRegister();
    }


    private function checkCashRegister()
    {
        $this->currentCashRegister = CashRegister::getOpenRegister();
        $this->hasCashRegister = !is_null($this->currentCashRegister);

        if (!$this->hasCashRegister) {
            \Log::warning('No hay caja abierta al intentar acceder al formulario de factura');
        } else {
            \Log::info('Caja abierta encontrada', [
                'register_id' => $this->currentCashRegister->id,
                'opened_at' => $this->currentCashRegister->opened_at
            ]);
        }
    }


    public function handleClientAdded($clientId)
    {
        // Cargar el nuevo cliente y establecerlo como seleccionado
        $this->client = Client::findOrFail($clientId);
        $this->client_id = $clientId;

        // Si el cliente tiene documento principal, establecerlo
        if ($primaryDoc = $this->client->primaryDocument) {
            $this->document_number = $primaryDoc->document_number;
            $this->selected_document_type = $primaryDoc->documentType->name;
        }

        $this->available_documents = [];
        session()->flash('message', 'Cliente agregado exitosamente.');
    }


    public function addClient()
    {
        $breadcrumbs = [
            ['name' => 'Facturas', 'route' => 'invoices.index'],
            ['name' => $this->editMode ? 'Editar Factura' : 'Nueva Factura']
        ];

        $encodedBreadcrumbs = base64_encode(json_encode($breadcrumbs));

        return response()->redirectToRoute('clients.create', [
            'redirect_to' => 'invoice',
            'parent_breadcrumbs' => $encodedBreadcrumbs
        ]);
    }
    protected function rules()
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ];
    }

    public function mount($invoiceId = null)
    {
        $this->invoice_date = now()->format('Y-m-d');
        $this->checkCashRegister();

        $this->addPaymentMethod();
        $this->calculateBalances();

        if (!$this->hasCashRegister) {
            return;
        }

        if ($invoiceId) {
            $this->invoiceId = $invoiceId;
            $this->editMode = true;
            $this->loadInvoice();
        }
    }


    public function removePaymentMethod($index)
    {
        unset($this->paymentMethods[$index]);
        $this->paymentMethods = array_values($this->paymentMethods);
        $this->calculateBalances();
    }


    public function getChange($index)
    {
        if ($this->paymentMethods[$index]['method'] === 'cash') {
            $amount = floatval($this->paymentMethods[$index]['amount']);
            $pendingBeforeThisPayment = $this->total - (collect($this->paymentMethods)
                ->take($index)
                ->sum('amount'));

            return max(0, $amount - min($amount, $pendingBeforeThisPayment));
        }
        return 0;
    }


    public function calculateBalances()
    {
        // Calcular el total pagado sumando todos los montos directamente
        $this->totalPaid = collect($this->paymentMethods)->sum(function ($payment) {
            return floatval($payment['amount']);
        });

        // Calcular el saldo pendiente
        $this->remainingBalance = $this->total - $this->totalPaid;

        // Si hay sobrepago, ajustar el cambio/vuelto solo para pagos en efectivo
        if ($this->totalPaid > $this->total) {
            $cashPayments = collect($this->paymentMethods)->where('method', 'cash');
            if ($cashPayments->isNotEmpty()) {
                $lastCashPayment = $cashPayments->last();
                $change = $this->totalPaid - $this->total;
                // El vuelto se maneja por separado, pero el total pagado debe ser exacto
                $this->totalPaid = $this->total;
                $this->remainingBalance = 0;
            }
        }
    }


    // Métodos para gestionar los pagos
    public function addPaymentMethod()
    {
        $this->paymentMethods[] = [
            'method' => 'cash',
            'amount' => 0,
            'reference' => '',
        ];
    }

    // Búsqueda de cliente por número de documento
    public function updatedDocumentNumber()
    {
        \Log::info('updatedDocumentNumber called', [
            'document_number' => $this->document_number,
            'length' => strlen($this->document_number)
        ]);

        if (strlen($this->document_number) >= 3) {
            \Log::info('Searching for documents');

            $documents = ClientDocument::where('document_number', 'like', $this->document_number . '%')
                ->with(['client', 'documentType'])  // Cargar explícitamente la relación client
                ->get();

            \Log::info('Found documents', [
                'count' => $documents->count(),
                'documents' => $documents->toArray()
            ]);

            $this->available_documents = $documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'document_type' => $doc->documentType->name,
                    'document_number' => $doc->document_number,
                    'client_id' => $doc->client_id,
                    'client_name' => $doc->client ? $doc->client->full_name : 'Cliente no disponible'
                ];
            })->toArray();

            \Log::info('Mapped documents', [
                'available_documents' => $this->available_documents
            ]);
        } else {
            $this->available_documents = [];
        }

        $this->client = null;
        $this->client_id = '';
    }

    // Búsqueda de producto
    public function updatedSearchProduct()
    {
        \Log::info('updatedSearchProduct called', [
            'search_term' => $this->search_product,
            'length' => strlen($this->search_product)
        ]);

        if (strlen($this->search_product) >= 3) {
            try {
                $products = Product::where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search_product . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search_product . '%');
                })->get();

                \Log::info('Products found', [
                    'count' => $products->count(),
                    'products' => $products->toArray()
                ]);

                $this->available_products = $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'barcode' => $product->barcode
                    ];
                })->toArray();

            } catch (\Exception $e) {
                \Log::error('Error in updatedSearchProduct', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        } else {
            $this->available_products = [];
        }
    }

    // Búsqueda por código de barras
    public function updatedBarcode()
    {
        if (!empty($this->barcode)) {
            $product = Product::where('barcode', $this->barcode)->first();
            if ($product) {
                $this->addProductToList($product);
                $this->barcode = '';
                $this->dispatch('focus-barcode');
            }
        }
    }

    // Agregar producto a la lista
    private function addProductToList($product)
    {
        \Log::info('addProductToList called', [
            'product' => $product->toArray(),
            'current_items_count' => count($this->items)
        ]);

        try {
            $existingIndex = null;
            foreach ($this->items as $index => $item) {
                if ($item['product_id'] == $product->id) {
                    $existingIndex = $index;
                    break;
                }
            }

            if ($existingIndex !== null) {
                $this->items[$existingIndex]['quantity'] += $this->quantity;
                $this->items[$existingIndex]['subtotal'] =
                    $this->items[$existingIndex]['price'] * $this->items[$existingIndex]['quantity'];

                \Log::info('Updated existing item', [
                    'index' => $existingIndex,
                    'new_quantity' => $this->items[$existingIndex]['quantity'],
                    'new_subtotal' => $this->items[$existingIndex]['subtotal']
                ]);
            } else {
                $newItem = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $this->quantity,
                    'price' => $product->price,
                    'subtotal' => $product->price * $this->quantity
                ];

                $this->items[] = $newItem;

                \Log::info('Added new item', [
                    'new_item' => $newItem
                ]);
            }

            $this->calculateTotal();

        } catch (\Exception $e) {
            \Log::error('Error in addProductToList', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    // Mantener el foco en el input de código de barras
    public function focusBarcode()
    {
        $this->dispatch('focus-barcode');
    }



    // Seleccionar cliente
    public function selectDocument($documentId)
    {
        \Log::info('selectDocument called', [
            'document_id' => $documentId
        ]);

        try {
            $document = ClientDocument::with(['client', 'documentType'])->find($documentId);

            \Log::info('Document found', [
                'document' => $document->toArray()
            ]);

            $this->client = $document->client;
            $this->client_id = $document->client_id;
            $this->selected_document_type = $document->documentType->name;
            $this->document_number = $document->document_number;
            $this->available_documents = [];

            \Log::info('Client selected', [
                'client_id' => $this->client_id,
                'document_type' => $this->selected_document_type
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in selectDocument', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }


    // Seleccionar producto
    public function selectProduct($productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $this->addProductToList($product);
            $this->search_product = '';
            $this->available_products = [];
        }
    }

    public function loadInvoice()
    {
        $invoice = Invoice::with('details.product')->findOrFail($this->invoiceId);

        $this->client_id = $invoice->client_id;
        $this->invoice_date = $invoice->created_at->format('Y-m-d');

        foreach ($invoice->details as $detail) {
            $this->items[] = [
                'product_id' => $detail->product_id,
                'product_name' => $detail->product->name,
                'quantity' => $detail->quantity,
                'price' => $detail->unit_price,
                'subtotal' => $detail->subtotal
            ];
        }

        $this->calculateTotals();
    }

    public function addProduct()
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1'
        ], [
            'product_id.required' => 'Debe seleccionar un producto',
            'quantity.required' => 'La cantidad es requerida',
            'quantity.min' => 'La cantidad debe ser mayor a 0'
        ]);

        $product = Product::find($this->product_id);

        $this->items[] = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => $this->quantity,
            'price' => $product->price,
            'subtotal' => $product->price * $this->quantity
        ];

        $this->product_id = '';
        $this->quantity = 1;

        $this->calculateTotals();
    }
    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }


    private function calculateTotals()
    {
        $this->total = collect($this->items)->sum('subtotal');
    }

    public function updateQuantity($index, $newQuantity)
    {
        if ($newQuantity > 0) {
            $this->items[$index]['quantity'] = $newQuantity;
            $this->items[$index]['subtotal'] = $this->items[$index]['price'] * $newQuantity;
            $this->calculateTotal();
        }
    }

    private function calculateTotal()
    {
        $this->total = collect($this->items)->sum('subtotal');
    }


    public function store()
    {
        try {
            // Verificar nuevamente si hay caja abierta antes de crear la factura
            if (!$this->hasCashRegister) {
                session()->flash('error', 'No hay una caja abierta. No se puede crear la factura.');
                throw new \Exception('No hay una caja abierta. No se puede crear la factura.');
            }

            // Validar que el total pagado sea suficiente
            if ($this->remainingBalance > 0) {
                session()->flash('error', 'El monto total pagado debe cubrir el total de la factura.');
                throw new \Exception('El monto total pagado debe cubrir el total de la factura.');
            }

            // Mensajes de validación personalizados
            $messages = [
                'client_id.required' => 'Debe seleccionar un cliente para crear la factura.',
                'client_id.exists' => 'El cliente seleccionado no es válido.',
                'invoice_date.required' => 'La fecha de factura es obligatoria.',
                'invoice_date.date' => 'La fecha de factura no es válida.',
                'items.required' => 'Debe agregar al menos un producto a la factura.',
                'items.min' => 'Debe agregar al menos un producto a la factura.',
                'items.*.product_id.required' => 'Todos los productos deben ser válidos.',
                'items.*.product_id.exists' => 'Uno o más productos seleccionados no son válidos.',
                'items.*.quantity.required' => 'La cantidad es requerida para todos los productos.',
                'items.*.quantity.numeric' => 'La cantidad debe ser un número.',
                'items.*.quantity.min' => 'La cantidad debe ser mayor a 0.',
            ];

            // Validación inicial
            $this->validate([
                'client_id' => 'required|exists:clients,id',
                'invoice_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:1',
            ], $messages);

            // Verificación adicional de existencia de productos y stock
            foreach ($this->items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    session()->flash('error', "El producto seleccionado no existe.");
                    throw new \Exception("El producto seleccionado no existe.");
                }

                if ($product->stock < $item['quantity']) {
                    session()->flash('error', "Stock insuficiente para el producto: {$product->name}");
                    throw new \Exception("Stock insuficiente para el producto: {$product->name}");
                }
            }

            DB::beginTransaction();

            // Crear la factura vinculándola con la caja actual
            $invoice = Invoice::create([
                'client_id' => $this->client_id,
                'cash_register_id' => $this->currentCashRegister->id, // Vinculación con la caja
                'invoice_number' => $this->generateInvoiceNumber(),
                'invoice_type' => 'standard',
                'subtotal' => $this->total,
                'tax' => 0,
                'total' => $this->total,
                'status' => 'pending',
                'amount_paid' => 0, // Inicialmente no hay pago
                'balance' => $this->total, // El balance inicial es el total
                'payment_status' => 'unpaid', // Estado de pago inicial
                'created_by' => auth()->id()
            ]);

            // Crear los detalles
            foreach ($this->items as $item) {
                $invoice->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'tax' => 0,
                    'total' => $item['subtotal'],
                    'created_by' => auth()->id()
                ]);

                // Actualizar el stock del producto
                $product = Product::find($item['product_id']);
                $product->decrement('stock', $item['quantity']);
            }

            // Verificar que haya métodos de pago
            if (empty($this->paymentMethods)) {
                DB::rollBack();
                session()->flash('error', 'Debe seleccionar al menos un método de pago.');
                throw new \Exception('Debe seleccionar al menos un método de pago.');
            }

            // Registrar los pagos
            $totalPayments = 0;
            foreach ($this->paymentMethods as $payment) {
                if ($payment['amount'] > 0) {
                    // Verificar que el método de pago exista
                    $paymentMethod = PaymentMethod::where('code', $payment['method'])->first();
                    if (!$paymentMethod) {
                        DB::rollBack();
                        session()->flash('error', "Método de pago no válido: {$payment['method']}");
                        throw new \Exception("Método de pago no válido: {$payment['method']}");
                    }

                    $invoice->payments()->create([
                        'payment_method_id' => $paymentMethod->id,
                        'cash_register_id' => $this->currentCashRegister->id,
                        'amount' => $payment['method'] === 'cash' ?
                            min($payment['amount'], $this->total) :
                            $payment['amount'],
                        'reference_number' => $payment['method'] !== 'cash' ? $payment['reference'] : null,
                        'created_by' => auth()->id()
                    ]);

                    $totalPayments += $payment['amount'];
                }
            }

            // Verificar que los pagos cubran el total
            if ($totalPayments < $this->total) {
                DB::rollBack();
                session()->flash('error', 'El total de los pagos no cubre el monto de la factura.');
                throw new \Exception('El total de los pagos no cubre el monto de la factura.');
            }

            DB::commit();

            // Limpiar el formulario
            $this->reset(['items', 'client_id', 'client', 'document_number', 'selected_document_type']);
            $this->calculateTotals();

            session()->flash('message', 'Factura creada exitosamente.');

            // Redirigir a la vista de detalle de la factura
            return redirect()->route('invoices.show', ['invoiceId' => $invoice->id]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear factura:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Si no se ha establecido ya un mensaje flash, agregar uno genérico
            if (!session()->has('error')) {
                session()->flash('error', 'Error al crear la factura: ' . $e->getMessage());
            }

            // For Livewire, you might want to add:
            $this->addError('invoice_error', $e->getMessage());


            return null;
        }
    }

    public function update()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $invoice = Invoice::findOrFail($this->invoiceId);

            if ($invoice->status !== 'pending') {
                throw new \Exception('Solo se pueden editar facturas pendientes.');
            }

            $invoice->update([
                'client_id' => $this->client_id,
                'subtotal' => $this->total,
                'tax' => 0,
                'total' => $this->total,
                'updated_by' => auth()->id()
            ]);

            // Eliminar detalles anteriores
            $invoice->details()->delete();

            // Crear nuevos detalles
            foreach ($this->items as $item) {
                $invoice->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'tax' => 0,
                    'total' => $item['subtotal'],
                    'created_by' => auth()->id()
                ]);
            }

            DB::commit();
            session()->flash('message', 'Factura actualizada exitosamente.');
            return redirect()->route('invoices.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al actualizar la factura: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('invoices.index');
    }

    protected function generateInvoiceNumber()
    {
        $lastInvoice = Invoice::latest()->first();
        $lastNumber = $lastInvoice ? intval(substr($lastInvoice->invoice_number, -6)) : 0;
        return 'FAC-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }

    protected function getBaseBreadcrumbs()
    {
        return [
            ['name' => 'Facturas', 'route' => 'invoices.index'],
            ['name' => $this->editMode ? 'Editar Factura' : 'Nueva Factura']
        ];
    }

    public function render()
    {

        // Check for any existing error messages
        if (session()->has('error')) {
            $this->addError('invoice_error', session('error'));
        }

        if (!$this->hasCashRegister) {
            return view('livewire.invoice.no-cash-register', [
                'breadcrumbs' => $this->getBaseBreadcrumbs()
            ])->layout('layouts.app');
        }


        return view('livewire.invoice.invoice-form', [
            'breadcrumbs' => $this->getBaseBreadcrumbs()
        ])->layout('layouts.app');
    }


    // Búsqueda manual de cliente
    public function searchClient()
    {
        \Log::info('searchClient called', [
            'document_number' => $this->document_number
        ]);

        if (strlen($this->document_number) >= 3) {
            try {
                $documents = ClientDocument::where('document_number', 'like', '%' . $this->document_number . '%')
                    ->with(['client', 'documentType'])
                    ->get();

                \Log::info('Documents found', [
                    'count' => $documents->count(),
                    'documents' => $documents->toArray()
                ]);

                $this->available_documents = $documents->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'document_type' => $doc->documentType->name,
                        'document_number' => $doc->document_number,
                        'client_id' => $doc->client_id,
                        'client_name' => $doc->client->full_name
                    ];
                })->toArray();

            } catch (\Exception $e) {
                \Log::error('Error in searchClient', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }

    // Agregar producto por código de barras
    public function addProductByBarcode()
    {
        \Log::info('addProductByBarcode called', [
            'barcode' => $this->barcode
        ]);

        if (!empty($this->barcode)) {
            try {
                $product = Product::where('barcode', $this->barcode)->first();

                \Log::info('Product search result', [
                    'found' => isset($product),
                    'product' => $product ? $product->toArray() : null
                ]);

                if ($product) {
                    $this->addProductToList($product);
                    $this->barcode = '';
                    $this->dispatch('focus-barcode');
                } else {
                    session()->flash('error', 'Producto no encontrado');
                }
            } catch (\Exception $e) {
                \Log::error('Error in addProductByBarcode', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                session()->flash('error', 'Error al buscar el producto');
            }
        }
    }
    // Agregar producto desde búsqueda
    public function addProductBySearch()
    {
        \Log::info('addProductBySearch called', [
            'search_term' => $this->search_product
        ]);

        if (!empty($this->search_product)) {
            try {
                $product = Product::where('name', 'like', '%' . $this->search_product . '%')
                    ->orWhere('barcode', 'like', '%' . $this->search_product . '%')
                    ->first();

                \Log::info('Product search result', [
                    'found' => isset($product),
                    'product' => $product ? $product->toArray() : null
                ]);

                if ($product) {
                    $this->addProductToList($product);
                    $this->search_product = '';
                    $this->available_products = [];
                } else {
                    session()->flash('error', 'Producto no encontrado');
                }
            } catch (\Exception $e) {
                \Log::error('Error in addProductBySearch', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                session()->flash('error', 'Error al buscar el producto');
            }
        }
    }


    // Listeners para actualizar los cálculos cuando cambian los pagos
    public function updatedPaymentMethods()
    {
        $this->calculateBalances();
    }


}
