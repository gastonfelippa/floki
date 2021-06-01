<?php

namespace App\Http\Livewire;

use App\Providers\RouteServiceProvider;

use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Models\Comercio;
use App\Models\Empleado;
use App\Models\Localidad;
use App\Models\ModelHasRole;
use App\Models\Provincia;
use App\Models\Role;
use App\Models\TipoComercio;
use App\Models\User;
use App\Models\UsuarioComercio;
use Carbon\Carbon;
use DB;

use App\Events\UserRegistered;
use Illuminate\Mail\Mailable;

use App\Mail\WelcomeUser;
use Illuminate\Support\Facades\Mail;


class UsuarioController extends Component
{
    use RegistersUsers;
    
    public $name, $apellido, $documento, $calle, $numero, $localidad = 'Elegir', $provincia = 'Elegir';
    public $sexo = 0, $telefono1, $fecha_ingreso, $fecha_nac, $email, $username, $password;
    public $selected_id = null, $search, $action = 1;
    public $comercioId;
    public $empleado, $rol;
    public $empleados, $roles;
    public $comercio, $admin;

    public function render()
    {
         //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');

        $localidades = Localidad::select()->orderBy('descripcion','asc')->get();
        $provincias = Provincia::all();
 
        $this->empleados = Empleado::select('*')->where('comercio_id', $this->comercioId)->get();
        $this->roles = Role::select('*')->where('id', '<>', '1')->where('comercio_id', $this->comercioId)->get();
      
        //busca el nombre del comercio logueado y de su admin para el envío de emails
        $comercio = Comercio::select('nombre')
        ->where('id', $this->comercioId)->first(); 
        $this->comercio = $comercio->nombre; 

        $admin = User::join('usuario_comercio as uc', 'uc.usuario_id','users.id')
            ->where('uc.comercio_id', $this->comercioId)
            ->select('users.name', 'users.sexo')
            ->orderBy('users.id', 'asc')->first();
        $this->admin = $admin->name; 
     
        if(strlen($this->search) > 0)
        {
            $info = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
                ->join('model_has_roles as mhr', 'mhr.model_id', 'users.id')
                ->join('roles', 'roles.id', 'mhr.role_id')
                ->where('users.name', 'like', '%'. $this->search . '%')
                ->where('uc.comercio_id', $this->comercioId)
                ->orwhere('users.apellido', 'like', '%'. $this->search . '%')
                ->where('uc.comercio_id', $this->comercioId)
                ->orwhere('roles.alias', 'like', '%'. $this->search . '%')
                ->where('uc.comercio_id', $this->comercioId)
                ->select('users.*', 'roles.alias')->get();
        }
        else
        {
            $info = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
                ->join('model_has_roles as mhr', 'mhr.model_id', 'users.id')
                ->join('roles', 'roles.id', 'mhr.role_id')
                ->where('uc.comercio_id', $this->comercioId)
                ->select('users.*', 'roles.alias')->get();
        }         
        return view('livewire.usuarios.component', [
            'info' =>$info,
            'localidades' => $localidades,
            'provincias' => $provincias
        ]);
    }
    
    public function doAction($action)
    {
        $this->resetInput();
        $this->action = $action;
    }
    
    public function resetInput()
    {
        $this->name = '';
        $this->apellido = '';
        $this->documento = '';
        $this->calle = '';
        $this->numero = '';
        $this->localidad = 'Elegir';
        $this->telefono1 = '';
        $this->fecha_nac = '';
        $this->fecha_ingreso = '';
        $this->sexo = 0;
        $this->email = '';
        $this->password = '';
        $this->selected_id = null;
        $this->action = 1;
        $this->search = '';
    }
    
    public $listeners = [
        'deleteRow' => 'destroy',
        'createFromModal' => 'createFromModal'        
	];

