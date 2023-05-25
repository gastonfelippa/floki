<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetMetodoPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('det_metodo_pagos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('recibo_id')->nullable();
            $table->foreign('recibo_id')->references('id')->on('recibos');

            $table->unsignedBigInteger('factura_id')->nullable();
            $table->foreign('factura_id')->references('id')->on('facturas');
                            //efectivo,tarj débito,tarj crédito,transferencia,cheque,cta cte
            $table->enum('medio_de_pago', ['1','2','3','4','5','6'])->nullable(); 
            $table->string('num_comp_pago')->nullable();
            $table->decimal('importe',10,2);

            $table->unsignedBigInteger('comercio_id');
            $table->foreign('comercio_id')->references('id')->on('comercios');

            $table->unsignedBigInteger('arqueo_id');
            $table->foreign('arqueo_id')->references('id')->on('caja_usuarios');

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
        Schema::dropIfExists('det_metodo_pagos');
    }
}
