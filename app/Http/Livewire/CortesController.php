<?php

namespace App\Http\Livewire;
use Illuminate\Http\Request;

use Livewire\Component;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
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


class CortesController extends Component
{
    
    public $fecha, $fecha_inicio;
    public $comercioId, $arqueoGralId, $estadoArqueoGral, $nro_arqueo, $Arqueo;
    public $cajaInicial, $ventas, $cobrosCtaCte, $otrosIngresos, $totalIngresos, $egresos, $cajaFinal;
    public $importe, $comentario='', $caja_abierta = 1, $factPendiente;
    public $user = 0, $usuario_habilitado = 1, $repartidor = true;

    public function render()
    {
            //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        $this->arqueoGralId = session('idArqueoGral');
        $this->estadoArqueoGral = session('estadoArqueoGral');

        //primero verifico si el usuario logueado es el Administrador del Sistema, en tal caso
        //no hago ninguna validación y le permito hacer cualquier procedimiento
        $usuadrioAdmin = ModelHasRole::join('roles as r', 'r.id', 'model_has_roles.role_id')
            ->join('users as u', 'u.id', 'model_has_roles.model_id')
            ->where('r.alias', 'Administrador')
            ->where('r.comercio_id', $this->comercioId)->select('u.id')->get();
        if($usuadrioAdmin[0]->id <> auth()->user()->id){
            //si no es el Admin, verifico si el usuario logueado es quien inició el Arqueo Gral, en caso de existir...
            //si es ese usuario, habilito para que vea todas las cajas
            //sino, debo averiguar si hay una Caja abierta con su Id, 
            //y en ese caso solo le dejo ver la suya, pero no realizar el cierre de la misma,
            //de lo contrario muestro un mensaje y vuelvo al home
            $usuarioArqueo = CajaUsuario::where('user_id', auth()->user()->id)
                    ->where('arqueo_gral_id', $this->arqueoGralId)->get();
            if($usuarioArqueo->count()){
                $this->user = 0;                //usuario habilitado para ver todo
            }else{
                $this->usuario_habilitado = 0;  //el usuario solo verá su arqueo
                $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', auth()->user()->id)
                    ->where('caja_usuarios.estado', '1')->select('caja_usuarios.*')->get();           
                $this->caja_abierta = $caja_abierta->count();
                if($caja_abierta->count()){
                    $this->user = auth()->user()->id;
                    $this->nro_arqueo = $caja_abierta[0]->id;
                    $this->fecha_inicio = $caja_abierta[0]->created_at;
                }
            }
                    //busco los usuarios con Caja abierta
            $users = CajaUsuario::join('users as u', 'u.id', 'caja_usuarios.caja_usuario_id')
            ->join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
            ->where('c.comercio_id', $this->comercioId)
            ->where('caja_usuarios.estado', '1')
            ->where('caja_usuarios.user_id', auth()->user()->id)
            ->select('u.id', 'u.name')->get();
        }else{
                    //busco los usuarios con Caja abierta
            $users = CajaUsuario::join('users as u', 'u.id', 'caja_usuarios.caja_usuario_id')
            ->join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
            ->where('c.comercio_id', $this->comercioId)
            ->where('caja_usuarios.estado', '1')
            ->select('u.id', 'u.name')->get();
        }
        //muestro datos solo si hay un usuario seleccionado
        if($this->user <> 0){
            $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', $this->user)
                ->where('caja_usuarios.estado', '1')->select('caja_usuarios.*')->get();   
            if($caja_abierta->count()){
                $this->nro_arqueo = $caja_abierta[0]->id;
                $this->fecha_inicio = $caja_abierta[0]->created_at;
            }
            $this->Arqueo(); 
        }         
        //capturo el id del repartidor Salón para asociarla a alguna Caja que tenga facturas pendientes
        $salon = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('uc.comercio_id', $this->comercioId)
            ->where('users.name', '...')
            ->where('users.apellido', 'Salón')
            ->where('uc.comercio_id', $this->comercioId)
            ->select('users.id')->get();    
        $factPendiente = Factura::where('estado', 'pendiente')
            ->where('repartidor_id', $salon[0]->id)
            ->where('user_id', $this->user)
            ->orWhere('estado', 'abierta')
            ->where('user_id', $this->user)
            ->orWhere('repartidor_id', $this->user)
            ->where('estado', 'pendiente', 'repartidor_id')->get();
        if($factPendiente->count()){     //si hay facturas pendientes, cambio el valor de factPendiente a 1
            if($factPendiente[0]->estado == 'abierta') $this->factPendiente = 1;
            else $this->factPendiente = 2; //si hay facturas pendientes, cambio el valor de factPendiente a 2
                //luego veo si son facturas de repartidores o no
            if($factPendiente[0]->repartidor_id == $salon[0]->id) $this->repartidor = false;
        }else $this->factPendiente = 0;        

        // //busco los usuarios con Caja abierta
        // $users = CajaUsuario::join('users as u', 'u.id', 'caja_usuarios.caja_usuario_id')
        //     ->join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
        //     ->where('c.comercio_id', $this->comercioId)
        //     ->where('caja_usuarios.estado', '1')
        //     ->where('caja_usuarios.user_id', auth()->user()->id)
        //     ->select('u.id', 'u.name')->get();

        $infoCajaInicial = CajaInicial::join('caja_usuarios as cu', 'cu.id', 'caja_inicials.caja_user_id')
            ->where('cu.id', $this->nro_arqueo)
            ->select('caja_inicials.created_at', 'caja_inicials.importe')->get(); 

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

        return view('livewire.cortes.component',[
                'users'           => $users,
                'infoCajaInicial' => $infoCajaInicial,
                'listaVentas'     => $listaVentas,
                'listaCobros'     => $listaCobros,
                'listaIngresos'   => $listaIngresos,
                'listaEgresos'    => $listaEgresos
            ]);
    }
    public function resetInput()
    {
        $this->user = 0;
        $this->cajaInicial = 0;
        $this->ventas = 0;
        $this->cobrosCtaCte = 0;
        $this->otrosIngresos = 0;
        $this->egresos = 0;
        $this->cajaFinal = 0;
    }

