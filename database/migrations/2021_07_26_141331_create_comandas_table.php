<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComandasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comandas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('factura_id');
            $table->foreign('factura_id')->references('id')->on('facturas');

            $table->enum('estado', ['cargado','en espera', 'procesando', 'terminado']);

            $table->unsignedBigInteger('sectorcomanda_id');
            $table->foreign('sectorcomanda_id')->references('id')->on('sector_comandas');

            $table->datetime('sent_at')->nullable();
            $table->datetime('finished_at')->nullable();
            
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
        Schema::dropIfExists('comandas');
    }
}
