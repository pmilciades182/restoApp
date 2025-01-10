<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_type_id')->constrained()->onDelete('cascade');
            $table->string('document_number');
            $table->string('verification_digit')->nullable();
            $table->date('expiration_date')->nullable();
            $table->boolean('is_primary')->default(false); // Documento principal
            $table->timestamps();

            // Índices
            $table->unique(['document_type_id', 'document_number'], 'unique_document');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_documents');
    }
};
