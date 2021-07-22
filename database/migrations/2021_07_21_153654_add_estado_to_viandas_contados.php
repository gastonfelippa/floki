<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoToViandasContados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('viandas_contados', function (Blueprint $table) {
            $table->enum('estado', ['Cobrada','Cta Cte', 'Anulada']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('viandas_contados', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
}
