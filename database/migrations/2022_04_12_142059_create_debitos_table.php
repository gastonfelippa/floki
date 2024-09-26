<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebitosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debitos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('numero');

            $table->unsignedBigInteger('socio_id')->nullable();
            $table->foreign('socio_id')->references('id')->on('socios');

            $table->decimal('importe',10,2);
            $table->enum('estado', ['pendiente', 'ctacte', 'anulado']);
            $table->enum('estado_pago', ['0','1','2']);           //ctacte,pagado,entrega

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
        Schema::dropIfExists('debitos');
    }
}
