<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'format',
        'requires_verification_digit'
    ];

    protected $casts = [
        'requires_verification_digit' => 'boolean'
    ];

    public function clientDocuments()
    {
        return $this->hasMany(ClientDocument::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_documents')
            ->withPivot('document_number', 'verification_digit', 'expiration_date', 'is_primary')
            ->withTimestamps();
    }
}
