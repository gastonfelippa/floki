<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrupoFamiliarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupo_familiar', function (Blueprint $table) {
            $table->id();
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
            
            $table->unsignedBigInteger('socio_id');
            $table->foreign('socio_id')->references('id')->on('socios');
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
        Schema::dropIfExists('grupo_familiar');
    }
}
