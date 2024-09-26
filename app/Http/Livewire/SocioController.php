<?php

//namespace App\Exceptions\Handler;
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\CajaUsuario;
use App\Models\CategoriaClub;
use App\Models\GrupoFamiliar;
use App\Models\Localidad;
use App\Models\OtroDebito;
use App\Models\Provincia;
use App\Models\Socio;
use App\Models\SocioActividad;
use App\Models\User;
use Carbon\Carbon;
use DB;

class SocioController extends Component
{
    public $tipo = 1, $categoria = 'Elegir', $nombre, $apellido, $telefono, $email;
    public $fecha_nac =null, $fecha_alta =null, $fecha_baja =null;
    public $documento, $calle, $numero, $localidad = 'Elegir', $provincia = 'Elegir';
    public $cobrador = 'Local', $cobrar_en, $comentario, $estado = 'Activo', $grupo_familiar = 1;  
    public $cobradores, $selected_id, $search, $action = 1, $caja_abierta; 
    public $recuperar_registro = 0, $descripcion_soft_deleted, $id_soft_deleted;
    public $comercioId, $mes, $año, $socio, $socio_id = null, $socio_actividad;
    public $nro_arqueo, $fecha_inicio, $dni_valido;
    

    public function render()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');

        //vemos si tenemos una caja habilitada con nuestro user_id
        $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', auth()->user()->id)
            ->where('caja_usuarios.estado', '1')->select('caja_usuarios.*')->get();
        $this->caja_abierta = $caja_abierta->count();
        if($caja_abierta->count() > 0){
            $this->nro_arqueo = $caja_abierta[0]->id; 
            $this->fecha_inicio = $caja_abierta[0]->created_at;  
        }
        
        $actividades = OtroDebito::where('comercio_id', $this->comercioId)->select()->orderBy('descripcion')->get();
        $categorias = CategoriaClub::where('comercio_id', $this->comercioId)->select()->orderBy('descripcion')->get();
        $localidades = Localidad::where('comercio_id', $this->comercioId)->select()->orderBy('descripcion')->get();
        $provincias = Provincia::all();
        if($this->socio_id != null){
            $this->socio_actividad = SocioActividad::join('otros_debitos as o', 'o.id', 'socio_actividad.actividad_id')
                ->where('socio_actividad.socio_id', $this->socio_id)
                ->select('o.*')->get();
        }
        

        //busco los cobradores dentro de los usuarios
        $this->cobradores = User::join('model_has_roles as mhr', 'mhr.model_id', 'users.id')
            ->join('roles as r', 'r.id', 'mhr.role_id')
            ->where('r.alias', 'Cobrador')
            ->where('r.comercio_id', $this->comercioId)
            ->select('users.id', 'users.name', 'users.apellido')->get();

        // //capturo el id del repartidor Salón 
        // $salon = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
        //    ->where('users.name', '...')
        //    ->where('users.apellido', 'Salón')
        //    ->where('uc.comercio_id', $this->comercioId)
        //    ->select('users.id')->get();
        // $this->localId = $salon[0]->id;