    public function createFromModal($info)
    {
        $data = json_decode($info);

        $existe = Localidad::where('descripcion', ucwords($data->localidad))
            ->where('provincia_id', $data->provincia_id)
            ->where('comercio_id', $this->comercioId)->get();  
        if($existe->count() > 0 ) {
            session()->flash('info', 'La Localidad ingresada ya existe!!!');
            return;
        }else{
            DB::begintransaction();
            try{   
                Localidad::create([
                    'descripcion' => ucwords($data->localidad),
                    'provincia_id' => $data->provincia_id,
                    'comercio_id' => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Localidad creada exitosamente!!!'); 
                DB::commit();               
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se creó...');
            }
        }
    }
    
    public function edit($id)
    {
        $record = User::find($id);
        $this->name = $record->name;
        $this->apellido = $record->apellido;
        $this->documento = $record->documento;
        $this->calle = $record->calle;
        $this->numero = $record->numero;
        $this->localidad = $record->localidad_id;
        $this->telefono1 = $record->telefono1;
        $this->fecha_ingreso = Carbon::parse($record->fecha_ingreso)->format('d-m-Y');
        $this->fecha_nac = Carbon::parse($record->fecha_nac)->format('d-m-Y');
        $this->sexo = $record->sexo;
        $this->email = $record->email;
        $this->selected_id = $record->id;

        $this->action = 2;
    }

    public function StoreOrUpdate()
    {
        //genera un password random de 8 caracteres y crea una sesion con ese password
        $password = Str::random(8);
        session(['pass_empleado' => $password]);
        session(['empleado' => 'si']);

        $nombre = strtolower($this->name);
        $cadena = Comercio::select('nombre')->where('id', $this->comercioId)->first();
        $cadena = strtolower($cadena->nombre);
        $username = str_replace(' ', '',Str::finish($nombre,'@'. $cadena));
        
        //si existe el username, le agregamos un número al nombre
        $existe = User::where('username', $username);
        $i=2;
        if($existe->count() > 0){
            do{
                $username = str_replace(' ', '',Str::finish($nombre, $i .'@'. $cadena));
                $existe = User::where('username', $username);
                $i ++;
            }while($existe->count() > 0 );            
        }
        $this->username = $username; 
        //busco el id del rol No Usuario para agregarlo por defecto al nuevo usuario
        $rolNoUsuario = Role::where('alias', 'No Usuario')
                    ->where('comercio_id', $this->comercioId)
                    ->select('id')->get();
        
        $this->validate([
			'sexo' => 'not_in:0',
			'localidad' => 'not_in:Elegir'
		]);
        
        $this->validate([
            'name' => 'required',
            'apellido' => 'required'
            ]);  

        if($this->numero == '' && $this->calle != '') $this->numero = 's/n';       
        
        DB::begintransaction();                 //iniciar transacción para grabar
        try{       
            if($this->selected_id <= 0)
            {
                $user = User::create([
                    'name' => ucwords($this->name),
                    'apellido' => ucwords($this->apellido),
                    'documento' => $this->documento,
                    'calle' => ucwords($this->calle),
                    'numero' => $this->numero,
                    'localidad_id' => $this->localidad,
                    'telefono1' => $this->telefono1,
                    'fecha_ingreso' => Carbon::parse($this->fecha_ingreso)->format('Y,m,d h:i:s'),             
                    'fecha_nac' => Carbon::parse($this->fecha_nac)->format('Y,m,d h:i:s'),
                    'sexo' => $this->sexo,
                    'email' => strtolower($this->email),
                    'username' => $username,
                    'password' => Hash::make($password),
                    'pass' => $password,
                    'abonado' => 'No'
                ]);
                    
                UsuarioComercio::create([
                    'usuario_id' => $user->id,            
                    'comercio_id' => $this->comercioId           
                ]);

                ModelHasRole::create([
                    'role_id' => $rolNoUsuario[0]->id,
                    'model_type' => 'App\Models\User',           
                    'model_id' => $user->id           
                ]);
            }
            else{
                $user = User::find($this->selected_id);
                $user->update([
                    'name' => ucwords($this->name),
                    'apellido' => ucwords($this->apellido),
                    'documento' => $this->documento,
                    'calle' => ucwords($this->calle),
                    'numero' => $this->numero,
                    'localidad_id' => $this->localidad,
                    'telefono1' => $this->telefono1,
                    'fecha_ingreso' => Carbon::parse($this->fecha_ingreso)->format('Y,m,d h:i:s'),             
                    'fecha_nac' => Carbon::parse($this->fecha_nac)->format('Y,m,d h:i:s'),
                    'sexo' => $this->sexo,
                    'email' => strtolower($this->email),
                    'abonado' => 'No'
                ]);
            }
            DB::commit();
            $this->sendEmail($user, $this->comercio, $this->admin);
            $this->doAction(1);
            if($this->selected_id > 0) session()->flash('message', 'Usuario Actualizado');            
            else session()->flash('message', 'Usuario creado exitosamente! Verificar envío de email');       
        }catch (\Exception $e){
            DB::rollback();    //en caso de error, deshacemos para no generar inconsistencia de datos  
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }  
    }  
    
    public function destroy($id)
    {
        if ($id) { //si es un id válido
            $record = User::where('id', $id); //buscamos el registro
            $record->delete(); //eliminamos el registro
            $this->resetInput(); //limpiamos las propiedades
        }
    }

    public function sendEmail($user, $comercio, $admin)
    {
        $objDemo = new \stdClass();
        $objDemo->demo_one = $user->username;
        $objDemo->demo_two = session('pass_empleado');
        $objDemo->sender = 'El equipo de FlokI';
        $objDemo->receiver = $user->name;
 
        Mail::to($this->email)->send(new WelcomeUser($user, $comercio, $admin));
    }

}
