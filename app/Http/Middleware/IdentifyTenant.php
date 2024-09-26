<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class IdentifyTenant
{
    public function handle($request, Closure $next)
    {
        // Supongamos que identificas el tenant por subdominio
        //$subdomain = $request->route('subdomain');

        // Recupera la información del tenant desde la base de datos central
        //$tenant = DB::table('tenants')->where('subdomain', $subdomain)->first();

        //Identifico al tenant por el nombre
        $name = 'cafecaro';
        $tenant = DB::table('tenants')->where('name', $name)->first();

        if ($tenant) {
            // Guarda la información del tenant en la sesión
            Session::put('tenant', $tenant);

            // Configura la conexión de base de datos para este tenant
            config(['database.connections.tenant.database' => $tenant->database_name]);
            DB::purge('tenant');
            DB::reconnect('tenant');
            config(['database.default' => 'tenant']);
        } else {
            // Redirige o maneja el caso donde no se encuentra el tenant
            abort(404, 'Tenant not found');
        }

        return $next($request);
    }
}

