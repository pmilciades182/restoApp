<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Category;
use App\Models\InvoiceDetail;
use App\Traits\HasBreadcrumbs;
use Illuminate\Support\Facades\DB;

class ProductSales extends Component
{
    use HasBreadcrumbs;

    public $dateFrom;
    public $dateTo;
    public $categoryId = '';
    public $sortField = 'total_amount';
    public $sortDirection = 'desc';

    protected function getBaseBreadcrumbs()
    {
        return [
            ['name' => 'Facturas', 'route' => 'invoices.index'],
            ['name' => 'Reportes'],
            ['name' => 'Venta de Productos']
        ];
    }

    public function mount()
    {
        // Set default date range to current month
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getProductSalesProperty()
    {
        return InvoiceDetail::query()
            ->join('products', 'invoice_details.product_id', '=', 'products.id')
            ->join('invoices', 'invoice_details.invoice_id', '=', 'invoices.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereNull('invoice_details.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->where('invoices.status', 'paid')
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('invoices.created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('invoices.created_at', '<=', $this->dateTo);
            })
            ->when($this->categoryId, function ($query) {
                $query->where('products.category_id', $this->categoryId);
            })
            ->select(
                'products.id',
                'products.name as product_name',
                'categories.name as category_name',
                DB::raw('SUM(invoice_details.quantity) as total_quantity'),
                DB::raw('SUM(invoice_details.subtotal) as total_amount')
            )
            ->groupBy('products.id', 'products.name', 'categories.name')
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();
    }

    public function getTotalAmountProperty()
    {
        return $this->productSales->sum('total_amount');
    }

    public function getTotalQuantityProperty()
    {
        return $this->productSales->sum('total_quantity');
    }

    public function render()
    {
        return view('livewire.reports.product-sales', [
            'categories' => Category::orderBy('name')->get(),
            'breadcrumbs' => $this->getBaseBreadcrumbs()
        ])->layout('layouts.app');
    }
}
