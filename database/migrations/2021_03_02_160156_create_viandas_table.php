<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViandasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viandas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes');

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->enum('estado', ['activo', 'suspendido'])->default('activo');
            $table->string('comentarios')->nullable();

            $table->time('h_lunes_m')->nullable();
            $table->time('h_lunes_n')->nullable();
            $table->time('h_martes_m')->nullable();
            $table->time('h_martes_n')->nullable();
            $table->time('h_miercoles_m')->nullable();
            $table->time('h_miercoles_n')->nullable();
            $table->time('h_jueves_m')->nullable();
            $table->time('h_jueves_n')->nullable();
            $table->time('h_viernes_m')->nullable();
            $table->time('h_viernes_n')->nullable();
            $table->time('h_sabado_m')->nullable();
            $table->time('h_sabado_n')->nullable();
            $table->time('h_domingo_m')->nullable();
            $table->time('h_domingo_n')->nullable();
            $table->integer('c_lunes_m')->nullable();
            $table->integer('c_lunes_n')->nullable();
            $table->integer('c_martes_m')->nullable();
            $table->integer('c_martes_n')->nullable();
            $table->integer('c_miercoles_m')->nullable();
            $table->integer('c_miercoles_n')->nullable();
            $table->integer('c_jueves_m')->nullable();
            $table->integer('c_jueves_n')->nullable();
            $table->integer('c_viernes_m')->nullable();
            $table->integer('c_viernes_n')->nullable();
            $table->integer('c_sabado_m')->nullable();
            $table->integer('c_sabado_n')->nullable();
            $table->integer('c_domingo_m')->nullable();
            $table->integer('c_domingo_n')->nullable();

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
        Schema::dropIfExists('viandas');
    }
}
