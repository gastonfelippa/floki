<?php

namespace App\Http\Livewire;

use Illuminate\Support\Arr;

use Livewire\Component;
use App\Models\UsuarioComercioPlanes;
use App\Models\Plan;
use App\Models\Proceso;
use App\Models\RegistroProceso;
use Carbon\Carbon;
use DB;

class ProcedimientosAdminController extends Component
{
    public $planes, $plan ='Elegir';
    public $btnRAP, $btnPPC, $btnPAM, $btnPAI;
    public $RAP_id, $PPC_id, $PAM_id, $PAI_id;

    public function render()
    {
        $planes = UsuarioComercioPlanes::all();
        $this->planes = $planes;

        $fecha_actual = Carbon::now();
        $day = $fecha_actual->format('d');

        //habilitar el boton solo el dia que corresponda de cada mes
        $this->btnRAP = false;
        $this->btnPPC = false;
        $this->btnPAM = false;
        $this->btnPAI = false;

        $diaProceso = Proceso::all();
        foreach($diaProceso as $dia)
        {
            if($dia->descripcion == 'Renovación Automática De Planes' && $day == $dia->dia_ejecucion){
                $this->btnRAP = true;
                $this->RAP_id = $dia->id;
            }
            if($dia->descripcion == 'Plan De Prueba Finalizado' && $day == $dia->dia_ejecucion){
                $this->btnPPC = true;
                $this->PPC_id = $dia->id;
            } 
            if($dia->descripcion == 'Plan Activo En Mora' && $day == $dia->dia_ejecucion){
                $this->btnPAM = true;
                $this->PAM_id = $dia->id;
            } 
            if($dia->descripcion == 'Plan Activo Impago' && $day == $dia->dia_ejecucion){
                $this->btnPAI = true;
                $this->PAI_id = $dia->id;
            }
        } 
        
        return view('livewire.admin.procedimientos');
    }

    public function planDePruebaFinalizado()
    {
        $fecha_actual = Carbon::now();
        $cambios = 0;
        foreach($this->planes as $plan) 
        {
            //verifica si el plan actual es de prueba y no esta vencido
            if($fecha_actual->gt($plan->fecha_fin) && $plan->plan_id == 1 && $plan->estado_plan <> 'finalizado')
            {  
                $plan->update(['estado_plan' => 'finalizado']); //si se cumple la condición, le da salida
                $cambios += 1;
            }
        }

        RegistroProceso::create(['proceso_id' =>  $this->PPC_id, 'cambios' => $cambios]);

        if($cambios == 0){
            session()->flash('message', 'No hubo cambios en la BD...');
            return;
        }elseif($cambios == 1){
            session()->flash('message', 'El proceso se efectuó correctamente en ' . $cambios . ' abonado!!!'); 
            return;     
        }else{
            session()->flash('message', 'El proceso se efectuó correctamente en ' . $cambios . ' abonados!!!');
            return;      
        }
    }
    public function planActivoEnMora()
    {
        $fecha_actual = Carbon::now();
        $cambios = 0;

        foreach($this->planes as $plan) 
        {
            //verifica si el plan actual es de pago y está en mora
            if($fecha_actual->gt($plan->fecha_vto) && $plan->plan_id <> 1 
                && $plan->estado_plan == 'activo' && $plan->estado_pago <> 'en mora' && $plan->estado_pago <> 'pagado')
            {  
                $plan->update(['estado_pago' => 'en mora']); //si se cumple la condición, le da salida
                $cambios += 1;
            }
        }  
        
        RegistroProceso::create(['proceso_id' =>  $this->PAM_id, 'cambios' => $cambios]);

        if($cambios == 0){
            session()->flash('message', 'No hubo cambios en la BD...');
        }elseif($cambios == 1){
            session()->flash('message', 'El proceso se efectuó correctamente en ' . $cambios . ' abonado!!!');      
        }else{
            session()->flash('message', 'El proceso se efectuó correctamente en ' . $cambios . ' abonados!!!');      
        }
        
    }
    public function planActivoImpago()
    {
        $fecha_actual = Carbon::now();
        $cambios = 0;

        foreach($this->planes as $plan) 
        {
            //verifica si el plan actual es de pago y está vencido
            $diferencia = $fecha_actual->diffInDays($plan->fecha_vto);
            if($fecha_actual->gt($plan->fecha_vto) && $diferencia > 5 && $plan->plan_id <> 1 && $plan->estado_plan == 'activo' && $plan->estado_pago <> 'pagado')
            {  
                $plan->update([
                    'estado_plan' => 'suspendido',
                    'estado_pago' => 'impago']); 
                $cambios += 1;
            }
        }

        RegistroProceso::create(['proceso_id' =>  $this->PAI_id, 'cambios' => $cambios]);

        if($cambios == 0){
            session()->flash('message', 'No hubo cambios en la BD...');
        }elseif($cambios == 1){
            session()->flash('message', 'El proceso se efectuó correctamente en ' . $cambios . ' abonado!!!');      
        }else{
            session()->flash('message', 'El proceso se efectuó correctamente en ' . $cambios . ' abonados!!!');      
        }
    }

    public function renovacionAutomaticaPlanes()
    {        
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

        $fechaInicioNuevo = Carbon::parse($fi)->format('Y,m,d') . ' 00:00:00';
        $fechaFinNuevo = Carbon::parse($ff)->format('Y,m,d') . ' 23:59:59';
        $fechaVtoNuevo = Carbon::parse($fvto)->format('Y,m,d') . ' 23:59:59';

        $cant_planes_a_renovar = UsuarioComercioPlanes::distinct('usuariocomercio_id')
                                ->where('estado_plan', 'finalizado')->count();

        $planes_a_renovar = UsuarioComercioPlanes::select('usuariocomercio_id')
                                ->distinct()
                                ->where('estado_plan', 'finalizado')->get();
                                
        $planes = Plan::where('id', '2')->get();
 
        DB::begintransaction();                //iniciar transacción para grabar
        try
        {       
            foreach($planes_a_renovar as $plan)
            {
                UsuarioComercioPlanes::create([
                    'usuariocomercio_id' => $plan->usuariocomercio_id,
                    'plan_id' => $planes[0]->id,
                    'estado_plan' => 'activo',
                    'importe' => $planes[0]->precio,
                    'estado_pago' => 'no vencido',
                    'fecha_inicio_periodo' =>$fechaInicioNuevo,
                    'fecha_fin' => $fechaFinNuevo,
                    'fecha_vto' => $fechaVtoNuevo,
                    'comentarios' => 'Renovado automáticamente'         
                ]);                
            }

            RegistroProceso::create(['proceso_id' =>  $this->RAP_id, 'cambios' => $cant_planes_a_renovar]);
            
            DB::commit();                  //confirmar la transaccion       
            if($cant_planes_a_renovar == 0){
                session()->flash('message', 'No hubo cambios en la BD...');
            }elseif($cant_planes_a_renovar == 1){
                session()->flash('message', 'El proceso se efectuó correctamente en ' . $cant_planes_a_renovar . ' abonado!!!');      
            }else{
                session()->flash('message', 'El proceso se efectuó correctamente en ' . $cant_planes_a_renovar . ' abonados!!!');      
            }     
        }catch (Exception $e){
            DB::rollback();    //en caso de error, deshacemos para no generar inconsistencia de datos
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! Las renovaciones de planes no se grabaron...');
        } 
    }
}
