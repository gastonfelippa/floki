<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetRecetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('det_recetas', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('receta_id');
            $table->foreign('receta_id')->references('id')->on('recetas');
            
            $table->decimal('cantidad',10,3);
            $table->enum('unidad_de_medida', ['Un','Gr','Kg','Ml','Lt', 'Mt']);
            
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->foreign('producto_id')->references('id')->on('productos');
            
            $table->enum('principal', ['si','no'])->default('no');
            
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
        Schema::dropIfExists('det_recetas');
    }
}
