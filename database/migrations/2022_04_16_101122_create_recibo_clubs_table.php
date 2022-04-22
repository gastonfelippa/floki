<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReciboClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recibo_clubs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('numero');
            $table->decimal('importe',10,2);
            $table->enum('forma_de_pago', ['1','2','3','4','5']); //efectivo,tarj débito,tarj crédito,transferencia,cheque
            $table->string('nro_comp_pago')->nullable();                     //nro ticket tarjeta o transferencia
            $table->enum('mercadopago', ['0','1'])->default('0'); //no,si
            $table->string('comentario',100)->nullable();
            $table->string('entrega',1); //se indicará '0' si es pago total, y '1' si es una entrega
            
            $table->unsignedBigInteger('socio_id');
            $table->foreign('socio_id')->references('id')->on('socios');
            
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            
            $table->unsignedBigInteger('comercio_id');
            $table->foreign('comercio_id')->references('id')->on('comercios');
            
            $table->unsignedBigInteger('arqueo_id');
            $table->foreign('arqueo_id')->references('id')->on('caja_usuarios');
            
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
        Schema::dropIfExists('recibo_clubs');
    }
}
