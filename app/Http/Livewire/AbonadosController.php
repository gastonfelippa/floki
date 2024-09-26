<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Plan;
use App\Models\UsuarioComercio;
use App\Models\UsuarioComercioPlanes;
use Carbon\Carbon;
use DB;

class AbonadosController extends Component
{
    public $selected_id = null, $action = 1;  
    public $abonados, $activos, $en_mora;
    public $search, $estadoPlan = 'activo';
    public $nombre, $nombreComercio, $planActual, $estadoPago, $fechaFin, $fechaVto, $fechaInicio, $planes;
    public $plan="Elegir", $comentarios, $pagado = false;
    public $duracion, $importe, $fechaInicioNuevo, $fechaFinNuevo, $fechaVtoNuevo;
    public $usuarioComercioId, $estado_pago = 'no vencido', $importeAgrabar;
    
    
    public function render()
    { 
        $fecha_actual = Carbon::now();  
        
        $this->abonados = 0;
        $this->activos = 0;
        $this->en_mora = 0;
        $abonados = UsuarioComercioPlanes::distinct('usuariocomercio_id')->count();
        $activos = UsuarioComercioPlanes::where('estado_plan', 'activo')->count();
        $en_mora = UsuarioComercioPlanes::where('estado_pago', 'en mora')->count();
        $this->abonados = $abonados;
        $this->activos = $activos;
        $this->en_mora = $en_mora;  

        $this->planes = Plan::select()->orderBy('id', 'asc')->get();

        $dPlan = Plan::find($this->plan);
        if($dPlan != null) {
            $this->importeAgrabar = $dPlan->precio;
            $this->importe = '$ ' . $dPlan->precio;
            $this->duracion = $dPlan->duracion . ' días';
        }else {
            $this->importe = '';
            $this->duracion = '';
        }  

        $hoy = Carbon::now()->locale('en');      //inicializo fecha_inicio con la fecha en que se suscribe al sistema
        $mes = $hoy->monthName;                  //recupero el mes        
        Carbon::setTestNow($hoy);                //habilito a Carbon para que actúe sobre fecha_inicio
        $fi = new Carbon('first day of ' . $mes);
        $fvto = $fi->addDays(9);
        $ff = new Carbon('last day of ' . $mes);

        $hoy = Carbon::now()->locale('en');      //inicializo fecha_inicio con la fecha en que se suscribe al sistema
        $mes = $hoy->monthName;                  //recupero el mes 
        Carbon::setTestNow($hoy);                //habilito a Carbon para que actúe sobre fecha_inicio
        $fi = new Carbon('first day of ' . $mes); //actualizo la fecha de inicio       

        $this->fechaInicioNuevo = Carbon::parse($fi)->format('Y,m,d') . ' 00:00:00';
        $this->fechaFinNuevo = Carbon::parse($ff)->format('Y,m,d') . ' 23:59:59';
        $this->fechaVtoNuevo = Carbon::parse($fvto)->format('Y,m,d') . ' 23:59:59';

        if(strlen($this->search) > 0) {
            $info = UsuarioComercioPlanes::join('usuario_comercio as uc', 'uc.id', 'usuariocomercio_planes.usuariocomercio_id')
                ->join('users as u', 'u.id', 'uc.usuario_id')
                ->join('comercios as c', 'c.id', 'uc.comercio_id')
                ->join('planes as p', 'p.id', 'usuariocomercio_planes.plan_id')
                ->select('u.name', 'u.apellido', 'u.email', 'u.telefono1', 'c.nombre as nombrecomercio',
                        'p.descripcion', 'usuariocomercio_planes.estado_plan', 'usuariocomercio_planes.fecha_fin',
                        'usuariocomercio_planes.fecha_vto', 'usuariocomercio_planes.id', 'usuariocomercio_planes.estado_pago')
                ->where('u.apellido', 'like', '%' . $this->search .'%')
                ->orWhere('u.name', 'like', '%' . $this->search .'%')
                ->orWhere('c.nombre', 'like', '%' . $this->search .'%')
                ->orWhere('u.email', 'like', '%' . $this->search .'%')
                ->orWhere('p.descripcion', 'like', '%' . $this->search .'%')
                ->orWhere('usuariocomercio_planes.estado_plan', 'like', '%' . $this->search .'%')
                ->orWhere('usuariocomercio_planes.estado_pago', 'like', '%' . $this->search .'%')
                ->orderBy('usuariocomercio_planes.fecha_fin', 'desc')->get();
        } else {            
            $info = UsuarioComercioPlanes::join('usuario_comercio as uc', 'uc.id', 'usuariocomercio_planes.usuariocomercio_id')
            ->join('users as u', 'u.id', 'uc.usuario_id')
            ->join('comercios as c', 'c.id', 'uc.comercio_id')
            ->join('planes as p', 'p.id', 'usuariocomercio_planes.plan_id')
            ->select('usuariocomercio_planes.*', 'u.name', 'u.apellido', 'u.email', 'u.telefono1', 'c.nombre as nombrecomercio',
                    'p.descripcion')
            ->where('usuariocomercio_planes.estado_plan', $this->estadoPlan)
            ->orderBy('u.apellido', 'asc')->get();
        }        
        
        return view('livewire.admin.abonados',[
            'info'  => $info,
            'fi'    =>$fi,
            'ff'    =>$ff,
            'fvto'  =>$fvto
            ]);
    }

