<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use DB;

class CreateTenantDatabase extends Command
{
    protected $signature = 'tenant:create {name}';
    protected $description = 'Crea una nueva BD para el tenant agregado';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Obtiene el nombre del inquilino desde el argumento del comando
        $tenantName = $this->argument('name');

        // Define el nombre de la base de datos basado en el nombre del inquilino
        $databaseName = 'tenant_' . $tenantName;

        // Conexión MySQL por defecto
        $charset = config('database.connections.mysql.charset');
        $collation = config('database.connections.mysql.collation');

        try {
            // Crear la base de datos
            DB::statement("CREATE DATABASE $databaseName CHARACTER SET $charset COLLATE $collation;");
            $this->info("Database $databaseName created successfully!");

            //Ejecuta migraciones
            Artisan::call('migrate:tenant',['tenant' => $tenantName]);

            // Aquí puedes guardar los detalles del inquilino en una tabla "tenants"            
            DB::table('prueba')->insert([
                'name' => $tenantName,
                'database_name' => 'pruebaDB',
                // Añadir otros campos necesarios
            ]);

            $this->info("Tenant $tenantName registered successfully!");
        } catch (\Exception $e) {
            $this->error("Error creating database: " . $e->getMessage());
        }
    }
}
