<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            // Información general
            $table->string('business_name')->nullable(); // Razón social para personas jurídicas
            $table->string('first_name')->nullable(); // Nombre para personas físicas
            $table->string('last_name')->nullable(); // Apellido para personas físicas
            $table->string('fantasy_name')->nullable(); // Nombre de fantasía
            $table->enum('client_type', ['individual', 'business']); // Persona física o jurídica

            // Información de contacto
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile_phone')->nullable();

            // Dirección
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable(); // Departamento
            $table->string('district')->nullable(); // Barrio
            $table->string('country')->default('Paraguay');
            $table->string('postal_code')->nullable();

            // Información adicional
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes(); // Permite soft delete de clientes
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
