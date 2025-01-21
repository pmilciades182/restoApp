<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Agregar referencia a la caja donde se realizó la venta
            $table->foreignId('cash_register_id')
                  ->nullable()
                  ->after('client_id')
                  ->constrained('cash_registers')
                  ->onDelete('restrict');

            // Campos para manejar el balance y estado de pago
            $table->decimal('amount_paid', 12, 2)
                  ->default(0)
                  ->after('total')
                  ->comment('Monto total pagado');

            $table->decimal('balance', 12, 2)
                  ->default(0)
                  ->after('amount_paid')
                  ->comment('Balance pendiente');

            // Campo para manejar el estado de pago más detallado
            $table->enum('payment_status', ['unpaid', 'partially_paid', 'paid', 'overpaid'])
                  ->default('unpaid')
                  ->after('status');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['cash_register_id']);
            $table->dropColumn([
                'cash_register_id',
                'amount_paid',
                'balance',
                'payment_status'
            ]);
        });
    }
};
