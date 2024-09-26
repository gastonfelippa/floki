<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Mesa;
use App\Models\Reserva;
use App\Models\Sector;
use App\Models\User;
use Carbon\Carbon;
use DB;

class ReservasEstadoMesasController extends Component
{
    public $comercioId, $sectores, $mesas, $mozos, $reservas, $estadoMesa = "1", $selected_id = 0;
    public $search, $search_table, $action = 1, $tab="Interior", $recuperar_registro = 0;
    public $nombre, $apellido, $telefono, $cantidad, $mesa, $comentario, $fecha, $horario = "Elegir";
    public $mesa_anterior = '', $mesaId, $mesaDescripcion;
    public $asignarReserva, $descripcion_soft_deleted, $id_soft_deleted;
 
    public function render()
    {
        $this->comercioId = session('idComercio');
        session(['idMesa' => null]);

        $this->asignarReserva = session('asignarReserva');
        if($this->asignarReserva) $this->edit($this->asignarReserva);

        $hoy = Carbon::now();  
        $hoy_solo_fecha = Carbon::parse($hoy)->format('Y-m-d'); 
       
        $this->sectores = Sector::all()->where('comercio_id', $this->comercioId);
        $this->mesas = Mesa::where('comercio_id', $this->comercioId)
            ->where('estado', 'Disponible')
            ->orWhere('id', $this->mesa_anterior)->get();

        $this->mozos = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('uc.comercio_id', $this->comercioId)->select('users.*')->orderBy('apellido')->get();

        if(strlen($this->search) > 0){
            $this->search_table = null;
            $this->reservas = Reserva::where('nombre', 'like', '%' .  $this->search . '%')
                ->where('comercio_id', $this->comercioId)
                ->where('fecha', $hoy_solo_fecha)
                ->orWhere('apellido', 'like', '%' .  $this->search . '%')
                ->where('comercio_id', $this->comercioId)
                ->where('fecha', $hoy_solo_fecha)
                ->orWhere('reservas.estado', 'like', '%' .  $this->search . '%')
                ->where('reservas.comercio_id', $this->comercioId)
                ->where('fecha', $hoy_solo_fecha)
                ->select('*', DB::RAW("'' as mesaDesc"))->orderBy('apellido')->get();
        }else{
            $this->reservas = Reserva::where('comercio_id', $this->comercioId)
                ->where('fecha', $hoy_solo_fecha)
                ->select('*', DB::RAW("'' as mesaDesc"))->orderBy('apellido')->get();
        }
        if(strlen($this->search_table) > 0){
            $this->search = null;
            if(strtolower($this->search_table) == 'sin asignar'){
                $this->reservas = Reserva::where('mesa_id', null)
                    ->where('comercio_id', $this->comercioId)
                    ->where('fecha', $hoy_solo_fecha)
                    ->select('*', DB::RAW("'' as mesaDesc"))->get();
            }else{
                $mesaId = Mesa::where('descripcion', 'like', $this->search_table)
                    ->where('comercio_id', $this->comercioId)->first();
                if($mesaId) {
                    $this->reservas = Reserva::where('mesa_id', $mesaId['id'])
                        ->where('comercio_id', $this->comercioId)
                        ->where('fecha', $hoy_solo_fecha)
                        ->select('*', DB::RAW("'' as mesaDesc"))->get();
                }
            }
        }
        foreach ($this->reservas as $i) {
            if ($i->mesa_id) {
                $mesa = Mesa::find($i->mesa_id);
                $i->mesaDesc = $mesa->descripcion;
            }else{
                $i->mesaDesc = 'Sin asignar...';
            }
        }

       
        switch ($this->estadoMesa) {
            case '1': //todas
                $info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', '<>', 'Deshabilitada')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
             case '2': //disponibles
                $info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'Disponible')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            case '3': //ocupadas
                $info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'Ocupada')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            case '4': //c/factura 
                $info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'C/factura')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            case '5': //canceladas 
                $info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'Cancelada')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            case '6': //reservadas
                $info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'Reservada')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            case '7': //deshabilitadas
                $info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'Deshabilitada')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            default:
        }
        return view('livewire.reservas-estado-mesas.component' , ['info' => $info]);
    }
    protected $listeners = [
        'abrirMesa'        => 'abrirMesa',
        'agregaMozo'       => 'agregaMozo',
        'deshabilitarMesa' => 'deshabilitarMesa',
        'habilitarMesa'    => 'habilitarMesa',
        'cancelarReserva'  => 'cancelarReserva'
    ];
    
    protected $validationAttributes = [
        'horario' => 'Horario de la Reserva'
    ];

    public function doAction($action)
    {
        $this->action = $action;
        if($this->action == 2) session(['asignarReserva' => null]);
        if($action != 3) $this->resetInput();
    }
    public function resetInput()
    {
        $this->search        = null;
        $this->nombre        = null;
        $this->apellido      = null;
        $this->telefono      = null;
        $this->fecha         = null;
        $this->horario       = "Elegir";
        $this->cantidad      = '';
        $this->mesa          = null;
        $this->mesa_anterior = '';
        $this->comentario    = null;
        $this->mesas         = null;
        $this->tab           = 'Interior';
        $this->estadoMesa    = '1';
        $this->selected_id   = 0;
    }
    public function cambiarSector($sector)
    {
        $this->tab = $sector;
    }
    public function verReservas()
    {
        $this->doAction(2);
    }
    public function agregarReserva()
    {
        $this->doAction(3);
    }
    public function agregaMozo($idMozo)
    {
        if($idMozo){
            session(['idMozo' => $idMozo]);
            session(['idMesa' => $this->mesaId]);
            session(['facturaPendiente' => null]);
            return redirect()->to('/facturasbar');
        }
    }
    public function abrirMesa($data)
    {
        //dd($data);
        $info = intval($data);      
        // $info = json_decode($data);        

        if($info == 'd' || $info == 'D'){
            session(['idMesa' => $info]);
            session(['facturaPendiente' => null]);
            return redirect()->to('/facturasbar');
        }else{
            $buscar_mesa = Mesa::find($info);
            if($buscar_mesa->count()){
                $this->mesaId = $buscar_mesa->id;
                $mesa = $buscar_mesa->descripcion;
                session(['idMesa' => $this->mesaId]);
                if($buscar_mesa->estado == 'Disponible'){
                    $this->emit('agregarMozo', $mesa);
                }elseif($buscar_mesa->estado == 'Reservada'){
                    $cliente = Reserva::where('mesa_id', $buscar_mesa->id)->where('estado', 'Asignada')->get();
                    $cliente = $cliente[0]->apellido . ' ' . $cliente[0]->nombre;
                    $this->emit('abrir_mesa_reserva', $mesa, $cliente);
                }elseif($buscar_mesa->estado == 'Deshabilitada'){
                    $this->emit('habilitar_mesa', $mesa);
                }else{
                    session(['idMozo' => null]);
                    session(['facturaPendiente' => null]); 
                    return redirect()->to('/facturasbar');  
                } 
            }else session()->flash('message', 'La mesa ingresada no existe');
        }
    }
    public function deshabilitarMesa($mesaDesc)
    {
        $mesa = Mesa::find($this->mesaId);
        if($mesa->count()){
            $mesa->update(['estado' => 'Deshabilitada']);
            $this->emit('mesa_deshabilitada', $mesaDesc);
        }
    }
    public function habilitarMesa($mesaDesc)
    {
        $mesa = Mesa::find($this->mesaId);
        if($mesa->count()){
            $mesa->update(['estado' => 'Disponible']);
            $this->emit('mesa_habilitada', $mesaDesc);
        }
        $this->estadoMesa    = '2';
    }
    public function edit($id)
    {
        $record = Reserva::findOrFail($id);
        $this->selected_id   = $id;
        $this->nombre        = $record->nombre;
        $this->apellido      = $record->apellido;
        $this->telefono      = $record->telefono;
        $this->cantidad      = $record->cantidad;
        $this->fecha         = Carbon::parse($record->fecha)->format('d-m-Y');
        $this->horario       = $record->horario;
        $this->comentario    = $record->comentario;
        if($record->mesa_id) $this->mesa = $record->mesa_id;        
        $this->mesa_anterior = $record->mesa_id;
      
        $this->doAction(3);
    }
    public function StoreOrUpdate()
    {
        $this->validate([
            'horario' => 'not_in:Elegir'
        ]);
           
        $this->validate([
            'nombre'   => 'required', 
            'cantidad' => 'required|numeric',
            'fecha'    => 'required'
        ]);

        $estado = 'Pendiente';
        if($this->mesa == 'Elegir') $this->mesa = null;
        if($this->mesa) $estado = 'Asignada';        
 
        DB::begintransaction();
        try{
            if($this->selected_id > 0) {
                $existe = Reserva::where('nombre', $this->nombre)
                    ->where('apellido', $this->apellido)
                    ->where('comercio_id', $this->comercioId)
                    ->where('id', '<>', $this->selected_id)
                    ->select('*')
                    ->withTrashed()->get();
                if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                    session()->flash('info', 'La Reserva que desea modificar ya existe pero fué eliminada anteriormente, para recuperarla haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->apellido . ' ' . $existe[0]->nombre;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif( $existe->count() > 0) {
                    session()->flash('info', 'La Reserva ya existe...');
                    $this->resetInput();
                    return;
                }
            }else {
                $existe = Reserva::where('nombre', $this->nombre)
                    ->where('apellido', $this->apellido)
                    ->where('comercio_id', $this->comercioId)
                    ->select('*')->withTrashed()->get();

                if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                    session()->flash('info', 'La Reserva que desea agregar ya existe pero fué eliminada anteriormente, para recuperarla haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->apellido . ' ' . $existe[0]->nombre;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count() > 0 ) {
                    session()->flash('info', 'La Reserva ya existe...');
                    $this->resetInput();
                    return;
                }
            }        
            if($this->selected_id <= 0) {
                Reserva::create([
                    'nombre'      => mb_strtoupper($this->nombre),            
                    'apellido'    => mb_strtoupper($this->apellido),             
                    'telefono'    => $this->telefono,
                    'horario'     => $this->horario,
                    'fecha'       => Carbon::parse($this->fecha)->format('Y,m,d'),
                    'cantidad'    => $this->cantidad,
                    'mesa_id'     => $this->mesa,
                    'estado'      => $estado,
                    'comentario'  => $this->comentario,
                    'comercio_id' => $this->comercioId            
                ]);
                if($this->mesa){
                    $record = Mesa::find($this->mesa);
                    $record->update(['estado' => 'Reservada']);
                }               
            }else {   
                $record = Reserva::find($this->selected_id);
                $record->update([
                    'nombre'      => mb_strtoupper($this->nombre),            
                    'apellido'    => mb_strtoupper($this->apellido),             
                    'telefono'    => $this->telefono,
                    'horario'     => $this->horario,
                    'fecha'       => Carbon::parse($this->fecha)->format('Y,m,d'),
                    'cantidad'    => $this->cantidad,
                    'mesa_id'     => $this->mesa,
                    'estado'      => $estado,
                    'comentario'  => $this->comentario,
                ]); 
                if($this->mesa_anterior){
                    $record = Mesa::find($this->mesa_anterior);
                    $record->update(['estado' => 'Disponible']);
                }
                if($this->mesa){
                    $record = Mesa::find($this->mesa);
                    $record->update(['estado' => 'Reservada']);
                }
            }             
            if($this->selected_id) $this->emit('crearReserva', 'Reserva actualizada exitosamente!!!');    
            else $this->emit('crearReserva', 'Reserva creada exitosamente!!!'); 

            DB::commit();            
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        return;
    }
    public function cancelarReserva($comentarioCancel)
    {
        DB::begintransaction();
        try{  
            $record = Reserva::find($this->selected_id);            
            if($record->mesa_id){
                $mesa = Mesa::find($record->mesa_id);
                $mesa->update(['estado' => 'Disponible']);
            } 
            $record->update([
                'mesa_id'           => null,
                'estado'            => 'Cancelada',
                'comentario_cancel' => $comentarioCancel,
            ]);
            session()->flash('msg-ok', 'Reserva Cancelada'); 

            DB::commit();            
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        return;

    }
}
