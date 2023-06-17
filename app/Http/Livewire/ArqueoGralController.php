<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use App\Models\ArqueoGral;
use App\Models\Auditoria;
use App\Models\CajaInicial;
use App\Models\CajaUsuario;
use App\Models\Factura;
use App\Models\Gasto;
use App\Models\ModelHasRole;
use App\Models\MovimientoDeCaja;
use App\Models\OtroIngreso;
use App\Models\Recibo;
use App\Models\User;
use Carbon\Carbon;
use DB;

class ArqueoGralController extends Component
{
    
    public $fecha, $fecha_inicio, $nro_arqueo, $user = 0, $factPendiente;
    public $cajaInicial, $ventas, $cobrosCtaCte, $otrosIngresos, $egresos, $cajaFinal, $cajaFinalUsuarios;
    public $diferencia, $totalIngresos, $Arqueo;
    public $comercioId, $selected_id = null, $importe, $nro_caja = 1, $comentario='', $caja_abierta = 0;
    public $arqueoGralId, $estadoArqueoGral, $sumaImporte, $usuario_habilitado = 1;

    public function mount()
    {
        $this->cajaInicial = 0;
        $this->ventas = 0;
        $this->cobrosCtaCte = 0;
        $this->otrosIngresos = 0;
        $this->egresos = 0;
        $this->cajaFinal = 0;
    }

