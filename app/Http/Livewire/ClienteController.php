<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Localidad;
use App\Models\Producto;
use App\Models\Provincia;
use App\Models\Vianda;
use App\Models\CajaUsuario;
use Carbon\Carbon;
use DB;

class ClienteController extends Component
{
    public $nombre, $apellido, $documento, $calle, $numero, $localidad = 'Elegir', $provincia = 'Elegir';
    public $telefono, $vianda = null, $consignatario = null;  
    public $selected_id, $search, $action = 1, $caja_abierta; 
    public $nomCliV, $apeCliV, $producto = 'Elegir', $comentarios;
    public $h_lunes_m, $h_lunes_n, $h_martes_m, $h_martes_n, $h_miercoles_m, $h_miercoles_n, $h_jueves_m, $h_jueves_n;
    public $h_viernes_m, $h_viernes_n, $h_sabado_m, $h_sabado_n, $h_domingo_m, $h_domingo_n;
    public $c_lunes_m, $c_lunes_n, $c_martes_m, $c_martes_n, $c_miercoles_m, $c_miercoles_n, $c_jueves_m, $c_jueves_n;
    public $c_viernes_m, $c_viernes_n, $c_sabado_m, $c_sabado_n, $c_domingo_m, $c_domingo_n;
    public $recuperar_registro = 0, $descripcion_soft_deleted, $id_soft_deleted;
    public $comercioId, $comercioTipo, $modViandas, $modConsignaciones;
    public $mes, $año, $nro_arqueo, $fecha_inicio;

    public function render()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        $this->modViandas = session('modViandas');
        $this->modConsignaciones = session('modConsignaciones');
        $this->comercioTipo = session('tipoComercio');
        
          //vemos si tenemos una caja habilitada con nuestro user_id
        $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', auth()->user()->id)
            ->where('caja_usuarios.estado', '1')->select('caja_usuarios.*')->get();
        $this->caja_abierta = $caja_abierta->count();
        if($caja_abierta->count() > 0){
            $this->nro_arqueo = $caja_abierta[0]->id; 
            $this->fecha_inicio = $caja_abierta[0]->created_at;  
        }

        $productos = Producto::select()->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        $localidades = Localidad::where('comercio_id', $this->comercioId)->select()->orderBy('descripcion')->get();
        $provincias = Provincia::all();

        if(strlen($this->search) > 0){
            $info = Cliente::join('localidades as loc', 'loc.id', 'clientes.localidad_id')
                ->where('nombre', 'like', '%' .  $this->search . '%')
                ->where('nombre', 'not like', 'FINAL')
                ->where('clientes.comercio_id', $this->comercioId)
                ->orWhere('apellido', 'like', '%' .  $this->search . '%')
                ->where('nombre', 'not like', 'FINAL')
                ->where('clientes.comercio_id', $this->comercioId)
                ->orWhere('calle', 'like', '%' .  $this->search . '%')
                ->where('nombre', 'not like', 'FINAL')
                ->where('clientes.comercio_id', $this->comercioId)
                ->orWhere('loc.descripcion', 'like', '%' .  $this->search . '%')
                ->where('nombre', 'not like', 'FINAL')
                ->where('clientes.comercio_id', $this->comercioId)
                ->select('clientes.*', 'loc.descripcion as localidad', DB::RAW("'' as esConsFinal"))
                ->orderBy('apellido', 'asc')->get();
        }else {
            $info = Cliente::join('localidades as loc', 'loc.id', 'clientes.localidad_id')
                ->where('clientes.comercio_id', $this->comercioId)
                ->where('nombre', 'not like', 'FINAL')
                ->orderBy('apellido', 'asc')
                ->select('clientes.*', 'loc.descripcion as localidad', DB::RAW("'' as tieneViandasCargadas",
                    DB::RAW("'' as esConsFinal"), DB::RAW("'' as tipo")))->get();
        } 
        foreach ($info as $i){
            $i->esConsFinal = 0;           
            $tieneViandasCargadas = Vianda::where('cliente_id', $i->id);
            if($tieneViandasCargadas->count()) $i->tieneViandasCargadas = 1;
            else $i->tieneViandasCargadas = 0;
            if($i->localidad == '.') $i->esConsFinal = 1;
            if($i->consignatario == '1') $i->tipo = 'Consignatario';
            else $i->tipo = 'Cliente';
        }

