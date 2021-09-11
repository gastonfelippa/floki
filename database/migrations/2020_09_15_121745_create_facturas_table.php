<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();            
            $table->unsignedBigInteger('numero');

            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->foreign('cliente_id')->references('id')->on('clientes');

            $table->unsignedBigInteger('repartidor_id')->nullable();
            $table->foreign('repartidor_id')->references('id')->on('users');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
         
            $table->decimal('importe',10,2);
            $table->enum('estado', ['abierta','contado', 'pendiente', 'ctacte', 'anulado']);
            $table->enum('estado_pago', ['0','1','2']);           //ctacte,pagado,entrega
            $table->enum('forma_de_pago', ['1','2','3','4','5'])->nullable(); //efectivo,tarj débito,tarj crédito,transferencia,cheque
            $table->enum('mercadopago', ['0','1'])->nullable(); //no,si
            $table->string('nro_comp_pago')->nullable();         //nro ticket tarjeta o transferencia 
            $table->enum('estado_entrega', ['0','1','2','3']);    //no delivery,delivery o en espera,en camino,entregado
            $table->enum('lista', ['1','2','3']);      //nro de lista para buscar precios de venta

            $table->unsignedBigInteger('mesa_id')->nullable();
            $table->foreign('mesa_id')->references('id')->on('mesas');

            $table->unsignedBigInteger('mozo_id')->nullable();
            $table->foreign('mozo_id')->references('id')->on('users');

            $table->unsignedBigInteger('user_id_delete')->nullable();
            $table->foreign('user_id_delete')->references('id')->on('users');

            $table->string('comentario',100)->nullable();

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
        Schema::dropIfExists('facturas');
    }
}
