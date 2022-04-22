<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetDebitosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('det_debitos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('debito_id');
            $table->foreign('debito_id')->references('id')->on('debitos');

            $table->unsignedBigInteger('debito_generado_id')->nullable();
            $table->foreign('debito_generado_id')->references('id')->on('debitos_generados');
            
            $table->unsignedBigInteger('actividad_id')->nullable();
            $table->foreign('actividad_id')->references('id')->on('otros_debitos');

            $table->decimal('importe',10,2);

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
        Schema::dropIfExists('det_debitos');
    }
}
