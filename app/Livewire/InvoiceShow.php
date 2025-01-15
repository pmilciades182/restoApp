<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Invoice;
use App\Traits\HasBreadcrumbs;
use Illuminate\Support\Facades\Log;

class InvoiceShow extends Component
{
    use HasBreadcrumbs;

    public $invoice;
    public $invoiceId;
    public $client;
    public $details;

    public function mount($invoiceId)
    {
        Log::info('InvoiceShow::mount', [
            'invoice_id' => $invoiceId
        ]);

        try {
            $this->invoiceId = $invoiceId;
            $this->loadInvoice();
        } catch (\Exception $e) {
            Log::error('Error loading invoice', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Error al cargar la factura');
        }
    }

    private function loadInvoice()
    {
        $this->invoice = Invoice::with([
            'client',
            'details.product',
            'creator',
            'updater'
        ])->findOrFail($this->invoiceId);

        $this->client = $this->invoice->client;
        $this->details = $this->invoice->details;

        Log::info('Invoice loaded', [
            'invoice_number' => $this->invoice->invoice_number,
            'client' => $this->client->full_name,
            'details_count' => $this->details->count()
        ]);
    }

    public function printInvoice()
    {
        try {
            \Log::info('Iniciando impresión de ticket', [
                'invoice_id' => $this->invoice->id,
                'invoice_number' => $this->invoice->invoice_number
            ]);

            // Verificar que tenemos todos los datos necesarios
            if (!$this->invoice || !$this->client || !$this->details) {
                \Log::error('Faltan datos necesarios para la impresión', [
                    'has_invoice' => isset($this->invoice),
                    'has_client' => isset($this->client),
                    'details_count' => $this->details ? $this->details->count() : 0
                ]);
                return;
            }

            \Log::info('Emitiendo evento print-ticket');
            $this->dispatch('print-ticket');

            \Log::info('Evento print-ticket emitido');
            return;

        } catch (\Exception $e) {
            \Log::error('Error al imprimir ticket', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function cancelInvoice()
    {
        Log::info('Attempting to cancel invoice', [
            'invoice_id' => $this->invoiceId,
            'current_status' => $this->invoice->status
        ]);

        try {
            if ($this->invoice->status === 'paid') {
                throw new \Exception('No se puede cancelar una factura que ya ha sido pagada.');
            }

            $this->invoice->cancel('Cancelado por el usuario');
            $this->invoice->refresh();

            session()->flash('message', 'Factura cancelada exitosamente');
            Log::info('Invoice cancelled successfully');

        } catch (\Exception $e) {
            Log::error('Error cancelling invoice', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Error al cancelar la factura: ' . $e->getMessage());
        }
    }

    public function markAsPaid()
    {
        Log::info('Attempting to mark invoice as paid', [
            'invoice_id' => $this->invoiceId,
            'current_status' => $this->invoice->status
        ]);

        try {
            if ($this->invoice->status !== 'pending') {
                throw new \Exception('Solo se pueden marcar como pagadas las facturas pendientes.');
            }

            $this->invoice->markAsPaid();
            $this->invoice->refresh();

            session()->flash('message', 'Factura marcada como pagada exitosamente');
            Log::info('Invoice marked as paid successfully');

        } catch (\Exception $e) {
            Log::error('Error marking invoice as paid', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Error al marcar la factura como pagada: ' . $e->getMessage());
        }
    }

    protected function getBaseBreadcrumbs()
    {
        return [
            ['name' => 'Facturas', 'route' => 'invoices.index'],
            ['name' => 'Factura ' . ($this->invoice ? $this->invoice->invoice_number : '')]
        ];
    }

    public function getStatusBadgeClassesProperty()
    {
        return match ($this->invoice->status) {
            'paid' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStatusTextProperty()
    {
        return match ($this->invoice->status) {
            'paid' => 'Pagada',
            'pending' => 'Pendiente',
            'cancelled' => 'Cancelada',
            default => 'Desconocido'
        };
    }

    public function getCanBeCancelledProperty()
    {
        return $this->invoice->status === 'pending';
    }

    public function getCanBeMarkedAsPaidProperty()
    {
        return $this->invoice->status === 'pending';
    }

    public function render()
    {
        return view('livewire.invoice.invoice-show', [
            'breadcrumbs' => $this->getBaseBreadcrumbs()
        ])->layout('layouts.app');
    }
}
