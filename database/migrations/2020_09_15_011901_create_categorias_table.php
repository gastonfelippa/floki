<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->enum('tipo', ['Compra','Venta','Ambos'])->default('Venta');
            $table->string('margen_1')->default('0');
            $table->string('margen_2')->default('0');
            $table->enum('mostrar_al_vender', ['si','no'])->default('si');

            $table->unsignedBigInteger('rubro_id');
            $table->foreign('rubro_id')->references('id')->on('rubros');

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
        Schema::dropIfExists('categorias');
    }
}