    public function render()
    {
            //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        $this->arqueoGralId = session('idArqueoGral');
        $this->estadoArqueoGral = session('estadoArqueoGral');
        session(['facturaPendiente' => null]);  

        if($this->arqueoGralId > 0) {    //si hay un arqueo abierto o pendiente

            //primero verifico si el usuario logueado es el Administrador del Sistema, en tal caso
            //no hago ninguna validación y le permito hacer cualquier procedimiento
            $usuadrioAdmin = ModelHasRole::join('roles as r', 'r.id', 'model_has_roles.role_id')
                ->join('users as u', 'u.id', 'model_has_roles.model_id')
                ->where('r.alias', 'Administrador')
                ->where('r.comercio_id', $this->comercioId)->select('u.id')->get();
            if($usuadrioAdmin[0]->id <> auth()->user()->id){
                //si no es el Admin, verifico si el usuario logueado es quien inició el Arqueo Gral, en caso de existir
                //si no lo es, muestro un mensaje y no lo dejo continuar
                $usuarioArqueo = CajaUsuario::where('user_id', auth()->user()->id)
                        ->where('arqueo_gral_id', $this->arqueoGralId)->get();
                if($usuarioArqueo->count() == 0) $this->usuario_habilitado = 0;
            }

        }   
        if($this->estadoArqueoGral == 'no existe'){
            $this->caja_abierta = 2; //deshabilitar botón
        }else{
            //averiguamos si hay alguna Caja abierta, y en tal caso cambiamos el texto del botón
            //para indicar la acción que debe realizar
            $cajaAbierta = CajaUsuario::where('arqueo_gral_id', $this->arqueoGralId)
                ->where('estado', '1')->select('id')->get();   
            if($cajaAbierta->count()) $this->caja_abierta = 1; //habilito para cerrar Cajas abiertas
                                                        //habilito para hacer arqueo '0'
        }
        
        //'$this->Arqueo' se usa para completar las tarjetas de inicio
        if($this->estadoArqueoGral <> 'no existe') $this->Arqueo();
        //obtenemos el total de Caja Inicial por cada Caja
        $infoCajaInicial = CajaUsuario::join('caja_inicials as ci', 'ci.caja_user_id', 'caja_usuarios.id')
            ->join('cajas as c', 'c.id', 'caja_usuarios.caja_id')  
            ->where('caja_usuarios.arqueo_gral_id', $this->arqueoGralId)
            ->select('caja_usuarios.created_at', 'c.descripcion', 'ci.caja_user_id', DB::RAW("'' as sumaImporte"))
            ->groupBy('caja_usuarios.created_at', 'c.descripcion', 'ci.caja_user_id')->get(); 
        foreach($infoCajaInicial as $i){
            $infoCI = CajaInicial::where('caja_user_id', $i->caja_user_id)
                ->sum('importe');
            $i->sumaImporte = $infoCI;
        }

        //obtenemos los totales de ventas, cobros y otros ingresos pos caja y al final los sumamos
        $listaVentas = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
            ->where('caja_usuarios.arqueo_gral_id', $this->arqueoGralId)
            ->select('caja_usuarios.id', 'c.descripcion', 'caja_usuarios.created_at', 
            DB::RAW("'' as sumaVentas"), DB::RAW("'' as sumaCobros"), DB::RAW("'' as sumaOtIngresos"), DB::RAW("'' as sumaIngresosTotal"))
            ->groupBy('caja_usuarios.id', 'c.descripcion', 'caja_usuarios.created_at')->get();
        foreach ($listaVentas as $i){
            $infoV = Factura::where('facturas.arqueo_id', $i->id)
                ->where('estado', 'contado')
                ->sum('importe');
            $i->sumaVentas = $infoV;
        }
        foreach ($listaVentas as $i){
            $infoC = Recibo::join('det_metodo_pagos as det', 'det.recibo_id', 'recibos.id')
                ->where('recibos.arqueo_id', $i->id)
                ->sum('det.importe');
            $i->sumaCobros = $infoC;
        }
        foreach ($listaVentas as $i){
            $infoI = MovimientoDeCaja::where('arqueo_id', $i->id)
            ->where('movimiento_de_cajas.ingreso_id', '<>', null)
            ->sum('importe');
            $i->sumaOtIngresos = $infoI;
        }
        foreach ($listaVentas as $i){

            $i->sumaIngresosTotal = $i->sumaVentas + $i->sumaCobros + $i->sumaOtIngresos;
        }

        //obtenemos los egresos
        $listaEgresos = MovimientoDeCaja::join('caja_usuarios as cu', 'cu.id', 'movimiento_de_cajas.arqueo_id')
            ->join('cajas as c', 'c.id', 'cu.caja_id')    
            ->where('cu.arqueo_gral_id', $this->arqueoGralId)
            ->where('movimiento_de_cajas.egreso_id', '<>', null)
            ->select('movimiento_de_cajas.arqueo_id', 'c.descripcion', 'cu.created_at', DB::RAW("'' as sumaImporte"))
            ->groupBy('movimiento_de_cajas.arqueo_id', 'c.descripcion', 'cu.created_at')->get();
        foreach($listaEgresos as $i){
            $infoE = MovimientoDeCaja::where('arqueo_id', $i->arqueo_id)
                ->where('movimiento_de_cajas.egreso_id', '<>', null)
                ->sum('importe');
            $i->sumaImporte = $infoE;
        } 

        return view('livewire.arqueo_general.component',[
            'infoCajaInicial' => $infoCajaInicial,
            'listaVentas' => $listaVentas,
            'listaEgresos' => $listaEgresos
        ]);
    }
    public function resetInput()
    {
        $this->user = 0;
        $this->selected_id = null;
    }
    protected $listeners = [
        'infoToPrintCorte' => 'PrintCorte',
        'cerrarArqueoGral' => 'cerrarArqueoGral'
    ];




//////// Hacer una vista o modal para dejar la Caja del día siguiente









