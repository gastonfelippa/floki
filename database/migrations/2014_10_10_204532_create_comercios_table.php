<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComerciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comercios', function (Blueprint $table) {
            $table->id();
            
            $table->string('nombre');

            $table->unsignedBigInteger('tipo_id');
            $table->foreign('tipo_id')->references('id')->on('tipo_comercio');

            $table->time('hora_apertura')->nullable();
            $table->string('telefono',12)->nullable();
            $table->string('email',65)->nullable();
            $table->string('direccion',250)->nullable();
            $table->string('logo',45)->nullable(); 
            $table->string('leyenda_factura')->nullable();
            $table->integer('periodo_arqueo')->default(1);
            $table->enum('imp_por_hoja', ['1','2','4'])->default('1');
            $table->boolean('imp_duplicado')->default(false);

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
        Schema::dropIfExists('comercios');
    }
}
