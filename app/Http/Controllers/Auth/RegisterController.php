<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Spatie\Permission\Models\Role;
use App\Models\Comercio;
use App\Models\Cliente;
use App\Models\Localidad;
use App\Models\Mesa;
use App\Models\ModelHasRole;
use App\Models\Modulo;
use App\Models\Plan;
use App\Models\Sector;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UsuarioComercio;
use App\Models\UsuarioComercioPlanes;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Illuminate\Support\Facades\Artisan;

//use App\Events\UserRegistered;
use Illuminate\Mail\Mailable;

use App\Mail\NuevoAbonado;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public $comercio, $nombre, $comercioId, $user;

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {     
        return Validator::make($data, [
            'name'           => ['required', 'string', 'max:255'],
            'apellido'       => ['required', 'string', 'max:255'],
            'nombreComercio' => ['required', 'string', 'max:255','unique:comercios,nombre'],
            'sexo'           => ['required', 'not_in:0'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */ 
    protected function create(array $data)
    {
        //genera un password random de 8 caracteres y crea una sesion con ese password
        //................descomentar cuando funcione la autenticacion en la nube..........
        $password = Str::random(8);
        //................comentar cuando funcione la autenticacion en la nube..........
        //$password = Str::finish('123', strtolower($data['name']));
        //....................................

        session(['pass'     => $password]);
        session(['empleado' => false]);

        $cadena = strtolower($data['nombreComercio']);
        $username = str_replace(' ', '',Str::finish('admin@', $cadena));     
        
        DB::begintransaction();                 //iniciar transacción para grabar
        try{    
            $comercio = Comercio::create([
                'nombre'          => mb_strtoupper($data['nombreComercio']),            
                'tipo_id'         => $data['tipo'],
                'hora_apertura'   => '08:00:00',
                'email'           => strtolower($data['email']),
                'periodo_arqueo'  => 1            
            ]);
            $this->comercio = $comercio->nombre;
            $this->comercioId = $comercio->id;
                
            $user = User::create([            
                'name'     => ucwords($data['name']),
                'apellido' => ucwords($data['apellido']),
                'sexo'     => $data['sexo'],
                'username' => $username,
                'email'    => strtolower($data['email']),
                'password' => Hash::make($password),
                'pass'     => $password,
                'abonado'  => 'Si'
                //'email_verified_at' => Carbon::now()    //comentar cuando funcione la autenticacion en la nube
            ]);
            $this->user=$user->id;

            $userAdminComercio = UsuarioComercio::create([
                'usuario_id'  => $this->user,            
                'comercio_id' => $this->comercioId            
            ]);

            //creo un usuario/repartidor Salón para las ventas en el local
            $userRepartidor = User::create([            
                'name'     => '...',
                'apellido' => 'Salón',
                'sexo'     => null,
                'abonado'  => 'No'
            ]);                
            UsuarioComercio::create([
                'usuario_id'  => $userRepartidor->id,            
                'comercio_id' => $this->comercioId            
            ]);

            //creo un cliente llamado Consumidor Final, pero primero debo agregar una localidad
            $localidad = Localidad::create([            
                'descripcion'  => '...',            
                'provincia_id' => 5,     
                'comercio_id'  => $this->comercioId   
            ]);  
            $cliente = Cliente::create([            
                'nombre'       => 'FINAL',            
                'apellido'     => 'CONSUMIDOR',     
                'calle'        => '...',
                'localidad_id' => $localidad->id,
                'comercio_id'  => $this->comercioId   
            ]); 
            
            //asigno los módulos básicos
            if($data['tipo'] == "3" || $data['tipo'] == "8"){  //si es un bar... o rotisería
                $modulos = Modulo::create([
                    'modViandas'  => '1',
                    'modComandas' => '1',
                    'modDelivery' => '1',
                    'comercio_id' => $this->comercioId
                ]);
            }elseif($data['tipo'] == "4" || $data['tipo'] == "5" || $data['tipo'] == "6" || $data['tipo'] == "7"){  
                $modulos = Modulo::create([ //si es una pizzería, cervecería, heladería o cafetería
                    'modComandas' => '1',
                    'modDelivery' => '1',
                    'comercio_id' => $this->comercioId
                ]);
            }elseif($data['tipo'] == "10" || $data['tipo'] == "11"){  //si es una tienda o una consignación
                $modulos = Modulo::create([
                    'modConsignaciones' => '1',
                    'comercio_id'       => $this->comercioId
                ]);
            }elseif($data['tipo'] == "12"){       //si es un Club
                $modulos = Modulo::create([
                    'modClubes'   => '1',
                    'comercio_id' => $this->comercioId
                ]);
            }else{
                $modulos = Modulo::create([
                    'modDelivery' => '1',
                    'comercio_id' => $this->comercioId
                ]);
            }
            //creo los sectores para las mesas
            $sectorMesa = Sector::create([
                'descripcion' => 'Interior',
                'comercio_id' => $this->comercioId
            ]);                                   
            $sectorMesa = Sector::create([
                'descripcion' => 'Exterior',
                'comercio_id' => $this->comercioId
            ]); 
  
            if($data['tipo'] == "3"){  //si es un bar, cargo 5 mesas
                for ($i=1; $i < 6; $i++) { 
                    Mesa::create([
                        'descripcion' => $i,
                        'capacidad'   => 2,
                        'estado'      => 'Disponible',
                        'sector_id'   => '1',
                        'comercio_id' => $this->comercioId
                    ]);
                }                
            }

            if($data['tipo'] == "12"){  //si es un club, creo el rol Cobrador
                $rolAdmin = Role::create([
                    'name'        => 'Cobrador'. $this->comercioId,
                    'alias'       => 'Cobrador',
                    'comercio_id' => $this->comercioId,
                    'admite_caja' => null        
                ]);
            }          

            //creo los roles Admin, No Usuario, Encargado, Cajero y Repartidor            
            $rolAdmin = Role::create([
                'name'        => 'Admin'. $this->comercioId,
                'alias'       => 'Administrador',
                'comercio_id' => $this->comercioId,
                'admite_caja' => '1'        
            ]);    
            $rolNoUsuario = Role::create([
                'name'        => 'No Usuario'. $this->comercioId,
                'alias'       => 'No Usuario',
                'comercio_id' => $this->comercioId,
                'admite_caja' => null         
            ]);
            $rolEncargado = Role::create([
                'name'        => 'Encargado'. $this->comercioId,
                'alias'       => 'Encargado',
                'comercio_id' => $this->comercioId,
                'admite_caja' => '1'         
            ]);
            $rolCajero = Role::create([
                'name'        => 'Cajero'. $this->comercioId,
                'alias'       => 'Cajero',
                'comercio_id' => $this->comercioId,
                'admite_caja' => '1'         
            ]);
            $rolRepartidor = Role::create([
                'name'        => 'Repartidor'. $this->comercioId,
                'alias'       => 'Repartidor',
                'comercio_id' => $this->comercioId,
                'admite_caja' => '1'         
            ]);
          
            //Asigno el rol Admin al nuevo Usuario
            ModelHasRole::create([
                'role_id'    => $rolAdmin->id,
                'model_type' => 'App\Models\User',           
                'model_id'   => $user->id           
            ]);
            // // Asigno el rol Repartidor al usuario Salón
            // ModelHasRole::create([
            //     'role_id'    => $rolRepartidor->id,
            //     'model_type' => 'App\Models\User',           
            //     'model_id'   => $userRepartidor->id           
            // ]);
               
            //asigno permisos al rol Admin
            $rolAdmin->givePermissionTo([
                'Auditorias_index','Empresa_index','Permisos_index',
                'Productos_index','Productos_create','Productos_edit','Productos_destroy',
                'Categorias_index','Categorias_create','Categorias_edit','Categorias_destroy',
                'Clientes_index','Clientes_create','Clientes_edit','Clientes_destroy', 
                'Proveedores_index','Proveedores_create','Proveedores_edit','Proveedores_destroy',
                'Gastos_index','Gastos_create','Gastos_edit','Gastos_destroy',
                'Usuarios_index','Usuarios_create','Usuarios_edit','Usuarios_destroy',
                'Facturas_index','Facturas_create_producto','Facturas_edit_item','Facturas_destroy_item',
                'Facturas_imp','Fact_delivery_imp',
                'Compras_index','Compras_create_producto','Compras_edit_item','Compras_destroy_item',
                'HabilitarCaja_index','ArqueoDeCaja_index','CajaRepartidor_index','MovimientosDiarios_index',                
                'VentasDiarias_index','VentasPorFechas_index',                                
                'Ctacte_index','OtroIngreso_index',
                'Viandas_index','Ctacte_index','OtroIngreso_index'           
            ]);                                    
            //asigno permisos al rol Encargado
            $rolEncargado->givePermissionTo([
                'Productos_index','Productos_create','Productos_edit','Productos_destroy',
                'Categorias_index',
                'Clientes_index','Clientes_create','Clientes_edit','Clientes_destroy', 
                'Proveedores_index','Proveedores_create','Proveedores_edit','Proveedores_destroy',
                'Gastos_index',
                'Usuarios_index',
                'Facturas_index','Facturas_create_producto','Facturas_edit_item','Facturas_destroy_item',
                'Facturas_imp','Fact_delivery_imp',
                'Compras_index','Compras_create_producto','Compras_edit_item','Compras_destroy_item',
                'HabilitarCaja_index','ArqueoDeCaja_index','CajaRepartidor_index','MovimientosDiarios_index',                
                'VentasDiarias_index',
                'Viandas_index','Ctacte_index','OtroIngreso_index'           
            ]);                                    
            //asigno permisos al rol Cajero
            $rolCajero->givePermissionTo([
                'Clientes_index','Clientes_create','Clientes_edit',
                'Facturas_index','Facturas_edit_item','Facturas_destroy_item',
                'Facturas_imp','Fact_delivery_imp',
                'ArqueoDeCaja_index','MovimientosDiarios_index'               
            ]);                                    
            //asigno permisos al rol Repartidor
            $rolRepartidor->givePermissionTo([
                'Clientes_index',
                'Facturas_imp','Fact_delivery_imp',
                'CajaRepartidor_index'               
            ]);                                            
                                  
            $plan = Plan::select('*')->where('id', '1')->get(); 
                                    
            $fecha_inicio = Carbon::now()->locale('en');      //inicializo fecha_inicio con la fecha en que se suscribe al sistema
            $mes = $fecha_inicio->monthName;                  //recupero el mes
            Carbon::setTestNow($fecha_inicio);                //habilito a Carbon para que actúe sobre fecha_inicio
            $fecha_fin = new Carbon('last day of ' . $mes);   //inicializo fecha_fin con el último día del mes en curso
            $diferencia = $fecha_inicio->diffInDays($fecha_fin); //efectúo la diferencia entre fechas para saber los días que las separan
                                    
            if($diferencia < 15)                                 //si son menos de 15 días
            {                                  
                $fecha_fin = Carbon::now()->addMonthsNoOverflow(1)->locale('en'); //agrego un mes a fecha_fin a partir del corriente mes
                $mes = $fecha_fin->monthName;                    //recupero el mes
                Carbon::setTestNow($fecha_fin);                  //habilito a Carbon para que actúe sobre fecha_fin
                $fecha_fin = new Carbon('last day of ' . $mes);  //modifico fecha_fin con el último día del mes siguiente
            }
            Carbon::setTestNow();               //IMPORTANTE: resetea la fecha actual para grabarla en create_at y update_at
            
            UsuarioComercioPlanes::create([
                'usuariocomercio_id'   => $userAdminComercio->id,
                'plan_id'              => $plan[0]->id,
                'estado_plan'          => 'activo',
                'importe'              => $plan[0]->precio,
                'estado_pago'          => 'no corresponde',
                'comercio_id'          => $this->comercioId,
                'fecha_inicio_periodo' => Carbon::parse($fecha_inicio)->format('Y,m,d') . ' 00:00:00',
                'fecha_fin'            => Carbon::parse($fecha_fin)->format('Y,m,d') . ' 23:59:59',
                'fecha_vto'            => Carbon::parse($fecha_fin)->format('Y,m,d') . ' 23:59:59',
                'comentarios'          => 'Inicio plan de prueba'
            ]);
            
            /////////TENANT/////////
            // Obtiene el nombre del inquilino desde el argumento del comando           
            $tenantName = strtolower(str_replace(' ', '',$this->comercio));

            // Define el nombre de la base de datos basado en el nombre del inquilino
            $databaseName = 'tenant_' . $tenantName;

            $tenant = Tenant::create([
                'name' => $tenantName,
                'database_name' => $databaseName
            ]);
            ////////////////////////////

            DB::commit();

            $email = config('mail.from.address');
            $this->sendEmail($email, $user, $this->comercio);  //descomentar cuando funcione la autenticacion en la nube
            
            //CREA UNA BD PARA EL TENANT
            $name = strtolower(str_replace(' ', '',$this->comercio));
            Artisan::call('tenant:create', ['name' => $name]);
            ////////////////////////////

            return $user;
        }catch (Exception $e){
            DB::rollback();    //en caso de error, deshacemos para no generar inconsistencia de datos  
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }     
    }

    public function sendEmail($email, $user, $comercio)
    {         
        Mail::to($email)->send(new NuevoAbonado($user, $comercio));
    }
}