<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->string('apellido');
            $table->string('telefono',15)->nullable();
            
            $table->unsignedBigInteger('mesa_id');
            $table->foreign('mesa_id')->references('id')->on('mesas'); 

            $table->integer('cantidad');
            $table->datetime('hora')->nullable();
            $table->string('comentario')->nullable();

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
        Schema::dropIfExists('reservas');
    }
}
