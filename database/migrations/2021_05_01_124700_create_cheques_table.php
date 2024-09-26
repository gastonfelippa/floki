<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChequesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes')->nullable();

            $table->unsignedBigInteger('banco_id');
            $table->foreign('banco_id')->references('id')->on('bancos');
       
            $table->integer('numero');
            $table->datetime('fecha_de_emision');
            $table->datetime('fecha_de_pago');
            $table->decimal('importe',10,2);
            $table->string('cuit_titular',15);
            $table->enum('estado', ['en_cartera', 'en_caja', 'entregado', 'rechazado'])->default('en_cartera');
            $table->string('salida')->nullable();
            
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
        Schema::dropIfExists('cheques');
    }
}
