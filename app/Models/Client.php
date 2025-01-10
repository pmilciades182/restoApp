<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_name',
        'first_name',
        'last_name',
        'fantasy_name',
        'client_type',
        'email',
        'phone',
        'mobile_phone',
        'address',
        'city',
        'state',
        'district',
        'country',
        'postal_code',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function documents()
    {
        return $this->hasMany(ClientDocument::class);
    }

    public function documentTypes()
    {
        return $this->belongsToMany(DocumentType::class, 'client_documents')
            ->withPivot('document_number', 'verification_digit', 'expiration_date', 'is_primary')
            ->withTimestamps();
    }

    // Accessor para obtener el nombre completo
    public function getFullNameAttribute()
    {
        if ($this->client_type === 'business') {
            return $this->business_name;
        }

        return trim("{$this->first_name} {$this->last_name}");
    }

    // Accessor para obtener el documento principal
    public function getPrimaryDocumentAttribute()
    {
        return $this->documents()
            ->where('is_primary', true)
            ->with('documentType')
            ->first();
    }

    // Método para verificar si el cliente es persona jurídica
    public function isBusinessEntity()
    {
        return $this->client_type === 'business';
    }

    // Método para verificar si el cliente es persona física
    public function isIndividual()
    {
        return $this->client_type === 'individual';
    }

    // Método para obtener documento específico por tipo
    public function getDocumentByType($documentTypeId)
    {
        return $this->documents()
            ->where('document_type_id', $documentTypeId)
            ->first();
    }
}
