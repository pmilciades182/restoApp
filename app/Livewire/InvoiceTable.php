<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Traits\HasBreadcrumbs;
use Illuminate\Support\Facades\DB;

class InvoiceTable extends Component
{
    use WithPagination, HasBreadcrumbs;

    public $search = '';
    public $filterStatus = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterPaymentMethod = '';
    public $filterWaiter = '';
    public $filterTableNumber = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterDateFrom' => ['except' => ''],
        'filterDateTo' => ['except' => ''],
        'filterPaymentMethod' => ['except' => ''],
        'filterWaiter' => ['except' => ''],
        'filterTableNumber' => ['except' => '']
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function cancelInvoice($id)
    {
        try {
            DB::beginTransaction();

            $invoice = Invoice::findOrFail($id);

            if ($invoice->status === 'paid') {
                session()->flash('error', 'No se puede cancelar una factura que ya ha sido pagada.');
                return;
            }

            $invoice->cancel('Cancelado por el usuario');

            DB::commit();
            session()->flash('message', 'Factura cancelada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al cancelar la factura: ' . $e->getMessage());
        }
    }

    public function markAsPaid($id)
    {
        try {
            DB::beginTransaction();

            $invoice = Invoice::findOrFail($id);

            if ($invoice->status !== 'pending') {
                session()->flash('error', 'Solo se pueden marcar como pagadas las facturas pendientes.');
                return;
            }

            $invoice->markAsPaid();

            DB::commit();
            session()->flash('message', 'Factura marcada como pagada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al marcar la factura como pagada: ' . $e->getMessage());
        }
    }

    public function getInvoicesProperty()
    {
        return Invoice::query()
            ->with(['client', 'creator', 'details.product'])
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('invoice_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('client', function ($query) {
                            $query->where('business_name', 'like', '%' . $this->search . '%')
                                ->orWhere('first_name', 'like', '%' . $this->search . '%')
                                ->orWhere('last_name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterDateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->filterDateTo);
            })
            ->when($this->filterPaymentMethod, function ($query) {
                $query->where('payment_method', $this->filterPaymentMethod);
            })
            ->when($this->filterWaiter, function ($query) {
                $query->where('waiter', $this->filterWaiter);
            })
            ->when($this->filterTableNumber, function ($query) {
                $query->where('table_number', $this->filterTableNumber);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getTotalSalesProperty()
    {
        return Invoice::query()
            ->where('status', 'paid')
            ->when($this->filterDateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->filterDateTo);
            })
            ->sum('total');
    }

    protected function getBaseBreadcrumbs()
    {
        return [
            ['name' => 'Facturas']
        ];
    }

    public function render()
    {
        $invoices = Invoice::with('client') // Asegura cargar la relación client
            ->when($this->filterDateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->filterDateTo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.invoice.invoice-table', [
            'invoices' => $invoices,
            'statuses' => [
                'pending' => 'Pendiente',
                'paid' => 'Pagado',
                'cancelled' => 'Cancelado',
                'void' => 'Anulado'
            ],
            'breadcrumbs' => [['name' => 'Facturas']]
        ])->layout('layouts.app');
    }
}
