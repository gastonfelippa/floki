<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistroProcesosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registro_procesos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('proceso_id');
            $table->foreign('proceso_id')->references('id')->on('procesos');

            $table->integer('cambios');
            
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
        Schema::dropIfExists('registro_procesos');
    }
}
