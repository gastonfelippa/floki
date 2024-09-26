<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\ModelHasRole;
use App\Models\Comercio;
use App\Models\CondIva;
use App\Models\MovimientoDeStock;
use App\Models\Plan;
use App\Models\Proceso;
use App\Models\Provincia;
use App\Models\TipoArticulo;
use App\Models\TipoComercio;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //lista de permisos        
        Permission::create(['name' => 'Auditorias_index', 'alias' => 'Ver Auditorías']);
        Permission::create(['name' => 'Empresa_index', 'alias' => 'Ver Empresa']);
        Permission::create(['name' => 'Permisos_index', 'alias' => 'Ver Permisos']);

        Permission::create(['name' => 'Productos_index', 'alias' => 'Ver']);
        Permission::create(['name' => 'Productos_create', 'alias' => 'Agregar']);
        Permission::create(['name' => 'Productos_edit', 'alias' => 'Modificar']);
        Permission::create(['name' => 'Productos_destroy', 'alias' => 'Eliminar']);

        Permission::create(['name' => 'Categorias_index', 'alias' => 'Ver']);
        Permission::create(['name' => 'Categorias_create', 'alias' => 'Agregar']);
        Permission::create(['name' => 'Categorias_edit', 'alias' => 'Modificar']);
        Permission::create(['name' => 'Categorias_destroy', 'alias' => 'Eliminar']);

        Permission::create(['name' => 'Clientes_index', 'alias' => 'Ver']);
        Permission::create(['name' => 'Clientes_create', 'alias' => 'Agregar']);
        Permission::create(['name' => 'Clientes_edit', 'alias' => 'Modificar']);
        Permission::create(['name' => 'Clientes_destroy', 'alias' => 'Eliminar']);

        Permission::create(['name' => 'Proveedores_index', 'alias' => 'Ver']);
        Permission::create(['name' => 'Proveedores_create', 'alias' => 'Agregar']);
        Permission::create(['name' => 'Proveedores_edit', 'alias' => 'Modificar']);
        Permission::create(['name' => 'Proveedores_destroy', 'alias' => 'Eliminar']);

        Permission::create(['name' => 'Gastos_index', 'alias' => 'Ver']);
        Permission::create(['name' => 'Gastos_create', 'alias' => 'Agregar']);
        Permission::create(['name' => 'Gastos_edit', 'alias' => 'Modificar']);
        Permission::create(['name' => 'Gastos_destroy', 'alias' => 'Eliminar']);
        
        Permission::create(['name' => 'Usuarios_index', 'alias' => 'Ver']);
        Permission::create(['name' => 'Usuarios_create', 'alias' => 'Agregar']);
        Permission::create(['name' => 'Usuarios_edit', 'alias' => 'Modificar']);
        Permission::create(['name' => 'Usuarios_destroy', 'alias' => 'Eliminar']);

        Permission::create(['name' => 'Facturas_index', 'alias' => 'Crear']);
        Permission::create(['name' => 'Facturas_edit_item', 'alias' => 'Modificar']);
        Permission::create(['name' => 'Facturas_destroy_item', 'alias' => 'Eliminar']);
        Permission::create(['name' => 'Facturas_create_producto', 'alias' => 'Agregar Producto']);
        Permission::create(['name' => 'Facturas_imp', 'alias' => 'Ver Detalle/Imprimir']);
        Permission::create(['name' => 'Fact_delivery_imp', 'alias' => 'Ver Detalle/Imprimir']);

        Permission::create(['name' => 'Compras_index', 'alias' => 'Crear']);
        Permission::create(['name' => 'Compras_edit_item', 'alias' => 'Modificar']);
        Permission::create(['name' => 'Compras_destroy_item', 'alias' => 'Eliminar']);
        Permission::create(['name' => 'Compras_create_producto', 'alias' => 'Agregar Producto']);

        Permission::create(['name' => 'HabilitarCaja_index', 'alias' => 'Ver Habilitar Caja']);
        Permission::create(['name' => 'ArqueoDeCaja_index', 'alias' => 'Ver Arqueo De Caja']);
        Permission::create(['name' => 'CajaRepartidor_index', 'alias' => 'Ver Caja Repartidor']);
        Permission::create(['name' => 'MovimientosDiarios_index', 'alias' => 'Ver Movimientos Diarios']);
        
        Permission::create(['name' => 'VentasDiarias_index', 'alias' => 'Ver Ventas Diarias']);
        Permission::create(['name' => 'VentasPorFechas_index', 'alias' => 'Ver Ventas Por Fecha']);
        
        Permission::create(['name' => 'Viandas_index', 'alias' => 'Ver']);
        Permission::create(['name' => 'Ctacte_index', 'alias' => 'Ver']);
        Permission::create(['name' => 'OtroIngreso_index', 'alias' => 'Ver']);
        
        ////Permisos para Admin Floki
        Permission::create(['name' => 'Planes_index', 'alias' => 'Ver']);
        Permission::create(['name' => 'Abonados_index', 'alias' => 'Ver']);
        Permission::create(['name' => 'Procedimientos_index', 'alias' => 'Ver']);
        Permission::create(['name' => 'ModViandas', 'alias' => 'Ver']);
        
        //creamos tipos de comercio
        TipoComercio::create(['descripcion' => 'SuperAdmin']);
        TipoComercio::create(['descripcion' => 'Abonado']);
        TipoComercio::create(['descripcion' => 'Bar/Pub/Restó']);
        TipoComercio::create(['descripcion' => 'Pizzería']);
        TipoComercio::create(['descripcion' => 'Cervecería']);
        TipoComercio::create(['descripcion' => 'Heladería']);
        TipoComercio::create(['descripcion' => 'Cafetería']);
        TipoComercio::create(['descripcion' => 'Rotisería']);
        TipoComercio::create(['descripcion' => 'Panadería']);
        TipoComercio::create(['descripcion' => 'Tienda/Zapatería']);
        TipoComercio::create(['descripcion' => 'Consignación']);
        TipoComercio::create(['descripcion' => 'Club/Entidad Social']);
        TipoComercio::create(['descripcion' => 'Otro comercio gastronómico']);
        TipoComercio::create(['descripcion' => 'Otro comercio no gastronómico']);

        Plan::create([
            'descripcion' => 'PRUEBA', 
            'precio'      => '0',
            'duracion'    => '30',
            'estado'      => 'activo'
        ]);
        
 
        //creamos un comercio ficticio para poder crear el rol SuperAdmin
        // $comercio = Comercio::create(['nombre' => 'SUPERADMIN', 'tipo_id' => '1', 'hora_apertura' => null]);
        // $comercio = Comercio::create(['nombre' => 'ABONADO', 'tipo_id' => '2', 'hora_apertura' => null]);
        $comercio = Comercio::create([
            'nombre'          => 'SUPERADMIN', 
            'tipo_id'         => 1, 
        ]);
        $comercio = Comercio::create([
            'nombre'          => 'ABONADO', 
            'tipo_id'         => 2, 
        ]);

        // //lista de roles        
        $superadmin = Role::create([
            'name'        => 'SuperAdmin', 
            'alias'       => 'SuperAdmin',
            'comercio_id' => '1',
            'admite_caja' => null
        ]); 
        
        //asignación de permisos a roles
        $superadmin->givePermissionTo([   
            'Planes_index',  
            'Abonados_index',
            'Procedimientos_index'
        ]);
           
        Provincia::create(['descripcion' => 'Buenos Aires']);
        Provincia::create(['descripcion' => 'Catamarca']);
        Provincia::create(['descripcion' => 'Chaco']);
        Provincia::create(['descripcion' => 'Chubut']);
        Provincia::create(['descripcion' => 'Córdoba']);
        Provincia::create(['descripcion' => 'Corrientes']);
        Provincia::create(['descripcion' => 'Entre Ríos']);
        Provincia::create(['descripcion' => 'Formosa']);
        Provincia::create(['descripcion' => 'Jujuy']);
        Provincia::create(['descripcion' => 'La Pampa']);
        Provincia::create(['descripcion' => 'La Rioja']);
        Provincia::create(['descripcion' => 'Mendoza']);
        Provincia::create(['descripcion' => 'Misiones']);
        Provincia::create(['descripcion' => 'Neuquén']);
        Provincia::create(['descripcion' => 'Río Negro']);
        Provincia::create(['descripcion' => 'Salta']);
        Provincia::create(['descripcion' => 'San Juan']);
        Provincia::create(['descripcion' => 'San Luis']);
        Provincia::create(['descripcion' => 'Santa Cruz']);
        Provincia::create(['descripcion' => 'Santa Fe']);
        Provincia::create(['descripcion' => 'Santiago del Estero']);
        Provincia::create(['descripcion' => 'Tierra del Fuego']);
        Provincia::create(['descripcion' => 'Tucumán']); 

        CondIva::create(['descripcion' => 'S/D']);
        CondIva::create(['descripcion' => 'Resp. Monotributo']);
        CondIva::create(['descripcion' => 'Resp. Inscripto']);
        CondIva::create(['descripcion' => 'Exento']);
        CondIva::create(['descripcion' => 'IVA No Alcanzado']);

        User::create([
            'name'     => 'Gastón',
            'apellido' => 'Felippa', 
            'sexo'     => '2',
            'username' => 'admin@floki',
            'email'    => 'floki.adm@gmail.com',
            'password' => bcrypt('123floki'),
            'pass'     => '123floki',
            'abonado'  => 'Admin'
            //'email_verified_at' => Carbon::now() //comentar cuando funcione la autenticacion en la nube
        ]);     

        // $user = User::find(1);
        // $user->assignRole('SuperAdmin');
        ModelHasRole::create([
            'role_id'    => 1,
            'model_type' => 'App\Models\User',           
            'model_id'   => 1           
        ]);

        Proceso::create(['descripcion' => 'Renovación Automática De Planes', 'dia_ejecucion'=> '1']);
        Proceso::create(['descripcion' => 'Plan De Prueba Finalizado', 'dia_ejecucion'=> '1']);
        Proceso::create(['descripcion' => 'Plan Activo En Mora', 'dia_ejecucion'=> '11']);
        Proceso::create(['descripcion' => 'Plan Activo Impago', 'dia_ejecucion'=> '16']);

        MovimientoDeStock::create(['descripcion' => 'Existencia Inicial']);
        MovimientoDeStock::create(['descripcion' => 'Compra de Mercadería']);
        MovimientoDeStock::create(['descripcion' => 'Venta de Mercadería']);
        MovimientoDeStock::create(['descripcion' => 'Modificación Manual Directa']);
        MovimientoDeStock::create(['descripcion' => 'Modificación Manual Indirecta']);
        MovimientoDeStock::create(['descripcion' => 'Ingreso de Mercadería por Devolución']);
        MovimientoDeStock::create(['descripcion' => 'Egreso de Mercadería por Devolución']);
        MovimientoDeStock::create(['descripcion' => 'Venta Sin Stock']);
        MovimientoDeStock::create(['descripcion' => 'Baja de Mercadería por Mal Estado']);

        TipoArticulo::create(['descripcion' => 'Art. Compra']);
        TipoArticulo::create(['descripcion' => 'Art. Compra/Venta']);
        TipoArticulo::create(['descripcion' => 'Art. Venta c/receta']);
        TipoArticulo::create(['descripcion' => 'Art. Elaborado c/receta']);
    }
}
