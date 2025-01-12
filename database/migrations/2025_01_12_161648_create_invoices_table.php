<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients');
            $table->string('invoice_number')->unique();
            $table->string('invoice_type'); // Factura, Ticket, etc.
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax', 12, 2);
            $table->decimal('total', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'cancelled', 'void'])->default('pending');
            $table->string('payment_method')->nullable(); // Efectivo, Tarjeta, etc.
            $table->text('notes')->nullable();
            $table->integer('table_number')->nullable(); // Número de mesa para restaurante
            $table->string('waiter')->nullable(); // Mesero que atendió
            $table->timestamp('paid_at')->nullable(); // Fecha y hora de pago
            $table->timestamp('cancelled_at')->nullable(); // Fecha y hora de cancelación
            $table->string('cancelled_reason')->nullable(); // Razón de cancelación
            $table->foreignId('created_by')->constrained('users'); // Usuario que creó la factura
            $table->foreignId('updated_by')->nullable()->constrained('users'); // Usuario que modificó
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
