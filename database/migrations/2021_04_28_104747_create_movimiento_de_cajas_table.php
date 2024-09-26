<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientoDeCajasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimiento_de_cajas', function (Blueprint $table) {
            $table->id();
            // $table->enum('tipo', ['1', '2']);               //1=ingreso, 2=egreso
            $table->unsignedBigInteger('ingreso_id')->nullable();
            $table->foreign('ingreso_id')->references('id')->on('otro_ingresos');

            $table->unsignedBigInteger('egreso_id')->nullable();
            $table->foreign('egreso_id')->references('id')->on('gastos');

            $table->decimal('importe', 10,2)->default(0);

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('comercio_id');
            $table->foreign('comercio_id')->references('id')->on('comercios');
            
            $table->unsignedBigInteger('arqueo_id');
            $table->foreign('arqueo_id')->references('id')->on('caja_usuarios');

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
        Schema::dropIfExists('movimiento_de_cajas');
    }
}
