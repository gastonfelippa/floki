<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViandasContadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viandas_contados', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('factura_id');
            $table->foreign('factura_id')->references('id')->on('facturas'); 

            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes');

            $table->unsignedBigInteger('arqueo_gral_id');
            $table->foreign('arqueo_gral_id')->references('id')->on('arqueo_grals');

            $table->enum('estado', ['Cobrada','Cta Cte', 'Anulada']);

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
        Schema::dropIfExists('viandas_contados');
    }
}
