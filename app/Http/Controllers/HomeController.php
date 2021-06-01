<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsuarioComercio;
use App\Models\UsuarioComercioPlanes;
use Carbon\Carbon;

class HomeController extends Controller
{

    public $comercioId;
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
         
            //verificaciones de planes
            $fecha_actual = Carbon::now();      
            $estado = UsuarioComercioPlanes::select('*')
            ->where('comercio_id', $this->comercioId)
            ->orderBy('id', 'desc')->first();
            // ->where('usuariocomercio_planes.usuariocomercio_id', $userComercio[0]->id)
            
            if($estado->estado_plan == 'completado' && $estado->plan_id == 1)
            {
                return view('livewire.admin.mensajes.prueba_completada');
            } 

            if($estado->estado_plan == 'completado' && $estado->estado_pago == 'pagado')
            {
                return view('livewire.admin.mensajes.plan_completado');
            } 

            if($estado->estado_plan == 'activo' && $estado->estado_pago == 'en mora')
            {
                return view('livewire.admin.mensajes.plan_en_mora');
            }   

            if($estado->estado_plan == 'activo')
            {
                return view('home');
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