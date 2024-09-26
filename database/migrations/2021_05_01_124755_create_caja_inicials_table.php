<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajaInicialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caja_inicials', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('caja_user_id');
            $table->foreign('caja_user_id')->references('id')->on('caja_usuarios');

            $table->decimal('importe',10,2);
            $table->enum('tipo', ['1','2'])->default('1');    //1 efectivo, 2 cheque
            
            $table->unsignedBigInteger('cheque_id')->nullable();
            $table->foreign('cheque_id')->references('id')->on('cheques');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('caja_inicials');
    }
}
