<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('codigo');
            $table->string('descripcion');
            $table->decimal('precio_costo',10,2)->nullable();
            $table->decimal('precio_venta',10,2)->nullable();
            $table->unsignedBigInteger('stock')->nullable();
            $table->enum('estado', ['DISPONIBLE','SUSPENDIDO','SIN STOCK'])->default('DISPONIBLE');
            $table->enum('tipo', ['Art. Compra','Art. Venta','Ambos'])->default('Art. Venta');

            $table->unsignedBigInteger('categoria_id');
            $table->foreign('categoria_id')->references('id')->on('categorias');

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
        Schema::dropIfExists('productos');
    }
}
