<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSociosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('socios', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['1','2'])->default('1');

            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->foreign('categoria_id')->references('id')->on('categoria_club');

            $table->string('nombre');
            $table->string('apellido');
            $table->string('calle');
            $table->string('numero')->nullable();

            $table->unsignedBigInteger('localidad_id')->nullable();
            $table->foreign('localidad_id')->references('id')->on('localidades');
            
            $table->string('telefono',15)->nullable();
            $table->string('email')->nullable();
            $table->string('documento',10)->nullable();
            $table->datetime('fecha_nac')->nullable();
            $table->datetime('fecha_alta')->nullable();
            $table->datetime('fecha_baja')->nullable();
            
            $table->unsignedBigInteger('cobrador_id');
            $table->foreign('cobrador_id')->references('id')->on('users');

            $table->string('cobrar_en')->nullable();
            $table->string('comentario')->nullable();
            
            $table->enum('grupo_familiar', ['1','2']);
            $table->enum('estado', ['Activo','Suspendido','Baja'])->default('Activo');
            
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
        Schema::dropIfExists('socios');
    }
}
