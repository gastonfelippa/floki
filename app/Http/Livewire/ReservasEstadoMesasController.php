<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Mesa;
use App\Models\Reserva;
use App\Models\Sector;
use App\Models\User;
use DB;

class ReservasEstadoMesasController extends Component
{
    public $comercioId, $estadoMesa = "1", $info, $sectores, $reservas, $selected_id = 0, $search;
    public $action = 1, $tab="Interior", $factura_id = 7, $recuperar_registro = 0;
    public $nombre, $apellido = null, $telefono = null, $cantidad, $mesa = 'Elegir', $comentario = null;
    public $hora = null, $mesa_anterior = '', $mesaId;

    public function render()
    {
        $this->comercioId = session('idComercio');
        session(['idMesa' => null]);
       
        $this->sectores = Sector::all()->where('comercio_id', $this->comercioId);
        $this->mesas = Mesa::where('comercio_id', $this->comercioId)
            ->where('estado', 'Disponible')->get();

        $mozos = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('uc.comercio_id', $this->comercioId)->select('users.*')->orderBy('apellido')->get();

        if(strlen($this->search) > 0){
            $this->reservas = Reserva::join('mesas as m', 'm.id', 'reservas.mesa_id')
                ->select('reservas.*', 'm.descripcion as mesa')
                ->where('reservas.nombre', 'like', '%' .  $this->search . '%')
                ->where('reservas.comercio_id', $this->comercioId)
                ->orWhere('reservas.apellido', 'like', '%' .  $this->search . '%')
                ->where('reservas.comercio_id', $this->comercioId)
                ->orWhere('m.descripcion', 'like', $this->search)
                ->where('m.comercio_id', $this->comercioId)->get();
        }else{
            $this->reservas = Reserva::join('mesas as m', 'm.id', 'reservas.mesa_id')
            ->select('reservas.*', 'm.descripcion as mesa')->where('reservas.comercio_id', $this->comercioId)->get();
        }
       
        switch ($this->estadoMesa) {
            case '1': //todas
                $this->info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', '<>', 'Deshabilitada')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
             case '2': //disponibles
                $this->info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'Disponible')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            case '3': //ocupadas
                $this->info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'Ocupada')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            case '4': //c/factura 
                $this->info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'C/factura')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            case '5': //canceladas 
                $this->info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'Cancelada')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            case '6': //reservadas
                $this->info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'Reservada')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            case '7': //deshabilitadas
                $this->info = Mesa::join('sectores as s', 's.id', 'mesas.sector_id')
                    ->select('mesas.*', 's.descripcion as mesa')
                    ->where('mesas.comercio_id', $this->comercioId)
                    ->where('s.descripcion', $this->tab)
                    ->where('mesas.estado', 'Deshabilitada')
                    ->orderBy('mesas.descripcion', 'asc')->get();
                break;
            default:
        }
        return view('livewire.reservas-estado-mesas.component' , [
            'info'     => $this->info,
            'sectores' => $this->sectores,
            'mesas'    => $this->mesas,
            'reservas' => $this->reservas,
            'mozos'    => $mozos
        ]);
    }
    protected $listeners = [
        'abrirMesa'        => 'abrirMesa',
        'agregaMozo'       => 'agregaMozo',
        'deshabilitarMesa' => 'deshabilitarMesa',
        'habilitarMesa'    => 'habilitarMesa'
    ];

    public function doAction($action)
    {
        $this->action = $action;
    }
    public function resetInput()
    {
        $this->nombre        = '';
        $this->apellido      = null;
        $this->telefono      = null;
        $this->hora          = null;
        $this->cantidad      = '';
        $this->mesa          = 'Elegir';
        $this->tab           = 'Interior';
        $this->estadoMesa    = '1';
        $this->selected_id   = 0;
        $this->comentario    = null;
        $this->mesa_anterior = '';
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
        $info = json_decode($data);        

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
                if($buscar_mesa->estado == 'Disponible' || $buscar_mesa->estado == 'Reservada'){
                    $this->emit('agregarMozo', $mesa);
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
        $this->comentario    = $record->comentario;
        $this->mesa          = $record->mesa_id;
        $this->mesa_anterior = $record->mesa_id;
        
        $this->doAction(3);
    }
    public function StoreOrUpdate()
    {
        $this->validate(['mesa' => 'not_in:Elegir']);
           
        $this->validate([
            'nombre' => 'required', 
            'cantidad' => 'required'
        ]);
        
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
                    'hora'        => $this->hora,
                    'cantidad'    => $this->cantidad,
                    'mesa_id'     => $this->mesa,
                    'comentario'  => $this->comentario,
                    'comercio_id' => $this->comercioId            
                ]);
                $record = Mesa::find($this->mesa);
                $record->update(['estado' => 'Reservada']);
            }else {   
                $record = Reserva::find($this->selected_id);
                $record->update([
                    'nombre'      => mb_strtoupper($this->nombre),            
                    'apellido'    => mb_strtoupper($this->apellido),             
                    'telefono'    => $this->telefono,
                    'hora'        => $this->hora,
                    'cantidad'    => $this->cantidad,
                    'mesa_id'     => $this->mesa,
                    'comentario'  => $this->comentario,
                ]); 
                $record = Mesa::find($this->mesa_anterior);
                $record->update(['estado' => 'Disponible']);
                $record = Mesa::find($this->mesa);
                $record->update(['estado' => 'Reservada']);
            }             
            if($this->selected_id) session()->flash('msg-ok', 'Reserva Actualizada');    
            else session()->flash('msg-ok', 'Reserva Creada'); 

            DB::commit();            
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        return;
    }
}
