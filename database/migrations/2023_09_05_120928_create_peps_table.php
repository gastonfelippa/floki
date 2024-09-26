<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peps', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('mov_stock_id');
            $table->foreign('mov_stock_id')->references('id')->on('movimiento_de_stock');

            $table->unsignedBigInteger('det_compra_id')->nullable();
            $table->foreign('det_compra_id')->references('id')->on('det_compras');

            $table->unsignedBigInteger('det_venta_id')->nullable();
            $table->foreign('det_venta_id')->references('id')->on('detfacturas');

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->decimal('cantidad',10,3)->nullable();
            $table->decimal('resto',10,3)->nullable();
            $table->decimal('costo_historico',10,2)->nullable();

            // $table->unsignedBigInteger('prod_modif_id')->nullable();
            // $table->foreign('producto_id')->references('id')->on('productos');

            $table->decimal('cant_prod_modif',10,3)->nullable();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('comercio_id');
            $table->foreign('comercio_id')->references('id')->on('comercios');
            
            $table->softDeletes();
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
        Schema::dropIfExists('peps');
    }
}
