<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('apellido');
            $table->string('documento',15)->nullable();
            $table->string('calle')->nullable();
            $table->string('numero',10)->nullable();

            $table->unsignedBigInteger('localidad_id')->nullable();
            $table->foreign('localidad_id')->references('id')->on('localidades');

            $table->string('telefono1',15)->nullable();
            $table->string('telefono2',15)->nullable();
            $table->enum('sexo', ['1', '2'])->nullable();
            $table->enum('abonado', ['Si', 'No', 'Admin'])->default('No');
            $table->string('username')->unique()->nullable();
            $table->string('pass')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->datetime('fecha_ingreso')->nullable();
            $table->datetime('fecha_nac')->nullable();
            $table->enum('estado', ['Activo','Suspendido', 'Baja'])->default('Activo')->nullable();

            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->foreign('categoria_id')->references('id')->on('categoria_gastos');

            $table->unsignedBigInteger('comercio_id')->nullable();
            $table->foreign('comercio_id')->references('id')->on('comercios');

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
