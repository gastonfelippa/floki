<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modulos', function (Blueprint $table) {
            $table->id();

            $table->enum('modViandas', ['0','1'])->default('0');
            $table->enum('modComandas', ['0','1'])->default('0');
            $table->enum('modDelivery', ['0','1'])->default('0');
            $table->enum('modConsignaciones', ['0','1'])->default('0');
            $table->enum('modClubes', ['0','1'])->default('0');
            
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
        Schema::dropIfExists('modulos');
    }
}
