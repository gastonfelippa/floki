<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetfacturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detfacturas', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('factura_id');
            $table->foreign('factura_id')->references('id')->on('facturas');

            $table->unsignedBigInteger('producto_id')->nullable();
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->decimal('cantidad',10,2);
            $table->decimal('precio',10,2);
            $table->decimal('costo',10,2);
            
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
        Schema::dropIfExists('detfacturas');
    }
}
