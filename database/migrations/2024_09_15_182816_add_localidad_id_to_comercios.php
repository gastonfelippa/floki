<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocalidadIdToComercios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comercios', function (Blueprint $table) {
            $table->unsignedBigInteger('localidad_id');
            $table->foreign('localidad_id')->references('id')->on('localidades')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comercios', function (Blueprint $table) {
            $table->dropColumn('localidad_id');
        });
    }
}
