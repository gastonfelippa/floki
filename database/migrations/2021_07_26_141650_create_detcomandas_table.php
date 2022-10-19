<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetcomandasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detcomandas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('comanda_id');
            $table->foreign('comanda_id')->references('id')->on('comandas');

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos')->nullable();

            $table->unsignedBigInteger('subproducto_id');
            $table->foreign('subproducto_id')->references('id')->on('subproductos')->nullable();

            $table->decimal('cantidad',10,2);
            $table->string('descripcion');

            $table->unsignedBigInteger('comercio_id');
            $table->foreign('comercio_id')->references('id')->on('comercios');

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
        Schema::dropIfExists('detcomandas');
    }
}
