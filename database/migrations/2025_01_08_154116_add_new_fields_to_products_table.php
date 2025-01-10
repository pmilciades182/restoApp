<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Primero eliminamos la restricción foreign key existente
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        // Luego agregamos las nuevas columnas y la nueva restricción
        Schema::table('products', function (Blueprint $table) {
            $table->string('barcode')->unique()->nullable()->after('name');
            $table->decimal('cost', 10, 2)->after('price'); // Costo en guaraníes
            $table->boolean('is_kitchen')->default(false)->after('stock');

            // Agregamos la nueva foreign key con restrict
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Eliminamos la foreign key nueva
            $table->dropForeign(['category_id']);

            // Eliminamos las columnas agregadas
            $table->dropColumn(['barcode', 'cost', 'is_kitchen']);

            // Restauramos la foreign key original
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }
};
