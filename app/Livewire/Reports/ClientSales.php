<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Client;
use App\Models\InvoiceDetail;
use App\Traits\HasBreadcrumbs;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientSales extends Component
{
    use HasBreadcrumbs;

    public $dateFrom;
    public $dateTo;
    public $clientId = '';
    public $sortField = 'client_name';
    public $sortDirection = 'asc';

    protected function getBaseBreadcrumbs()
    {
        return [
            ['name' => 'Facturas', 'route' => 'invoices.index'],
            ['name' => 'Reportes'],
            ['name' => 'Venta por Cliente']
        ];
    }

    public function mount()
    {
        // Leave date fields empty by default
        $this->dateFrom = '';
        $this->dateTo = '';
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

    public function getClientSalesProperty()
    {
        // No mostrar datos si no hay fechas seleccionadas
        if (empty($this->dateFrom) || empty($this->dateTo)) {
            return collect();
        }

        return InvoiceDetail::query()
            ->join('invoices', 'invoice_details.invoice_id', '=', 'invoices.id')
            ->join('clients', 'invoices.client_id', '=', 'clients.id')
            ->join('products', 'invoice_details.product_id', '=', 'products.id')
            ->whereNull('invoice_details.deleted_at')
            ->whereNull('invoices.deleted_at')
            ->where('invoices.status', 'paid')
            ->whereDate('invoices.created_at', '>=', $this->dateFrom)
            ->whereDate('invoices.created_at', '<=', $this->dateTo)
            ->when($this->clientId, function ($query) {
                $query->where('clients.id', $this->clientId);
            })
            ->select(
                'clients.id as client_id',
                DB::raw('CASE WHEN clients.client_type = "business" THEN clients.business_name ELSE CONCAT(clients.first_name, " ", clients.last_name) END as client_name'),
                DB::raw('DATE(invoices.created_at) as sale_date'),
                'products.id as product_id',
                'products.name as product_name',
                DB::raw('SUM(invoice_details.quantity) as total_quantity'),
                DB::raw('SUM(invoice_details.subtotal) as total_amount')
            )
            ->groupBy('clients.id', DB::raw('CASE WHEN clients.client_type = "business" THEN clients.business_name ELSE CONCAT(clients.first_name, " ", clients.last_name) END'), DB::raw('DATE(invoices.created_at)'), 'products.id', 'products.name')
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('sale_date', 'desc')
            ->orderBy('product_name', 'asc')
            ->get()
            ->groupBy('client_name')
            ->map(function ($clientSales) {
                return $clientSales->groupBy('sale_date');
            });
    }

    public function getTotalAmountProperty()
    {
        $total = 0;
        foreach ($this->clientSales as $client) {
            foreach ($client as $dateSales) {
                $total += $dateSales->sum('total_amount');
            }
        }
        return $total;
    }

    public function getTotalQuantityProperty()
    {
        $total = 0;
        foreach ($this->clientSales as $client) {
            foreach ($client as $dateSales) {
                $total += $dateSales->sum('total_quantity');
            }
        }
        return $total;
    }

    public function export()
    {
        $data = $this->clientSales;

        return new StreamedResponse(function () use ($data) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for proper Excel encoding
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($handle, ['Cliente', 'Fecha', 'Producto', 'Cantidad', 'Total'], ';');

            // Data
            foreach ($data as $clientName => $clientSales) {
                foreach ($clientSales as $date => $dateSales) {
                    foreach ($dateSales as $sale) {
                        fputcsv($handle, [
                            $clientName,
                            date('d/m/Y', strtotime($date)),
                            $sale->product_name,
                            $sale->total_quantity,
                            number_format($sale->total_amount, 0, ',', '.')
                        ], ';');
                    }
                }
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="ventas_por_cliente_' . date('Y-m-d_His') . '.csv"',
        ]);
    }

    public function exportTxt()
    {
        $data = $this->clientSales;

        return new StreamedResponse(function () use ($data) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for proper encoding
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers separados por tabulaciones
            fwrite($handle, "Fecha\tCliente\tProducto\tCantidad\tMonto\n");

            // Data
            foreach ($data as $clientName => $clientSales) {
                foreach ($clientSales as $date => $dateSales) {
                    foreach ($dateSales as $sale) {
                        $line = sprintf(
                            "%s\t%s\t%s\t%s\t%s\n",
                            date('d/m/Y', strtotime($date)),
                            $clientName,
                            $sale->product_name,
                            $sale->total_quantity,
                            number_format($sale->total_amount, 0, ',', '.')
                        );
                        fwrite($handle, $line);
                    }
                }
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="ventas_por_cliente_' . date('Y-m-d_His') . '.txt"',
        ]);
    }

    public function render()
    {
        return view('livewire.reports.client-sales', [
            'clients' => Client::all()->sortBy(function($client) {
                return $client->full_name;
            }),
            'breadcrumbs' => $this->getBaseBreadcrumbs()
        ])->layout('layouts.app');
    }
}
