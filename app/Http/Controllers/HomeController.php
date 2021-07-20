<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArqueoGral;
use App\Models\Comercio;
use App\Models\UsuarioComercio;
use App\Models\UsuarioComercioPlanes;
use Carbon\Carbon;

class HomeController extends Controller
{

    public $comercioId, $arqueoGralId;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {     
        
        $this->middleware('auth');
    }
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth()->user()->id != 1)
        {
            //inicializa la variable de session idComercio con el comercio asignado al usuario actual
            $userComercio = UsuarioComercio::select('id','comercio_id')
                ->where('usuario_id', Auth()->user()->id)->get();
            session(['idComercio' => $userComercio[0]->comercio_id]); 
            $this->comercioId = session('idComercio'); 
            
            //averiguamos la hora de apertura del comercio para comprobar el arqueo
            $horaApertura = Comercio::select('hora_apertura')
                ->where('id', $this->comercioId)->first();

            //inicializa la variable de session idArqueoGral correspondiente al idComercio anterior
            $idArqueoGral = ArqueoGral::where('estado', '1')
                ->where('comercio_id', $this->comercioId)->get();
            if($idArqueoGral->count()){ //si existe algún arqueo abierto, capturamos la fecha de creación
                session(['idArqueoGral' => $idArqueoGral[0]->id]); //capturamos su id solo para usarlo en el arqueo gral
                $date = Carbon::parse($idArqueoGral[0]->created_at);
                //obtenemos solo la fecha actual y le agregamos la hora para que sea el final del día
                $hoy = Carbon::now();
                $hoy_solo_fecha = Carbon::parse($hoy)->format('Y-m-d');
                $hoy = $hoy_solo_fecha . ' 23:59:59';
                //comparamos las dos fecha/hora
                $diff = $date->diffInDays($hoy);
         
                $hora_actual = Carbon::now()->format('H:i');
                //si estamos en el mismo día 'o' es el día siguiente 
                //y 'no' es más tarde que la hora de apertura del comercio
                if($diff == 0 || $diff == 1 && $hora_actual <= $horaApertura->hora_apertura){
                    session(['estadoArqueoGral' => 'activo']); 
                }else{    //sino, debemos hacer el arqueo 'si o si', e igualamos la variable a cero
                    session(['estadoArqueoGral' => 'pendiente']);
                }     
            }else{
                session(['idArqueoGral' => -1]);
                session(['estadoArqueoGral' => 'no existe']); //si no existe ningún arqueo para este comercio, 
                    //igualamos la variable a 'no_existe', para indicar que hay iniciar el arqueo al
                    //inicializar alguna Caja. 
            }               
            $this->estadoAqueoGral = session('estadoArqueoGral');

            //verifica si hay un arqueo cerrado con la misma fecha que hoy
            //en tal caso, no permitimos abrir otro hasta el día siguiente, después del horario de apertura del local
            if($this->estadoAqueoGral == 'no existe'){
                $idArqueoGral = ArqueoGral::where('estado', '0')
                    ->where('comercio_id', $this->comercioId)->orderBy('created_at', 'desc')->get();
                if($idArqueoGral->count()){
                    $date = Carbon::parse($idArqueoGral[0]->created_at);
                    $hoy = Carbon::now();
                    $hoy_solo_fecha = Carbon::parse($hoy)->format('Y-m-d');
                    $hoy = $hoy_solo_fecha . ' 23:59:59';
                    $diff = $date->diffInDays($hoy);
                    $diff = $date->diffInDays($hoy);
                    $hora_actual = Carbon::now()->format('H:i');
                    if($diff == 0 || $diff == 1 && $hora_actual <= $horaApertura->hora_apertura){
                        session(['estadoArqueoGral' => 'ya existe']);
                        $this->estadoAqueoGral = session('estadoArqueoGral');
                    }
                }
            }

            //verificaciones de planes
            $fecha_actual = Carbon::now();      
            $estado = UsuarioComercioPlanes::select('*')
                ->where('comercio_id', $this->comercioId)
                ->orderBy('id', 'desc')->first();
            
            if($estado->estado_plan == 'finalizado' && $estado->plan_id == 1)
            {
                return view('livewire.admin.mensajes.prueba_finalizada');
            } 

            if($estado->estado_plan == 'finalizado' && $estado->estado_pago == 'pagado')
            {
                return view('livewire.admin.mensajes.plan_finalizado');
            } 

            if($estado->estado_plan == 'activo' && $estado->estado_pago == 'en mora')
            {
                return view('livewire.admin.mensajes.plan_en_mora');
            }   

            if($estado->estado_plan == 'activo')
            {
                if($this->estadoAqueoGral == 'pendiente') return view('livewire.admin.mensajes.forzar_arqueo');
                else return view('home');
            }              
            
            if($estado->estado_plan == 'suspendido')
            {
                return view('livewire.admin.mensajes.plan_suspendido');
            }
        }        
        else
        {                
            return view('abonados');
        }  
    }
    public function notificado()
    {
        return view('home');
    }
}