<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2); // Precio unitario al momento de la venta
            $table->decimal('subtotal', 12, 2); // Cantidad * precio unitario
            $table->decimal('tax', 12, 2); // Impuesto por ítem
            $table->decimal('total', 12, 2); // Subtotal + impuesto
            $table->decimal('discount', 12, 2)->default(0); // Descuento por ítem
            $table->text('notes')->nullable(); // Notas especiales (ej: "sin cebolla")
            $table->enum('status', ['pending', 'preparing', 'ready', 'delivered', 'cancelled'])->default('pending');
            $table->timestamp('prepared_at')->nullable(); // Fecha y hora de preparación
            $table->timestamp('delivered_at')->nullable(); // Fecha y hora de entrega
            $table->string('cancelled_reason')->nullable(); // Razón de cancelación del ítem
            $table->foreignId('created_by')->constrained('users'); // Usuario que agregó el ítem
            $table->foreignId('updated_by')->nullable()->constrained('users'); // Usuario que modificó
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_details');
    }
};
