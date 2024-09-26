<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->string('letra')->nullable();
            $table->unsignedBigInteger('sucursal')->nullable();
            $table->unsignedBigInteger('num_fact')->nullable();

            $table->unsignedBigInteger('proveedor_id')->nullable();
            $table->foreign('proveedor_id')->references('id')->on('proveedores');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
         
            $table->decimal('importe',10,2);
            $table->enum('estado', ['abierta','contado', 'pendiente', 'ctacte', 'anulado']);
            $table->enum('estado_pago', ['0','1','2']);           //ctacte,pagado,entrega
            $table->enum('forma_de_pago', ['1','2','3','4','5'])->nullable(); //efectivo,tarj débito,tarj crédito,transferencia,cheque
            $table->enum('mercadopago', ['0','1'])->nullable(); //no,si
            $table->string('nro_comp_pago')->nullable();         //nro ticket tarjeta o transferencia 

            $table->unsignedBigInteger('comercio_id');
            $table->foreign('comercio_id')->references('id')->on('comercios');

            $table->datetime('fecha_fact')->nullable();
         
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
        Schema::dropIfExists('compras');
    }
}
