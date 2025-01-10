<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     */
    public static function generateUniqueBarcode()
    {
        do {
            $barcode = mt_rand(1000000000, 9999999999); // Genera un código de 10 dígitos
        } while (self::where('barcode', $barcode)->exists());

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
