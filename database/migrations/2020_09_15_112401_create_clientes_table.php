<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('calle');
            $table->string('numero')->nullable();

            $table->unsignedBigInteger('localidad_id')->nullable();
            $table->foreign('localidad_id')->references('id')->on('localidades');
            
            $table->string('telefono',15)->nullable();
            $table->enum('vianda', ['1','2'])->nullable();
            $table->enum('consignatario', ['0','1'])->default('0');//0 común, 1 consignatario
            $table->enum('saldo', ['0','1'])->nullable();     //0 si no debe nada 1 si debe
            
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
        Schema::dropIfExists('clientes');
    }
}
