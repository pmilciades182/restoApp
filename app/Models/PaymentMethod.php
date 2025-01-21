<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'requires_reference',
        'active'
    ];

    protected $casts = [
        'requires_reference' => 'boolean',
        'active' => 'boolean'
    ];

    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class);
    }
}