    protected $listeners = [
        'infoToPrintCorte'     => 'PrintCorte',
        'cambiarFecha'         => 'cambiarFecha',
        'cerrarCaja'           => 'cerrarCaja'
    ];
    public function cambiarFecha($data) //no se usa por ahora, tal vez sirva para cuando se busquen
    {                                   //arqueos por fecha
        if($data != '') $this->fecha = date('w',strtotime($data));
    }
    public function Arqueo()
    {   
        $this->cajaInicial = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
            ->join('caja_inicials as ci', 'ci.caja_user_id', 'caja_usuarios.id')
            ->where('caja_usuarios.caja_usuario_id', $this->user)
            ->where('caja_usuarios.estado', '1')->sum('ci.importe');
        $this->ventas = Factura::join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
            ->where('cu.estado', '1')
            ->where('facturas.arqueo_id', $this->nro_arqueo)
            ->where('facturas.estado', 'contado')->sum('importe');
        $this->cobrosCtaCte = Recibo::where('recibos.arqueo_id', $this->nro_arqueo)->sum('importe');
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
     
        $this->cajaFinal = $this->cajaInicial + $this->ventas + $this->cobrosCtaCte + 
                           $this->otrosIngresos - $this->egresos;
        $this->totalIngresos = $this->ventas + $this->cobrosCtaCte + $this->otrosIngresos;
    }
    public function Consultar()  //no se usa por ahora, tal vez sirva para cuando se busquen 
    {                            //arqueos por fecha
        
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
    public function cerrarCaja($cajaFinalSegunUsuario)
    {
        $diferenciaDeCaja = $cajaFinalSegunUsuario - $this->cajaFinal;
        DB::begintransaction();
        try{
            $record = CajaUsuario::where('id', $this->nro_arqueo);
            $record->update([
                'estado'             => '0',
                'caja_final_sistema' => $this->cajaFinal,  //según sistema
                'diferencia'         => $diferenciaDeCaja  //si falta dinero, el monto será negativo
            ]);
            DB::commit();
            $this->emit('cajaCerrada');               
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