        return view('livewire.clientes.component', [
            'info'        => $info,
            'localidades' => $localidades,
            'provincias'  => $provincias,
            'productos'   => $productos
        ]);
    }  
    protected $listeners = [
        'deleteRow'       =>'destroy',
        'createFromModal' => 'createFromModal',
        'guardar'         => 'StoreOrUpdate',
        'cambiarFecha'    =>'cambiarFecha'     
    ]; 
    public function cambiarFecha($data)  //esta función inhabilita la vista 'Ver Lista Facturas' 
    {                                    //cuando estamos fuera del Arqueo Gral activo
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
        //dd($this->mes,$this->año);
    }
    public function verViandas($id, $action)
    {
        $this->action = $action;
        $this->selected_id = $id;

        $record = Cliente::findOrFail($id);    
        $this->nomCliV = $record->nombre;
        $this->apeCliV = $record->apellido;

        $viandas = Vianda::join('productos as p', 'p.id', 'viandas.producto_id')
            ->where('cliente_id', $id)
            ->select('viandas.*', 'p.id as producto_id')->get();

        if($viandas->count() > 0){
            $this->producto      = $viandas[0]->producto_id;
            $this->comentarios   = $viandas[0]->comentarios;
            $this->h_lunes_m     = $viandas[0]->h_lunes_m;   
            $this->h_lunes_n     = $viandas[0]->h_lunes_n;   
            $this->h_martes_m    = $viandas[0]->h_martes_m;   
            $this->h_martes_n    = $viandas[0]->h_martes_n;   
            $this->h_miercoles_m = $viandas[0]->h_miercoles_m;   
            $this->h_miercoles_n = $viandas[0]->h_miercoles_n;   
            $this->h_jueves_m    = $viandas[0]->h_jueves_m;   
            $this->h_jueves_n    = $viandas[0]->h_jueves_n;   
            $this->h_viernes_m   = $viandas[0]->h_viernes_m;   
            $this->h_viernes_n   = $viandas[0]->h_viernes_n;   
            $this->h_sabado_m    = $viandas[0]->h_sabado_m;   
            $this->h_sabado_n    = $viandas[0]->h_sabado_n;   
            $this->h_domingo_m   = $viandas[0]->h_domingo_m;   
            $this->h_domingo_n   = $viandas[0]->h_domingo_n;
            $this->c_lunes_m     = $viandas[0]->c_lunes_m;   
            $this->c_lunes_n     = $viandas[0]->c_lunes_n;   
            $this->c_martes_m    = $viandas[0]->c_martes_m;   
            $this->c_martes_n    = $viandas[0]->c_martes_n;   
            $this->c_miercoles_m = $viandas[0]->c_miercoles_m;   
            $this->c_miercoles_n = $viandas[0]->c_miercoles_n;   
            $this->c_jueves_m    = $viandas[0]->c_jueves_m;   
            $this->c_jueves_n    = $viandas[0]->c_jueves_n;   
            $this->c_viernes_m   = $viandas[0]->c_viernes_m;   
            $this->c_viernes_n   = $viandas[0]->c_viernes_n;   
            $this->c_sabado_m    = $viandas[0]->c_sabado_m;   
            $this->c_sabado_n    = $viandas[0]->c_sabado_n;   
            $this->c_domingo_m   = $viandas[0]->c_domingo_m;   
            $this->c_domingo_n   = $viandas[0]->c_domingo_n;
        }       
    }
    public function doAction($action)
    {
        $this->action = $action;
        $this->resetInput();
    }        
    private function resetInput()
    {
        $this->nombre        = '';
        $this->apellido      = '';
        $this->documento     = '';
        $this->calle         = '';
        $this->numero        = '';
        $this->localidad     = 'Elegir';
        $this->provincia     = 'Elegir';
        $this->producto      = 'Elegir';
        $this->telefono      = '';
        $this->vianda        = null;
        $this->consignatario = null;
        $this->selected_id   = null;       
        $this->search        = '';

        $this->producto   = 'Elegir';
        $this->comentarios   = '';
        $this->h_lunes_m     = null;   
        $this->h_lunes_n     = null;   
        $this->h_martes_m    = null;   
        $this->h_martes_n    = null;   
        $this->h_miercoles_m = null;   
        $this->h_miercoles_n = null;   
        $this->h_jueves_m    = null;   
        $this->h_jueves_n    = null;   
        $this->h_viernes_m   = null;   
        $this->h_viernes_n   = null;   
        $this->h_sabado_m    = null;   
        $this->h_sabado_n    = null;   
        $this->h_domingo_m   = null;   
        $this->h_domingo_n   = null;
        $this->c_lunes_m     = null;   
        $this->c_lunes_n     = null;   
        $this->c_martes_m    = null;   
        $this->c_martes_n    = null;   
        $this->c_miercoles_m = null;   
        $this->c_miercoles_n = null;   
        $this->c_jueves_m    = null;   
        $this->c_jueves_n    = null;   
        $this->c_viernes_m   = null;   
        $this->c_viernes_n   = null;   
        $this->c_sabado_m    = null;   
        $this->c_sabado_n    = null;   
        $this->c_domingo_m   = null;   
        $this->c_domingo_n   = null;
    }
    public function edit($id)
    {
        $record = Cliente::findOrFail($id);
        $this->selected_id   = $id;
        $this->nombre        = $record->nombre;
        $this->apellido      = $record->apellido;
        $this->documento     = $record->documento;
        $this->calle         = $record->calle;
        $this->numero        = $record->numero;
        $this->localidad     = $record->localidad_id;
        $this->telefono      = $record->telefono;
		if($record->vianda == '1') $this->vianda = true; else $this->vianda = false;
		if($record->consignatario == '1') $this->consignatario = true; else $this->consignatario = false;

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
            Cliente::onlyTrashed()->find($id)->restore();
            session()->flash('msg-ok', 'Registro recuperado');
            $this->volver();
            
            DB::commit();               
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se recuperó...');
        }
    }
    public function StoreOrUpdate($vianda, $consignatario)
    {
        if($vianda) $vianda = '1'; else $vianda = '0';
        if($consignatario) $consignatario = '1'; else $consignatario = '0';
        $this->validate(['localidad' => 'not_in:Elegir']);
           
        $this->validate([
            'nombre' => 'required', 
            'apellido' => 'required',
            'calle' => 'required'
        ]);
        if($this->numero == '') $this->numero = 's/n';
        
        DB::begintransaction();
        try{
            if($this->selected_id > 0) {
                $existe = Cliente::where('nombre', $this->nombre)
                    ->where('apellido', $this->apellido)
                    ->where('calle', $this->calle)
                    ->where('numero', $this->numero)
                    ->where('localidad_id', $this->localidad)
                    ->where('comercio_id', $this->comercioId)
                    ->where('id', '<>', $this->selected_id)
                    ->select('*')
                    ->withTrashed()->get();
                if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                    session()->flash('info', 'El Cliente que desea modificar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->apellido . ' ' . $existe[0]->nombre;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif( $existe->count() > 0) {
                    session()->flash('info', 'El Cliente ya existe...');
                    $this->resetInput();
                    return;
                }
            }else {
                $existe = Cliente::where('nombre', $this->nombre)
                    ->where('apellido', $this->apellido)
                    ->where('calle', $this->calle)
                    ->where('numero', $this->numero)
                    ->where('localidad_id', $this->localidad)
                    ->where('comercio_id', $this->comercioId)
                    ->select('*')->withTrashed()->get();

                if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                    session()->flash('info', 'El Cliente que desea agregar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->apellido . ' ' . $existe[0]->nombre;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count() > 0 ) {
                    session()->flash('info', 'El Cliente ya existe...');
                    $this->resetInput();
                    return;
                }
            }        
            if($this->selected_id <= 0) {
                Cliente::create([
                    'nombre'        => mb_strtoupper($this->nombre),            
                    'apellido'      => mb_strtoupper($this->apellido),     
                    'calle'         => ucwords($this->calle),            
                    'numero'        => $this->numero,            
                    'localidad_id'  => $this->localidad,            
                    'telefono'      => $this->telefono,
                    'vianda'        => $vianda,
                    'consignatario' => $consignatario,
                    'saldo'         => '0',
                    'comercio_id'   => $this->comercioId            
                ]);
            }else {   
                $record = Cliente::find($this->selected_id);
                $record->update([
                    'nombre'        => mb_strtoupper($this->nombre),            
                    'apellido'      => mb_strtoupper($this->apellido),     
                    'calle'         => ucwords($this->calle),            
                    'numero'        => $this->numero,            
                    'localidad_id'  => $this->localidad,            
                    'telefono'      => $this->telefono,
                    'vianda'        => $vianda,
                    'consignatario' => $consignatario
                ]); 
                $this->action = 1;             
            }
            if($this->selected_id) session()->flash('msg-ok', 'Cliente Actualizado');    
            else session()->flash('msg-ok', 'Cliente Creado'); 

            DB::commit();            
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        return;
    }
    public function grabarViandas()
    {   //creo un array con las cantidades y otro con los horarios de todos los días
        $arrayCantidad = [$this->c_lunes_m, $this->c_lunes_n, $this->c_martes_m, $this->c_martes_n, $this->c_miercoles_m,
            $this->c_miercoles_n, $this->c_jueves_m, $this->c_jueves_n, $this->c_viernes_m, $this->c_viernes_n,
            $this->c_sabado_m, $this->c_sabado_n, $this->c_domingo_m, $this->c_domingo_n];
        $arrayHora = [$this->h_lunes_m, $this->h_lunes_n, $this->h_martes_m, $this->h_martes_n, $this->h_miercoles_m,
            $this->h_miercoles_n, $this->h_jueves_m, $this->h_jueves_n, $this->h_viernes_m, $this->h_viernes_n,
            $this->h_sabado_m, $this->h_sabado_n, $this->h_domingo_m, $this->h_domingo_n];

        for ($i=0;$i<14;$i++){           //verifico que las horas estén completas
            if(strlen($arrayHora[$i])==0 && $arrayCantidad[$i] > 0){
                if($i==0 || $i==1) $dia= 'Lunes';
                elseif($i==2 || $i==3) $dia= 'Martes';
                elseif($i==4 || $i==5) $dia= 'Miércoles';
                elseif($i==6 || $i==7) $dia= 'Jueves';
                elseif($i==8 || $i==9) $dia= 'Viernes';
                elseif($i==10 || $i==11) $dia= 'Sábado';
                else $dia= 'Doningo';
                session()->flash('msg-error', 'Verificar hora día ' . $dia . '. Está incompleta');
                return;
            }    
        }    
        for ($i=0;$i<14;$i++){          //verifico que las horas no estén en cero
            if($arrayHora[$i]=='00:00'){
                if($i==0 || $i==1) $dia= 'Lunes';
                elseif($i==2 || $i==3) $dia= 'Martes';
                elseif($i==4 || $i==5) $dia= 'Miércoles';
                elseif($i==6 || $i==7) $dia= 'Jueves';
                elseif($i==8 || $i==9) $dia= 'Viernes';
                elseif($i==10 || $i==11) $dia= 'Sábado';
                else $dia= 'Doningo';
                session()->flash('msg-error', 'Verificar hora día ' . $dia . '. No puede estar en cero');
                return;
            }    
        }    
        for ($i=0;$i<14;$i++){        //verifico que si hay una cantidad, la hora no esté nula
            if($arrayCantidad[$i]>0 && $arrayHora[$i]==null){
                if($i==0 || $i==1) $dia= 'Lunes';
                elseif($i==2 || $i==3) $dia= 'Martes';
                elseif($i==4 || $i==5) $dia= 'Miércoles';
                elseif($i==6 || $i==7) $dia= 'Jueves';
                elseif($i==8 || $i==9) $dia= 'Viernes';
                elseif($i==10 || $i==11) $dia= 'Sábado';
                else $dia= 'Doningo';
                session()->flash('msg-error', 'Verificar hora día ' . $dia . '. No puede ser nula');
                return;
            }
        }
        for ($i=0;$i<14;$i++){        //verifico que si hay una hora, la cantidad no esté en cero
            if($arrayCantidad[$i]==0 && $arrayHora[$i]!=null){
                if($i==0 || $i==1) $dia= 'lunes';
                elseif($i==2 || $i==3) $dia= 'martes';
                elseif($i==4 || $i==5) $dia= 'miercoles';
                elseif($i==6 || $i==7) $dia= 'jueves';
                elseif($i==8 || $i==9) $dia= 'viernes';
                elseif($i==10 || $i==11) $dia= 'sabado';
                else $dia= 'doningo';
                session()->flash('msg-error', 'Verificar cantidad día ' . $dia . '. No puede estar en cero');
                return;
            }
        }
        //validacion propia del sistema
        if($this->h_lunes_m == "") $this->h_lunes_m = null; 
        if($this->h_lunes_n == "") $this->h_lunes_n = null;  
        if($this->h_martes_m == "") $this->h_martes_m = null; 
        if($this->h_martes_n == "") $this->h_martes_n = null;  
        if($this->h_miercoles_m == "") $this->h_miercoles_m = null; 
        if($this->h_miercoles_n == "") $this->h_miercoles_n = null;  
        if($this->h_jueves_m == "") $this->h_jueves_m = null; 
        if($this->h_jueves_n == "") $this->h_jueves_n = null; 
        if($this->h_viernes_m == "") $this->h_viernes_m = null; 
        if($this->h_viernes_n == "") $this->h_viernes_n = null;  
        if($this->h_sabado_m == "") $this->h_sabado_m = null; 
        if($this->h_sabado_n == "") $this->h_sabado_n = null;  
        if($this->h_domingo_m == "") $this->h_domingo_m = null; 
        if($this->h_domingo_n == "") $this->h_domingo_n = null; 

        $this->validate(['producto' => 'not_in:Elegir']);
            
        DB::begintransaction();                
        try{
            $existe = Vianda::where('cliente_id', $this->selected_id)->first();
            if($existe){
                $existe->update([
                    'cliente_id'    => $this->selected_id, 
                    'producto_id'   => $this->producto,
                    'estado'        => 'activo', 
                    'comentarios'   => $this->comentarios, 
                    'h_lunes_m'     => $this->h_lunes_m, 
                    'h_lunes_n'     => $this->h_lunes_n, 
                    'h_martes_m'    => $this->h_martes_m, 
                    'h_martes_n'    => $this->h_martes_n, 
                    'h_miercoles_m' => $this->h_miercoles_m, 
                    'h_miercoles_n' => $this->h_miercoles_n, 
                    'h_jueves_m'    => $this->h_jueves_m, 
                    'h_jueves_n'    => $this->h_jueves_n, 
                    'h_viernes_m'   => $this->h_viernes_m, 
                    'h_viernes_n'   => $this->h_viernes_n, 
                    'h_sabado_m'    => $this->h_sabado_m, 
                    'h_sabado_n'    => $this->h_sabado_n, 
                    'h_domingo_m'   => $this->h_domingo_m, 
                    'h_domingo_n'   => $this->h_domingo_n, 
                    'c_lunes_m'     => $this->c_lunes_m, 
                    'c_lunes_n'     => $this->c_lunes_n, 
                    'c_martes_m'    => $this->c_martes_m, 
                    'c_martes_n'    => $this->c_martes_n, 
                    'c_miercoles_m' => $this->c_miercoles_m, 
                    'c_miercoles_n' => $this->c_miercoles_n, 
                    'c_jueves_m'    => $this->c_jueves_m, 
                    'c_jueves_n'    => $this->c_jueves_n, 
                    'c_viernes_m'   => $this->c_viernes_m, 
                    'c_viernes_n'   => $this->c_viernes_n, 
                    'c_sabado_m'    => $this->c_sabado_m, 
                    'c_sabado_n'    => $this->c_sabado_n, 
                    'c_domingo_m'   => $this->c_domingo_m, 
                    'c_domingo_n'   => $this->c_domingo_n 
                ]); 
            }else {
                Vianda::create([
                    'cliente_id'    => $this->selected_id, 
                    'producto_id'   => $this->producto,
                    'estado'        => 'activo', 
                    'comentarios'   => $this->comentarios, 
                    'h_lunes_m'     => $this->h_lunes_m, 
                    'h_lunes_n'     => $this->h_lunes_n, 
                    'h_martes_m'    => $this->h_martes_m, 
                    'h_martes_n'    => $this->h_martes_n, 
                    'h_miercoles_m' => $this->h_miercoles_m, 
                    'h_miercoles_n' => $this->h_miercoles_n, 
                    'h_jueves_m'    => $this->h_jueves_m, 
                    'h_jueves_n'    => $this->h_jueves_n, 
                    'h_viernes_m'   => $this->h_viernes_m, 
                    'h_viernes_n'   => $this->h_viernes_n, 
                    'h_sabado_m'    => $this->h_sabado_m, 
                    'h_sabado_n'    => $this->h_sabado_n, 
                    'h_domingo_m'   => $this->h_domingo_m, 
                    'h_domingo_n'   => $this->h_domingo_n, 
                    'c_lunes_m'     => $this->c_lunes_m, 
                    'c_lunes_n'     => $this->c_lunes_n, 
                    'c_martes_m'    => $this->c_martes_m, 
                    'c_martes_n'    => $this->c_martes_n, 
                    'c_miercoles_m' => $this->c_miercoles_m, 
                    'c_miercoles_n' => $this->c_miercoles_n, 
                    'c_jueves_m'    => $this->c_jueves_m, 
                    'c_jueves_n'    => $this->c_jueves_n, 
                    'c_viernes_m'   => $this->c_viernes_m, 
                    'c_viernes_n'   => $this->c_viernes_n, 
                    'c_sabado_m'    => $this->c_sabado_m, 
                    'c_sabado_n'    => $this->c_sabado_n, 
                    'c_domingo_m'   => $this->c_domingo_m, 
                    'c_domingo_n'   => $this->c_domingo_n 
                ]);        
            }
            DB::commit();
            if($existe != null) session()->flash('msg-ok', 'Detalle de Viandas actualizado exitosamente!!');            
            else session()->flash('msg-ok', 'Detalle de Viandas creado exitosamente!!');  
            
        }catch (Exception $e){
            DB::rollback();     
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        $this->action = 1;
    } 
    public function destroy($id)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $cliente = Cliente::find($id)->delete();
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
}
    