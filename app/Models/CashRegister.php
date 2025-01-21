<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashRegister extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'initial_cash',
        'final_cash',
        'opened_at',
        'closed_at',
        'closing_notes',
        'status'
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'initial_cash' => 'decimal:2',
        'final_cash' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function hasOpenRegister()
    {
        return static::where('status', 'open')->exists();
    }

    public static function getOpenRegister()
    {
        return static::where('status', 'open')->first();
    }

      // Agregar relación con facturas
      public function invoices()
      {
          return $this->hasMany(Invoice::class);
      }

    public function close($finalCash, $notes = null)
    {
        $this->update([
            'final_cash' => $finalCash,
            'closed_at' => now(),
            'closing_notes' => $notes,
            'status' => 'closed'
        ]);
    }
}
