<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'document_type_id',
        'document_number',
        'verification_digit',
        'expiration_date',
        'is_primary'
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'is_primary' => 'boolean'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    // Método para obtener el número de documento completo (con dígito verificador si aplica)
    public function getFullDocumentNumberAttribute()
    {
        if ($this->verification_digit) {
            return $this->document_number . '-' . $this->verification_digit;
        }
        return $this->document_number;
    }
}
