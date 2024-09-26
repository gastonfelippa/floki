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
            
            $table->unsignedBigInteger('mesa_id')->nullable();
            $table->foreign('mesa_id')->references('id')->on('mesas'); 

            $table->integer('cantidad');
            $table->date('fecha');
            $table->enum('horario', ['Desayuno','Almuerzo','Merienda','Cena']);
            $table->enum('estado', ['Pendiente','Asignada','Concretada','Cancelada']);
            $table->string('comentario')->nullable();
            $table->string('comentario_cancel')->nullable();

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