    public function Arqueo()
    {   
        //suma de todas las cajas iniciales del día
        $this->cajaInicial = CajaInicial::join('caja_usuarios as cu', 'cu.id', 'caja_inicials.caja_user_id')
            ->join('cajas as c', 'c.id', 'cu.caja_id')
            ->where('cu.arqueo_gral_id', $this->arqueoGralId)
            ->sum('importe');
        //suma de todas las ventas de contado del día
        $this->ventas = Factura::join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
            ->join('arqueo_grals as ag', 'ag.id', 'cu.arqueo_gral_id')
            ->where('facturas.comercio_id', $this->comercioId)
            ->where('cu.arqueo_gral_id', $this->arqueoGralId)
            ->where('facturas.estado', 'contado')
            ->where('facturas.estado_pago', '1')
            ->sum('importe');
        //suma de los recibos de pago total más las entregas del día
        $this->cobrosCtaCte = Recibo::join('caja_usuarios as cu', 'cu.id', 'recibos.arqueo_id')
            ->join('det_metodo_pagos as det', 'det.recibo_id', 'recibos.id')
            ->where('cu.arqueo_gral_id', $this->arqueoGralId)
            ->sum('det.importe');
        //suma de otros ingresos del día
        $this->otrosIngresos = MovimientoDeCaja::join('caja_usuarios as cu', 'cu.id', 'movimiento_de_cajas.arqueo_id')
            ->where('cu.arqueo_gral_id', $this->arqueoGralId)
            ->where('movimiento_de_cajas.ingreso_id', '<>', null)
            ->sum('importe');
        //suma los egresos del día
        $this->egresos = MovimientoDeCaja::join('caja_usuarios as cu', 'cu.id', 'movimiento_de_cajas.arqueo_id')
            ->where('cu.arqueo_gral_id', $this->arqueoGralId)
            ->where('movimiento_de_cajas.egreso_id', '<>', null)
            ->sum('importe');
        //suma las diferencias de caja del día
        $this->cajaFinalUsuarios = CajaUsuario::where('arqueo_gral_id', $this->arqueoGralId)->sum('diferencia');
   // dd($this->cajaFinalUsuarios);
        $this->cajaFinal = $this->cajaInicial + $this->ventas + $this->cobrosCtaCte + 
                           $this->otrosIngresos - $this->egresos;                 
        $this->totalIngresos = $this->ventas + $this->cobrosCtaCte + $this->otrosIngresos;
        if($this->cajaFinalUsuarios != 0){
            $this->cajaFinalUsuarios = $this->cajaFinal + $this->cajaFinalUsuarios;
        }else{
            $this->cajaFinalUsuarios = $this->cajaFinal;
        }
        $this->diferencia = $this->cajaFinalUsuarios - $this->cajaFinal;
    }    
    public function cerrarArqueoGral($proximaCajaChica)
    { 


        ///acá debo determinar cuánto dejo para la Caja Chica y cuánto se va para la Caja Gral.
        ///incluído cheques



        DB::begintransaction();
        try{
            $record = ArqueoGral::where('id', $this->arqueoGralId);
            $record->update([
                'estado'     => '0',
               // 'caja_final' => ,       //según sistema
               // 'diferencia' =>
            ]);
            DB::commit();               
            $this->emit('arqueoCerrado');
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! La Caja no se cerró...');
        }
        $this->resetInput();
        return;
    }
    public function PrintCorte($ventas, $entradas, $salidas, $Arqueo)
    {
        $nombreImpresora = "HP Laserjet Pro M12w";
        $connector = new WindowsPrintConnector($nombreImpresora);
        $impresora = new Printer($connector);

        $impresora->setJustification(Printer::JUSTIFY_CENTER);
        $impresora->setTextSize(2,2);

        //tarea: sacar info de la tabla empresa
        $impresora->text("SYSPARKING PLAZA \n");
        $impresora->setTextSize(1,1);
        $impresora->text("** Arqueo de Caja ** \n\n");

        $impresora->setJustification(Printer::JUSTIFY_LEFT);
        $impresora->text("============================\n");
        $impresora->text("Usuario: " . ($this->user == null ? 'Todos' : $this->user) . "\n");
        $impresora->text("Fecha: " . ($this->fecha == null ? date('d/m/Y h:i:s a' , time()) : Carbon::parse($this->fecha)->format('d,m,Y')) . "\n");
            
        $impresora->text("----------------------------\n");
        $impresora->text("Ventas: $" . number_format($this->ventas,2)) . "\n";
        $impresora->text("Entradas: $" . number_format($this->entradas,2)) . "\n";
        $impresora->text("Salidas: $" . number_format($this->salidas,2)) . "\n";
        $impresora->text("Arqueo: $" . number_format($this->Arqueo,2)) . "\n";
        $impresora->text("============================\n");

        $impresora->feed(3);
        $impresora->cut();
        $impresora->close();
    }
}