        if(strlen($this->search) > 0){
            $info = Socio::join('localidades as loc', 'loc.id', 'socios.localidad_id')
                ->where('nombre', 'like', '%' .  $this->search . '%')
                ->where('socios.tipo', $this->tipo)
                ->where('socios.estado', $this->estado)
                ->where('socios.comercio_id', $this->comercioId)
                ->orWhere('apellido', 'like', '%' .  $this->search . '%')
                ->where('socios.tipo', $this->tipo)
                ->where('socios.estado', $this->estado)
                ->where('socios.comercio_id', $this->comercioId)
                ->orWhere('calle', 'like', '%' .  $this->search . '%')
                ->where('socios.tipo', $this->tipo)
                ->where('socios.estado', $this->estado)
                ->where('socios.comercio_id', $this->comercioId)
                ->orWhere('loc.descripcion', 'like', '%' .  $this->search . '%')
                ->where('socios.tipo', $this->tipo)
                ->where('socios.estado', $this->estado)
                ->where('socios.comercio_id', $this->comercioId)
                ->select('socios.*', 'loc.descripcion as localidad')
                ->orderBy('apellido', 'asc')->get();
        }else {
            $info = Socio::join('localidades as loc', 'loc.id', 'socios.localidad_id')
                ->where('socios.tipo', $this->tipo)
                ->where('socios.estado', $this->estado)
                ->where('socios.comercio_id', $this->comercioId)
                ->orderBy('apellido', 'asc')
                ->select('socios.*', 'loc.descripcion as localidad', DB::RAW("'' as tieneGrupoCargado"))->get();
        }
        foreach ($info as $i){          
            $tieneGrupoCargado = GrupoFamiliar::where('socio_id', $i->id);
            if($tieneGrupoCargado->count()) $i->tieneGrupoCargado = 1;
            else $i->tieneGrupoCargado = 0;
        }

