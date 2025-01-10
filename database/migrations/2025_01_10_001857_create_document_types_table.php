<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // CI, RUC, Pasaporte, DNI, etc.
            $table->string('description')->nullable();
            $table->string('format')->nullable(); // Formato esperado del documento
            $table->boolean('requires_verification_digit')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
