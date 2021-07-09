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
    public $cajaInicial, $ventas, $cobrosCtaCte, $otrosIngresos, $egresos, $cajaFinal, $totalIngresos, $Arqueo;
    public $comercioId, $selected_id = null, $importe, $nro_caja = 1, $comentario='', $caja_abierta = 0;
    public $diferencia = 0, $dif, $arqueoGralId, $estadoArqueoGral;

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

        $users = CajaUsuario::join('users as u', 'u.id', 'caja_usuarios.caja_usuario_id')
            ->join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
            ->where('c.comercio_id', $this->comercioId)
            ->where('caja_usuarios.estado', '1')
            ->where('caja_usuarios.user_id', auth()->user()->id)
            ->select('u.id', 'u.name')->get();

        //estas variables se usan en las tablas
        $infoCajaInicial = CajaInicial::join('caja_usuarios as cu', 'cu.caja_id', 'caja_inicials.id')
            ->where('cu.id', $this->nro_arqueo)->get(); 

        $listaVentas = Factura::where('arqueo_id', $this->nro_arqueo)
            ->where('estado', 'contado')
            ->select('*', DB::RAW("'' as nomCli"))->get();
        foreach ($listaVentas as $i){
            if($i->cliente_id != null){
                $infoCli = Factura::join('clientes as c', 'c.id', 'facturas.cliente_id')
                    ->where('facturas.id', $i->id)
                    ->select('c.nombre', 'c.apellido')
                    ->get();
                $i->nomCli = $infoCli[0]->apellido . ' ' . $infoCli[0]->nombre;
            }else {
                $i->nomCli = '';
            }
        } 
        $listaCobros = Recibo::join('clientes as c', 'c.id', 'recibos.cliente_id')
            ->where('recibos.arqueo_id', $this->nro_arqueo)
            ->select('recibos.*', 'c.nombre', 'c.apellido')->get();
        $listaIngresos = MovimientoDeCaja::join('otro_ingresos as g', 'g.id', 'movimiento_de_cajas.ingreso_id')
            ->where('movimiento_de_cajas.ingreso_id', '<>', null)
            ->where('movimiento_de_cajas.arqueo_id', $this->nro_arqueo)
            ->select('g.descripcion', 'movimiento_de_cajas.importe')->get();
        $listaEgresos = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            ->where('movimiento_de_cajas.egreso_id', '<>', null)
            ->where('movimiento_de_cajas.arqueo_id', $this->nro_arqueo)
            ->select('g.descripcion', 'movimiento_de_cajas.importe')->get(); 

        // si el valor es '0', lo obligo a hacer el Arqueo Gral.
        // si es '1', todo sigue normal
        // y si es '-1', se creará un nuevo arqueo al habilitar la primer caja del día
        return view('livewire.cortes.arqueo_general',[
                'users' => $users,
                'infoCajaInicial' => $infoCajaInicial,
                'listaVentas' => $listaVentas,
                'listaCobros' => $listaCobros,
                'listaIngresos' => $listaIngresos,
                'listaEgresos' => $listaEgresos
            ]);
    }
    public function resetInput()
    {
        $this->user = 0;
        $this->selected_id = null;
    }

    protected $listeners = [
        'infoToPrintCorte'     => 'PrintCorte',
        'grabarCajaModal'      => 'grabarCajaModal',
        'deleteRow'            => 'destroy',
        'cambiarFecha'         => 'cambiarFecha',
        'userArqueo'           => 'userArqueo',
        'cerrarArqueoGral'     => 'cerrarArqueoGral',
        'calcular_diferencia'  => 'calcular_diferencia'
    ];
    public function userArqueo($puede_ver_otros)
    {
        if($puede_ver_otros == 0) $this->user = auth()->user()->id;
        else $this->user = 0;        
    }
    public function cambiarFecha($data)
    {
        if($data != '') $this->fecha = date('w',strtotime($data));
    }

    public function Arqueo()
    {     
        if($this->user == 0)  //calcula los montos totales del día
        {
            $this->cajaInicial = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
                ->join('caja_inicials as ci', 'ci.caja_user_id', 'caja_usuarios.id')
                ->where('c.comercio_id', $this->comercioId)
                ->where('caja_usuarios.arqueo_gral_id', $this->arqueoGralId)->sum('importe');
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
                ->where('cu.arqueo_gral_id', $this->arqueoGralId)
                ->sum('importe');
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
        }else{
            $this->cajaInicial = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
                ->join('caja_inicials as ci', 'ci.caja_user_id', 'caja_usuarios.id')
                ->where('caja_usuarios.caja_usuario_id', $this->user)
                ->where('caja_usuarios.estado', '1')->sum('importe');
            $this->ventas = Factura::join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
                ->where('cu.estado', '1')
                ->where('facturas.arqueo_id', $this->nro_arqueo)
                ->where('facturas.estado', 'contado')->sum('importe');
            $this->cobrosCtaCte = Recibo::whereDate('created_at', Carbon::today())
                ->where('comercio_id', $this->comercioId)->sum('importe');
            $this->otrosIngresos = MovimientoDeCaja::join('otro_ingresos as g', 'g.id', 'movimiento_de_cajas.ingreso_id')
                ->join('caja_usuarios as cu', 'cu.id', 'movimiento_de_cajas.arqueo_id')
                ->where('cu.estado', '1')
                ->where('movimiento_de_cajas.arqueo_id', $this->nro_arqueo) 
                ->where('movimiento_de_cajas.ingreso_id', '<>', null)->sum('importe');
            $this->egresos = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
                ->join('caja_usuarios as cu', 'cu.id', 'movimiento_de_cajas.arqueo_id')
                ->where('cu.estado', '1')
                ->where('movimiento_de_cajas.arqueo_id', $this->nro_arqueo) 
                ->where('movimiento_de_cajas.egreso_id', '<>', null)->sum('importe'); 

            // $listaEgresos = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            //     ->where('movimiento_de_cajas.egreso_id', '<>', null)
            //     ->where('movimiento_de_cajas.arqueo_id', $this->nro_arqueo)
            //     ->select('g.descripcion', 'movimiento_de_cajas.importe')->get(); 
        }
        $this->cajaFinal = $this->cajaInicial + $this->ventas + $this->cobrosCtaCte + 
                           $this->otrosIngresos - $this->egresos;
        $this->totalIngresos = $this->ventas + $this->cobrosCtaCte + $this->otrosIngresos;
    }

    public function Consultar()  //no se usa
    {
        
        $fi = Carbon::parse($this->fecha)->format('Y,m,d') . ' 00:00:00';
        $ff = Carbon::parse($this->fecha)->format('Y,m,d') . ' 23:59:59';

        if($this->user == 0){
            $this->cajaInicial = CajaInicial::whereDate('created_at',[$fi, $ff])->sum('importe');
            $this->ventas = Factura::where('comercio_id', $this->comercioId)
                ->whereDate('created_at', [$fi, $ff])
                ->where('estado', 'contado')->sum('importe');
            $this->cobrosCtaCte = Recibo::whereDate('created_at',[$fi, $ff])
                ->where('comercio_id', $this->comercioId)->sum('importe');
            $this->otrosIngresos = MovimientoDeCaja::join('otro_ingresos as g', 'g.id', 'movimiento_de_cajas.ingreso_id')
                ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
                ->where('movimiento_de_cajas.ingreso_id', '<>', null)
                ->whereDate('movimiento_de_cajas.created_at', [$fi, $ff])->sum('importe');
            $this->egresos = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
                ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
                ->where('movimiento_de_cajas.egreso_id', '<>', null)
                ->whereDate('movimiento_de_cajas.created_at', [$fi, $ff])->sum('importe');
            $this->cajaFinal = $this->cajaInicial + $this->ventas + $this->cobrosCtaCte + 
                               $this->otrosIngresos - $this->egresos;         
        }else{
            $this->cajaInicial = CajaInicial::whereDate('created_at',[$fi, $ff])
                ->where('user_id', $this->user)->sum('importe');
            $this->ventas = Factura::where('comercio_id', $this->comercioId)
                ->where('user_id', $this->user)
                ->whereDate('created_at', [$fi, $ff])
                ->where('estado', 'contado')->sum('importe');
            $this->cobrosCtaCte = Recibo::whereDate('created_at',[$fi, $ff])
                ->where('comercio_id', $this->comercioId)
                ->where('user_id', $this->user)->sum('importe');
            $this->otrosIngresos = MovimientoDeCaja::join('otro_ingresos as g', 'g.id', 'movimiento_de_cajas.ingreso_id')
                ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
                ->where('user_id', $this->user)
                ->where('movimiento_de_cajas.ingreso_id', '<>', null)
                ->whereDate('movimiento_de_cajas.created_at', [$fi, $ff])->sum('importe');
            $this->egresos = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
                ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
                ->where('user_id', $this->user)
                ->where('movimiento_de_cajas.egreso_id', '<>', null)
                ->whereDate('movimiento_de_cajas.created_at', [$fi, $ff])->sum('importe');
            $this->cajaFinal = $this->cajaInicial + $this->ventas + $this->cobrosCtaCte + 
                               $this->otrosIngresos - $this->egresos;
        }
    }
    
    public function cerrarArqueoGral()
    { 
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
    public function calcular_diferencia()
	{
//dd('kjkjkj');
       // $this->dif = '100';

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
    
    public function grabarCajaModal($info)
	{
		$data = json_decode($info);
		$this->selected_id = $data->id;
        $this->importe = $data->importe;

		$this->StoreOrUpdateCajaInicial();
    }
    public function StoreOrUpdateCajaInicial()
    {
        DB::begintransaction();
        try{
            //valida si se quiere modificar o grabar   
            if($this->selected_id > 0) {
                $record = CajaInicial::find($this->selected_id);
                $record->update([
                    'nro_caja' => $this->nro_caja,                        
                    'importe' => $this->importe               
                ]);
            }else {            
                CajaInicial::create([
                    'nro_caja'        => $this->nro_caja,
                    'importe'     => $this->importe,
                    'user_id' => auth()->user()->id
                ]);
            }              	              
            DB::commit();
            if($this->selected_id > 0) session()->flash('message', 'Registro Actualizado');       
            else session()->flash('message', 'Registro Agregado');  
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }     
        $this->resetInput(); 
        return; 
    }

    public function destroy($id) 
    {
        if ($id) {
            DB::begintransaction();
            try{
                $cajaInicial = CajaInicial::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla' => 'Caja Inicial',
                    'estado' => '0',
                    'comentario' => $this->comentario,
                    'user_delete_id' => auth()->user()->id,
                    'comercio_id' => $this->comercioId
                ]);
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
}