        return view('livewire.socios.component', [
            'info'            => $info,
            'localidades'     => $localidades,
            'provincias'      => $provincias,
            'categorias'      => $categorias,
            'actividades'     => $actividades
        ]);
    }  
    protected $listeners = [
        'deleteRow'                => 'destroy',
        'createFromModal'          => 'createFromModal',
        'createActividadFromModal' => 'createActividadFromModal',
        'guardar'                  => 'StoreOrUpdate',
        'cambiarFecha'             => 'cambiarFecha'     
    ]; 
    public function cambiarFecha($data)  
    {                                   
        $fecha_consulta = Carbon::parse($data);
        if($data != '') {
            $mes_en_numero = date('m',strtotime($data));
            $this->año = date('Y',strtotime($data));
        }
        switch ($mes_en_numero) {
            case '1' : $this->mes = 'ENERO'; break;
            case '2' : $this->mes = 'FEBRERO'; break;
            case '3' : $this->mes = 'MARZO'; break;
            case '4' : $this->mes = 'ABRIL'; break;
            case '5' : $this->mes = 'MAYO'; break;
            case '6' : $this->mes = 'JUNIO'; break;
            case '7' : $this->mes = 'JULIO'; break;
            case '8' : $this->mes = 'AGOSTO'; break;
            case '9' : $this->mes = 'SETIEMBRE'; break;
            case '10': $this->mes = 'OCTUBRE'; break;
            case '11': $this->mes = 'NOVIEMBRE'; break;
            case '12': $this->mes = 'DICIEMBRE'; break;
            default: $this->mes = "...";
        }
    }
    public function actividades($id)
    {
        $record = Socio::findOrFail($id);
        $this->socio = $record->apellido . ' ' . $record->nombre;
        $this->socio_id = $id;
         
        $this->doAction(3);
    }
    public function doAction($action)
    {
        $this->action = $action;
        $this->resetInput();
    }        
    private function resetInput()
    {
        $this->tipo           = 1;
        $this->categoria      = 'Elegir';
        $this->nombre         = '';
        $this->apellido       = '';
        $this->documento      = '';
        $this->calle          = '';
        $this->numero         = '';
        $this->localidad      = 'Elegir';
        $this->provincia      = 'Elegir';
        $this->telefono       = '';
        $this->email          = '';
        $this->documento      = '';
        $this->fecha_nac      = null;
        $this->fecha_alta     = null;
        $this->fecha_baja     = null;
        $this->cobrador       = 'Local';
        $this->cobrar_en      = '';
        $this->estado         = 'Activo';
        $this->comentario     = '';
        $this->grupo_familiar = 1;
        $this->selected_id    = null;       
        $this->search         = '';
    }
    public function edit($id)
    {
        $record = Socio::findOrFail($id);
        $this->selected_id    = $id;
        $this->tipo           = $record->tipo;
        $this->categoria      = $record->categoria_id;
        $this->nombre         = $record->nombre;
        $this->apellido       = $record->apellido;
        $this->documento      = $record->documento;
        $this->calle          = $record->calle;
        $this->numero         = $record->numero;
        $this->localidad      = $record->localidad_id;
        $this->telefono       = $record->telefono;
        $this->email          = $record->email;
        $this->documento      = $record->documento;
        $this->fecha_nac      = Carbon::parse($record->fecha_nac)->format('d-m-Y');
        $this->fecha_alta     = Carbon::parse($record->fecha_alta)->format('d-m-Y');
        if($record->fecha_baja) $this->fecha_baja = Carbon::parse($record->fecha_baja)->format('d-m-Y');
        $this->cobrador       = $record->cobrador_id;
        $this->cobrar_en      = $record->cobrar_en;
        $this->estado         = $record->estado;
        $this->comentario     = $record->comentario;
        $this->grupo_familiar = $record->grupo_familiar;
        $this->action = 2;
    }
    public function volver()
    {
        $this->recuperar_registro = 0;
        $this->resetInput();
        return; 
    }
    public function RecuperarRegistro($id)
    {
        DB::begintransaction();
        try{
            Socio::onlyTrashed()->find($id)->restore();
            session()->flash('msg-ok', 'Registro recuperado');
            $this->volver();
            
            DB::commit();               
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se recuperó...');
        }
    }
    public function StoreOrUpdate()
    {
        if($this->email <> '') $this->validate(['email' => 'required|string|email|max:255']);
        
        $this->validate([
            'localidad' => 'not_in:Elegir',
            'categoria' => 'not_in:Elegir'
        ]);
           
        $this->validate([
            'nombre'       => 'required', 
            'apellido'     => 'required',
            'calle'        => 'required',
            'telefono'     => 'required'
        ]);
        if($this->numero == '') $this->numero = 's/n';
        
        DB::begintransaction();
        try{
            if($this->selected_id > 0) {
                $existe = Socio::where('nombre', $this->nombre)
                    ->where('apellido', $this->apellido)
                    ->where('calle', $this->calle)
                    ->where('numero', $this->numero)
                    ->where('localidad_id', $this->localidad)
                    ->where('comercio_id', $this->comercioId)
                    ->where('id', '<>', $this->selected_id)
                    ->select('*')
                    ->withTrashed()->get();
                if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                    session()->flash('info', 'El Socio que desea modificar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->apellido . ' ' . $existe[0]->nombre;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif( $existe->count() > 0) {
                    session()->flash('info', 'El Socio ya existe...');
                    $this->resetInput();
                    return;
                }
            }else {
                $existe = Socio::where('nombre', $this->nombre)
                    ->where('apellido', $this->apellido)
                    ->where('calle', $this->calle)
                    ->where('numero', $this->numero)
                    ->where('localidad_id', $this->localidad)
                    ->where('comercio_id', $this->comercioId)
                    ->select('*')->withTrashed()->get();

                if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                    session()->flash('info', 'El Socio que desea agregar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->apellido . ' ' . $existe[0]->nombre;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count() > 0 ) {
                    session()->flash('info', 'El Socio ya existe...');
                    $this->resetInput();
                    return;
                }
            }        
            if($this->selected_id <= 0) {
                Socio::create([
                    'tipo'           => $this->tipo,
                    'categoria_id'   => $this->categoria,
                    'nombre'         => mb_strtoupper($this->nombre),            
                    'apellido'       => mb_strtoupper($this->apellido),     
                    'calle'          => ucwords($this->calle),            
                    'numero'         => $this->numero,            
                    'localidad_id'   => $this->localidad,            
                    'telefono'       => $this->telefono,
                    'email'          => $this->email,
                    'documento'      => $this->documento,
                    'fecha_nac'      => Carbon::parse($this->fecha_nac)->format('Y,m,d h:i:s'),
                    'fecha_alta'     => Carbon::parse($this->fecha_alta)->format('Y,m,d h:i:s'),
                    'fecha_baja'     => Carbon::parse($this->fecha_baja)->format('Y,m,d h:i:s'),
                    'cobrador_id'    => $this->cobrador,
                    'cobrar_en'      => ucwords($this->cobrar_en),
                    'comentario'     => $this->comentario,
                    'estado'         => $this->estado,
                    'grupo_familiar' => $this->grupo_familiar,
                    'comercio_id'    => $this->comercioId            
                ]);
            }else {   
                $record = Socio::find($this->selected_id);
                $record->update([
                    'tipo'           => $this->tipo,
                    'categoria_id'   => $this->categoria,
                    'nombre'         => mb_strtoupper($this->nombre),            
                    'apellido'       => mb_strtoupper($this->apellido),     
                    'calle'          => ucwords($this->calle),            
                    'numero'         => $this->numero,            
                    'localidad_id'   => $this->localidad,            
                    'telefono'       => $this->telefono,
                    'email'          => $this->email,
                    'documento'      => $this->documento,
                    'fecha_nac'      => Carbon::parse($this->fecha_nac)->format('Y,m,d h:i:s'),
                    'fecha_alta'     => Carbon::parse($this->fecha_alta)->format('Y,m,d h:i:s'),
                    'fecha_baja'     => Carbon::parse($this->fecha_baja)->format('Y,m,d h:i:s'),
                    'cobrador_id'    => $this->cobrador,
                    'cobrar_en'      => ucwords($this->cobrar_en),
                    'comentario'     => $this->comentario,
                    'estado'         => $this->estado,
                    'grupo_familiar' => $this->grupo_familiar
                ]); 
                $this->action = 1;             
            }
            if($this->selected_id) session()->flash('msg-ok', 'Socio Actualizado');    
            else session()->flash('msg-ok', 'Socio Creado'); 

            DB::commit();            
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        return;
    }
    public function verificarPorDni()
    {
        $this->dni_valido = false;
        $existe = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('uc.comercio_id', $this->comercioId)
            ->where('users.documento', $this->documento)
            ->withTrashed()->get();
        if($existe->count() && $existe[0]->deleted_at != null) {
           // session()->flash('msg-error', 'El Empleado que desea agregar ya existe en el sistema pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
            $this->action = 1;
            $this->recuperar_registro = 1;
            $this->descripcion_soft_deleted = $existe[0]->apellido . ' ' . $existe[0]->name . ' - DNI: ' . $existe[0]->documento;
            $this->id_soft_deleted = $existe[0]->usuario_id;
            return;
        }elseif($existe->count()) $this->emit('usuario_repetido');
        else $this->dni_valido = true;
    }
    public function destroy($id)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $socio = Socio::find($id)->delete();
                session()->flash('msg-ok', 'Registro eliminado con éxito!!');
                DB::commit();               
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
            }
            $this->resetInput();
            return;
        }    
    }
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
                    'descripcion'  => ucwords($data->localidad),
                    'provincia_id' => $data->provincia_id,
                    'comercio_id'  => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Localidad creada exitosamente!!!'); 
                DB::commit();               
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se creó...');
            }
        }
    }
    public function createActividadFromModal($info)
    {
        $data = json_decode($info);

        $existe = SocioActividad::where('actividad_id', $data->actividad_id)
            ->where('socio_id', $this->socio_id)->get();  
        if($existe->count()) {
            session()->flash('info', 'La Actividad ingresada ya existe!!!');
            return;
        }else{
            DB::begintransaction();
            try{   
                SocioActividad::create([
                    'socio_id'     => $this->socio_id,
                    'actividad_id' => $data->actividad_id
                ]);
                session()->flash('msg-ok', 'Actividad creada exitosamente!!!'); 
                DB::commit();               
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se creó...');
            }
        }
    }
}
