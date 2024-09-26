<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajarepartidorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cajarepartidor', function (Blueprint $table) {
            $table->id();
            $table->decimal('importe', 10,2)->default(0);
            $table->enum('tipo', ['Ingreso','Gasto'])->default('Ingreso');
            $table->enum('estado', ['Pendiente','Terminado'])->default('Pendiente');
            $table->unsignedBigInteger('gasto_id')->default(null)->nullable();
            $table->foreign('gasto_id')->references('id')->on('gastos');

            $table->unsignedBigInteger('empleado_id');
            $table->foreign('empleado_id')->references('id')->on('users');
            
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
        Schema::dropIfExists('cajareparidor');
    }
}
