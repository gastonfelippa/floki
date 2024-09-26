<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockEnConsignacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_en_consignacion', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->foreign('cliente_id')->references('id')->on('clientes');

            $table->unsignedBigInteger('remito_id')->nullable();
            $table->foreign('remito_id')->references('id')->on('remitos');

            $table->unsignedBigInteger('factura_id')->nullable();
            $table->foreign('factura_id')->references('id')->on('facturas');

            $table->unsignedBigInteger('producto_id')->nullable();
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->decimal('cantidad',10,2);
            
            $table->unsignedBigInteger('comercio_id');
            $table->foreign('comercio_id')->references('id')->on('comercios');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_en_consignacion');
    }
}
