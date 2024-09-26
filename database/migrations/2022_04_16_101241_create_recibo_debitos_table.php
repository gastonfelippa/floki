<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReciboDebitosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recibo_debitos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('recibo_club_id')->nullable();
            $table->foreign('recibo_club_id')->references('id')->on('recibo_clubs');

            $table->unsignedBigInteger('debito_id')->nullable();
            $table->foreign('debito_id')->references('id')->on('debitos');           
            
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
        Schema::dropIfExists('recibo_debitos');
    }
}
