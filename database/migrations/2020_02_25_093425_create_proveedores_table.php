<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_empresa');
            $table->string('tel_empresa',15)->nullable();

            $table->unsignedBigInteger('condiva_id')->nullable();
            $table->foreign('condiva_id')->references('id')->on('cond_iva');

            $table->string('cuit',15)->nullable();
            $table->string('calle')->nullable();
            $table->string('numero')->nullable();

            $table->unsignedBigInteger('localidad_id')->nullable();
            $table->foreign('localidad_id')->references('id')->on('localidades');

            $table->string('nombre_contacto')->nullable();
            $table->string('apellido_contacto')->nullable();
            $table->string('tel_contacto',15)->nullable();
            
            $table->unsignedBigInteger('categoria_id');
            $table->foreign('categoria_id')->references('id')->on('categoria_gastos');

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
        Schema::dropIfExists('proveedores');
    }
}
