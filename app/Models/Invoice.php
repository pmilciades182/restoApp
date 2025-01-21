<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'cash_register_id',
        'invoice_number',
        'invoice_type',
        'subtotal',
        'tax',
        'total',
        'discount',
        'status',
        'payment_method',
        'notes',
        'table_number',
        'waiter',
        'paid_at',
        'cancelled_at',
        'cancelled_reason',
        'created_by',
        'updated_by',
        'payment_status',
        'amount_paid',
        'balance'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];
    // Relación con el cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relación con los detalles de la factura
    public function details()
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    // Relación con el usuario que creó la factura
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relación con el usuario que actualizó la factura
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scope para facturas pendientes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope para facturas pagadas
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Scope para facturas canceladas
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Método para calcular totales
    public function calculateTotals()
    {
        $this->subtotal = $this->details->sum('subtotal');
        $this->tax = $this->details->sum('tax');
        $this->total = $this->subtotal + $this->tax - $this->discount;
        return $this;
    }

    // Método para marcar como pagada
    public function markAsPaid()
    {
        $this->status = 'paid';
        $this->paid_at = now();
        $this->save();
    }

    // Método para cancelar la factura
    public function cancel($reason)
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        $this->cancelled_reason = $reason;
        $this->save();
    }


    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

      // Nueva relación con la caja
      public function cashRegister()
      {
          return $this->belongsTo(CashRegister::class);
      }


}
