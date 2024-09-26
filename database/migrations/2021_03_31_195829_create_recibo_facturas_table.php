<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReciboFacturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recibo_facturas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('recibo_id')->nullable();
            $table->foreign('recibo_id')->references('id')->on('recibos');

            $table->unsignedBigInteger('factura_id')->nullable();
            $table->foreign('factura_id')->references('id')->on('facturas');           
            
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
        Schema::dropIfExists('recibo_facturas');
    }
}
