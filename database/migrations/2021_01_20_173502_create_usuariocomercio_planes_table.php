<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuarioComercioPlanesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuariocomercio_planes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('usuariocomercio_id')->nullable();
            $table->foreign('usuariocomercio_id')->references('id')->on('usuario_comercio');

            $table->unsignedBigInteger('plan_id');
            $table->foreign('plan_id')->references('id')->on('planes');

            $table->enum('estado_plan', ['activo', 'suspendido', 'finalizado', 'notificado'])->default('activo');
            $table->decimal('importe', 10,2);
            $table->enum('estado_pago', ['pagado', 'en mora', 'no corresponde', 
                                         'suspendido', 'no vencido'])->default('no vencido');
            
            $table->unsignedBigInteger('comercio_id');
            $table->foreign('comercio_id')->references('id')->on('comercios');
            
            $table->datetime('fecha_inicio_periodo');
            $table->datetime('fecha_fin');
            $table->datetime('fecha_vto');
            $table->text('comentarios')->nullable();

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
        Schema::dropIfExists('usuario_comercio_planes');
    }
}
