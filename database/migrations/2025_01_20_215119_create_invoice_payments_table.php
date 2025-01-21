<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Crear tabla para métodos de pago
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('requires_reference')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Crear tabla para los pagos de facturas
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained();
            $table->foreignId('cash_register_id')->nullable()->constrained();
            $table->decimal('amount', 12, 2);
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Insertar métodos de pago iniciales
        DB::table('payment_methods')->insert([
            [
                'name' => 'Efectivo',
                'code' => 'cash',
                'requires_reference' => false,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Tarjeta de Crédito',
                'code' => 'credit_card',
                'requires_reference' => true,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Tarjeta de Débito',
                'code' => 'debit_card',
                'requires_reference' => true,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Transferencia',
                'code' => 'transfer',
                'requires_reference' => true,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('invoice_payments');
        Schema::dropIfExists('payment_methods');
    }
};
