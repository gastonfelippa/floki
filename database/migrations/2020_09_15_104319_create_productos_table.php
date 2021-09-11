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
            $table->decimal('precio_venta_l1',10,2)->nullable();
            $table->decimal('precio_venta_l2',10,2)->nullable();
            $table->unsignedBigInteger('stock')->nullable();
            $table->unsignedBigInteger('stock_minimo')->nullable();
            $table->enum('estado', ['DISPONIBLE','SUSPENDIDO','SIN STOCK'])->default('DISPONIBLE');
            $table->enum('tipo', ['Art. Compra','Art. Venta','Ambos'])->default('Art. Venta');

            $table->unsignedBigInteger('categoria_id');
            $table->foreign('categoria_id')->references('id')->on('categorias');

            $table->boolean('salsa')->default(false);
            $table->boolean('guarnicion')->default(false);

            $table->unsignedBigInteger('texto_base_comanda_id')->nullable();
            $table->foreign('texto_base_comanda_id')->references('id')->on('texto_base_comandas');
            
            $table->unsignedBigInteger('sectorcomanda_id')->nullable();
            $table->foreign('sectorcomanda_id')->references('id')->on('sector_comandas');

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
