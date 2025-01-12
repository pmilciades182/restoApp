<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'tax',
        'total',
        'discount',
        'notes',
        'status',
        'prepared_at',
        'delivered_at',
        'cancelled_reason',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'prepared_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    // Relación con la factura
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Relación con el producto
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relación con el usuario que creó el detalle
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relación con el usuario que actualizó el detalle
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scope para items pendientes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope para items en preparación
    public function scopePreparing($query)
    {
        return $query->where('status', 'preparing');
    }

    // Scope para items listos
    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    // Scope para items entregados
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    // Método para calcular totales
    public function calculateTotals()
    {
        $this->subtotal = $this->quantity * $this->unit_price;
        $this->total = $this->subtotal + $this->tax - $this->discount;
        return $this;
    }

    // Método para marcar como en preparación
    public function markAsPreparing()
    {
        $this->status = 'preparing';
        $this->save();
    }

    // Método para marcar como listo
    public function markAsReady()
    {
        $this->status = 'ready';
        $this->prepared_at = now();
        $this->save();
    }

    // Método para marcar como entregado
    public function markAsDelivered()
    {
        $this->status = 'delivered';
        $this->delivered_at = now();
        $this->save();
    }

    // Método para cancelar el item
    public function cancel($reason)
    {
        $this->status = 'cancelled';
        $this->cancelled_reason = $reason;
        $this->save();
    }
}
