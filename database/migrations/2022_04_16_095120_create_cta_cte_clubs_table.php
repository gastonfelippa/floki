<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCtaCteClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cta_cte_clubs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('socio_id');
            $table->foreign('socio_id')->references('id')->on('socios');

            $table->unsignedBigInteger('debito_id')->nullable();
            $table->foreign('debito_id')->references('id')->on('debitos');

            $table->unsignedBigInteger('recibo_id')->nullable();
            $table->foreign('recibo_id')->references('id')->on('recibos');

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
        Schema::dropIfExists('cta_cte_clubs');
    }
}
