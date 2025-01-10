<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Product extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'barcode',
        'description',
        'price',
        'cost',
        'category_id',
        'stock',
        'is_kitchen'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock' => 'integer',
        'is_kitchen' => 'boolean'
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

  /**
 * Genera un código de barras único
 *
 * @return string
 */
public static function generateUniqueBarcode()
{
    Log::info('Iniciando generateUniqueBarcode en Model');

    $attempts = 0;
    $maxAttempts = 5;

    do {
        $barcode = strval(mt_rand(1000000000, 9999999999));
        Log::info('Intento de generar barcode:', [
            'attempt' => $attempts + 1,
            'barcode' => $barcode
        ]);

        $exists = self::where('barcode', $barcode)->exists();
        Log::info('Verificación de existencia:', ['exists' => $exists]);

        $attempts++;
        if ($attempts >= $maxAttempts) {
            Log::error('Máximo de intentos alcanzado al generar barcode');
            throw new \Exception('No se pudo generar un código de barras único después de ' . $maxAttempts . ' intentos');
        }
    } while ($exists);

    Log::info('Barcode generado exitosamente:', ['barcode' => $barcode]);
    return $barcode;
}


    /**
     * Get the formatted price in Guaraníes
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }

    /**
     * Get the formatted cost in Guaraníes
     */
    public function getFormattedCostAttribute()
    {
        return number_format($this->cost, 0, ',', '.');
    }
}
