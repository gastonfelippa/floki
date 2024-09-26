<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComerciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comercios', function (Blueprint $table) {
            $table->id();
            
            $table->string('nombre');

            $table->unsignedBigInteger('tipo_id');
            $table->foreign('tipo_id')->references('id')->on('tipo_comercio');

            $table->time('hora_apertura')->nullable();
            $table->string('telefono',12)->nullable();
            $table->string('email',65)->nullable();
            $table->string('calle',250)->nullable();
            $table->string('numero',20)->nullable();

            // $table->unsignedBigInteger('localidad_id');
            // $table->foreign('localidad_id')->references('id')->on('localidades')->nullable();

            $table->string('logo',45)->nullable(); 
            $table->string('leyenda_factura')->nullable();
            $table->integer('periodo_arqueo')->default(1);
            $table->boolean('venta_sin_stock')->default(false); 
            $table->enum('imp_por_hoja', ['1','2','4'])->default('1');
            $table->boolean('imp_duplicado')->default(false);
            $table->boolean('calcular_precio_de_venta')->default(false);
            $table->boolean('redondear_precio_de_venta')->default(true);
            $table->enum('opcion_de_guardado_compra', ['0','1','2'])->default('0'); 
                                            //0 no modifica nada, 1 modifica solo costos y sugeridos
                                            //2 modifica todo, costos, sugeridos y listas
            $table->enum('opcion_de_guardado_producto', ['1','2'])->default('1'); 
                                            //1 modifica solo costos y sugeridos
                                            //2 modifica todo, costos, sugeridos y listas

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
        Schema::dropIfExists('comercios');
    }
}
