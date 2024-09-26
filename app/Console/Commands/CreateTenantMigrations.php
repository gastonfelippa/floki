<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use DB;

class CreateTenantMigrations extends Command
{
    protected $signature = 'migrate:tenant {tenant}';
    protected $description = 'Run migrations specific to a tenant';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Obtener el nombre del tenant desde el argumento del comando
        $tenant = $this->argument('tenant');
     
        // Obtener la configuración del tenant desde la base de datos o configuración
        $tenantConfig = DB::table('tenants')->where('name', $tenant)->first();

        if (!$tenantConfig) {
            $this->error("Tenant $tenant not found!");
            return;
        }

        // Cambiar la configuración de la conexión a la base de datos del tenant
        config(['database.connections.tenant.database' => $tenantConfig->database_name]);
        DB::purge('tenant');
        DB::reconnect('tenant');

        // Ejecutar las migraciones desde el directorio 'tenant'
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => '/database/migrations/tenant',
            '--force' => true
        ]);

        $this->info("Migrations for tenant $tenant executed successfully.");
    }
}