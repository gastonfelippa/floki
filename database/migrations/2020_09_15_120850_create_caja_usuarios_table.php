<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajaUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caja_usuarios', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('caja_id');
            $table->foreign('caja_id')->references('id')->on('cajas');

            $table->unsignedBigInteger('caja_usuario_id');     //id del cajero o repartidor
            $table->foreign('caja_usuario_id')->references('id')->on('users');

            $table->enum('estado', ['0','1']);                 //0-cerrada, 1-abierta
            $table->decimal('caja_final_sistema',10,2)->nullable();
            $table->decimal('diferencia',10,2)->nullable();    //un valor negativo indica un faltante

            $table->unsignedBigInteger('user_id');             //id de quien habilita la caja
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('arqueo_gral_id');
            $table->foreign('arqueo_gral_id')->references('id')->on('arqueo_grals');

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
        Schema::dropIfExists('caja_usuarios');
    }
}