    public function resetInput()
    {
        $this->plan = 'Elegir';
        $this->comentarios = '';
        $this->pagado = false;
    }

    public function doAction($action, $id)
	{
        $this->resetInput();
        $this->action = $action;
        $this->selected_id = $id;

        $record = UsuarioComercioPlanes::join('usuario_comercio as uc', 'uc.id', 'usuariocomercio_planes.usuariocomercio_id')
            ->join('users as u', 'u.id', 'uc.usuario_id')
            ->join('comercios as c', 'c.id', 'uc.comercio_id')
            ->join('planes as p', 'p.id', 'usuariocomercio_planes.plan_id')
            ->select('usuariocomercio_planes.*', 'u.name', 'u.apellido', 'u.email', 'u.telefono1', 
                    'c.nombre as nombrecomercio', 'p.descripcion')
            ->where('usuariocomercio_planes.id', $id)->get();
      
        $this->usuarioComercioId = $record[0]->usuariocomercio_id;
        $this->nombre = $record[0]->apellido . ', ' . $record[0]->name;
        $this->nombreComercio = $record[0]->nombrecomercio;
        $this->planActual = $record[0]->descripcion;
        $this->estadoPlan = $record[0]->estado_plan;
        $this->estadoPago = $record[0]->estado_pago;
        $this->fechaInicio = $record[0]->fecha_inicio_periodo;
        $this->fechaFin = $record[0]->fecha_fin;
        $this->fechaVto = $record[0]->fecha_vto;
    }

    public function edit($id)
    {
        $record = UsuarioComercioPlanes::findOrFail($id);
        $this->nombre = $record->usuariocomercio_id;
        $this->planActual = $record->plan_id;
        $this->estadoPlan = $record->estado_plan;
        $this->estadoPago = $record->estado_pago;
        $this->fechaFin = $record->fecha_fin;
    }

    public function StoreOrUpdate()
    {         
        $this->validate([
			'plan' => 'not_in:Elegir'
        ]);

        if($this->pagado == false){
            $estadoPagoNuevo = 'no vencido';
        }else{
            $estadoPagoNuevo = 'pagado';
        }             
        DB::begintransaction();                //iniciar transacción para grabar
        try{       
            $plan = UsuarioComercioPlanes::create([
                'usuariocomercio_id' => $this->usuarioComercioId,
                'plan_id' => $this->plan,
                'estado_plan' => 'activo',
                'importe' => $this->importeAgrabar,
                'estado_pago' => $estadoPagoNuevo,
                'fecha_inicio_periodo' =>$this->fechaInicioNuevo,
                'fecha_fin' => $this->fechaFinNuevo,
                'fecha_vto' => $this->fechaVtoNuevo,
                'comentarios' => $this->comentarios         
            ]);                 
            DB::commit();                   //confirmar la transaccion
            if($this->selected_id > 0){		
                session()->flash('message', 'Plan Actualizado');       
            }else{ 
                session()->flash('message', 'Plan nuevo Creado'); 
            }           
        }catch (Exception $e){
            DB::rollback();    //en caso de error, deshacemos para no generar inconsistencia de datos
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El plan nuevo no se grabó...');
        } 
        $this->resetInput();
        $this->doAction(1,1); 
    }

    public function Cobrar()
    {
        $fecha_actual = Carbon::now();
        $plan = UsuarioComercioPlanes::find($this->selected_id);
        if($fecha_actual->gt($this->fechaVto)){
            $estadoPlan = 'finalizado';
        }else{
            $estadoPlan = 'activo';
        }
        $plan->update([
            'estado_plan' => $estadoPlan,          
            'estado_pago' => 'pagado'
        ]);
        $this->resetInput();
        $this->doAction(1,1); 
    }

    protected $listeners = [
        'deleteRow'=>'destroy',
        'cobrarPlan' => 'Cobrar'       
    ]; 

        //método para eliminar un registro dado
    public function destroy($id)
    {
        if ($id) { //si es un id válido
            $record = UsuarioComercioPlanes::where('id', $id); //buscamos el registro
            $record->delete(); //eliminamos el registro
            $this->resetInput();
            $this->doAction(1,1);
        }
    }
}
