<?php

namespace App\Http\Livewire;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

use Livewire\Component;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use App\Models\CajaInicial;
use App\Models\CajaUsuario;
use App\Models\Cheque;
use App\Models\Compra;
use App\Models\DetMetodoPago;
use App\Models\Factura;
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
    public $comercioId, $arqueoGralId, $estadoArqueoGral, $nro_arqueo;
    public $cajaInicial, $cobrosCtaCte, $otrosIngresos, $totalIngresos, $cajaFinal;
    public $ventas, $ventasCtdo, $ventasTDeb, $ventasTCred, $ventasTransfer, $ventasCheque;
    public $egresos;
    public $importe, $comentario='', $caja_abierta = 1, $factPendiente, $compraPendiente;
    public $user = 0, $usuario_habilitado = 1, $repartidor = true;
    // public $cajaEfectivo, $cajaCheque;
    // public $ventasEfectivo, $ventasTDebito, $ventasTCredito, $ventasTransferencia, $ventasCheques;
    // public $reciboEfectivo, $reciboTDebito, $reciboTCredito, $reciboTransferencia, $reciboCheque;

    public $infoCI, $cajaCheques, $totalPorMedioDePago = 0;
    public $ventaEfvo = 0, $ventaTDebito = 0, $ventaTCredito = 0, $ventaTransferencia = 0, $ventaCheque = 0;
    public $cobroEfvo = 0, $cobroTDebito = 0, $cobroTCredito = 0, $cobroTransferencia = 0, $cobroCheque = 0;
    public $gastoEfvo = 0, $gastoTDebito = 0, $gastoTCredito = 0, $gastoTransferencia = 0, $gastoCheque = 0;
    public $totalEfvo = 0, $totalTDebito = 0, $totalTCredito = 0, $totalTransferencia = 0, $totalCheque = 0;

    public function render()
    {
            //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        $this->arqueoGralId = session('idArqueoGral');
        $this->estadoArqueoGral = session('estadoArqueoGral');
        session(['facturaPendiente' => null]);  

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
        }else{
            $this->nro_arqueo = null;
            $this->resetInput();
        }  
        $this->Arqueo(); 

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

        //verifica compras abiertas 
        $compraPendiente = Compra::where('estado', 'abierta')
            ->where('user_id', $this->user)->get();
        if($compraPendiente->count()){     //si hay facturas pendientes, cambio el valor de factPendiente a 1
            if($compraPendiente[0]->estado == 'abierta') $this->compraPendiente = 1;
        }else $this->compraPendiente = 0;  

    ///////////////////////////////////////
        $cajaI = new Collection();

        $infoCI = CajaInicial::join('caja_usuarios as cu', 'cu.id', 'caja_inicials.caja_user_id')
            ->where('cu.id', $this->nro_arqueo)
            ->select('caja_inicials.tipo', 'caja_inicials.created_at', 'caja_inicials.importe',
              DB::RAW("0 as importeEfvo"), DB::RAW("0 as importeCheque"))->get();







        $this->infoCI = 0;   ///falta calcular
        $this->cajaCheques = 0;   ///falta calcular










        if($infoCI){
            foreach ($infoCI as $i) {
                if($i->tipo == '1') $this->infoCI = $this->infoCI + $i->importe;
                else $this->cajaCheques = $this->cajaCheques + $i->importe;
            }
        }
        $cajaTotal = $this->infoCI + $this->cajaCheques;

        $cajaI->offsetSet('texto','Caja Inicial');
        $cajaI->offsetSet('efectivo',number_format($this->infoCI, 2, ',', '.'));
        $cajaI->offsetSet('tdebito',number_format(0, 2, ',', '.'));
        $cajaI->offsetSet('tcredito',number_format(0, 2, ',', '.'));
        $cajaI->offsetSet('transferencia',number_format(0, 2, ',', '.'));
        $cajaI->offsetSet('cheque',number_format($this->cajaCheques, 2, ',', '.')); 
        $cajaI->offsetSet('total',number_format($cajaTotal, 2, ',', '.'));
    ///////////////////////////////

        $this->ventaEfvo = 0;
        $this->ventaTDebito = 0;
        $this->ventaTCredito = 0;
        $this->ventaTransferencia = 0;
        $this->ventaCheque = 0;

        $ingresosPorVentas = new Collection();
  
        $infoIngresosPorVentas = DetMetodoPago::join('facturas as f', 'f.id', 'det_metodo_pagos.factura_id')
            ->where('f.comercio_id', $this->comercioId)
            ->where('f.arqueo_id', $this->nro_arqueo)
            ->where('f.estado', 'contado')
            ->whereNotNull('det_metodo_pagos.factura_id')
            ->select('det_metodo_pagos.medio_de_pago', 'det_metodo_pagos.importe')->get();           
        foreach ($infoIngresosPorVentas as $i) {
            switch ($i->medio_de_pago) {
                case '1':
                    $this->ventaEfvo += $i->importe;
                    break;
                case '2':
                    $this->ventaTDebito += $i->importe;
                    break;
                case '3':
                    $this->ventaTCredito += $i->importe;
                    break;
                case '4':
                    $this->ventaTransferencia += $i->importe;
                    break;
                case '5':
                    $this->ventaCheque += $i->importe;
                    break;
                default:
                    break;
            }
        }
        $ventaTotal = $this->ventaEfvo + $this->ventaTDebito + $this->ventaTCredito +
                      $this->ventaTransferencia + $this->ventaCheque;

        $ingresosPorVentas->offsetSet('texto','Ventas');
        $ingresosPorVentas->offsetSet('efectivo',number_format($this->ventaEfvo, 2, ',', '.'));
        $ingresosPorVentas->offsetSet('tdebito',number_format($this->ventaTDebito, 2, ',', '.'));
        $ingresosPorVentas->offsetSet('tcredito',number_format($this->ventaTCredito, 2, ',', '.'));
        $ingresosPorVentas->offsetSet('transferencia',number_format($this->ventaTransferencia, 2, ',', '.'));
        $ingresosPorVentas->offsetSet('cheque',number_format($this->ventaCheque, 2, ',', '.'));
        $ingresosPorVentas->offsetSet('total',number_format($ventaTotal, 2, ',', '.'));
    /////////////////////// 

        $this->cobroEfvo = 0;   
        $this->cobroTDebito = 0;   
        $this->cobroTCredito = 0;   
        $this->cobroTransferencia = 0;   
        $this->cobroCheque = 0;  

        $cobros = new Collection();
     
        $infoCobros = DetMetodoPago::join('recibos as r', 'r.id', 'det_metodo_pagos.recibo_id')
            ->where('r.comercio_id', $this->comercioId)
            ->where('r.arqueo_id', $this->nro_arqueo)
            ->whereNotNull('det_metodo_pagos.recibo_id')
            ->select('det_metodo_pagos.medio_de_pago', 'det_metodo_pagos.importe')->get();
        foreach ($infoCobros as $i) {
            switch ($i->medio_de_pago) {
                case '1':
                    $this->cobroEfvo += $i->importe;
                    break;
                case '2':
                    $this->cobroTDebito += $i->importe;
                    break;
                case '3':
                    $this->cobroTCredito += $i->importe;
                    break;
                case '4':
                    $this->cobroTransferencia += $i->importe;
                    break;
                case '5':
                    $this->cobroCheque += $i->importe;
                    break;
                default:
                    break;
            }
        }
        $cobroTotal = $this->cobroEfvo + $this->cobroTDebito + $this->cobroTCredito +
                      $this->cobroTransferencia + $this->cobroCheque;

        $cobros->offsetSet('texto','Cobros Cta. Cte.');
        $cobros->offsetSet('efectivo',number_format($this->cobroEfvo, 2, ',', '.'));
        $cobros->offsetSet('tdebito',number_format($this->cobroTDebito, 2, ',', '.'));
        $cobros->offsetSet('tcredito',number_format($this->cobroTCredito, 2, ',', '.'));
        $cobros->offsetSet('transferencia',number_format($this->cobroTransferencia, 2, ',', '.'));
        $cobros->offsetSet('cheque',number_format($this->cobroCheque, 2, ',', '.'));
        $cobros->offsetSet('total',number_format($cobroTotal, 2, ',', '.'));
    //////////////////////////////////////////
        $gastos = new Collection();
        $gastoTotal = $this->gastoEfvo + $this->gastoTDebito + $this->gastoTCredito +
                      $this->gastoTransferencia + $this->gastoCheque;

        $gastos->offsetSet('texto','Egresos');
        $gastos->offsetSet('efectivo','-('.number_format($this->gastoEfvo, 2, ',', '.').')');
        $gastos->offsetSet('tdebito','-('.number_format($this->gastoTDebito, 2, ',', '.').')');
        $gastos->offsetSet('tcredito','-('.number_format($this->gastoTCredito, 2, ',', '.').')');
        $gastos->offsetSet('transferencia','-('.number_format($this->gastoTransferencia, 2, ',', '.').')');
        $gastos->offsetSet('cheque','-('.number_format($this->gastoCheque, 2, ',', '.').')');
        $gastos->offsetSet('total','-('.number_format($gastoTotal, 2, ',', '.').')');
    ////////////////////////////
        $totales = new Collection();
        $this->totalEfvo = $this->infoCI + $this->ventaEfvo + $this->cobroEfvo - $this->gastoEfvo;
        $this->totalTDebito = 0 + $this->ventaTDebito + $this->cobroTDebito - $this->gastoTDebito;
        $this->totalTCredito = 0 + $this->ventaTCredito + $this->cobroTCredito - $this->gastoTCredito;
        $this->totalTransferencia = 0 + $this->ventaTransferencia + $this->cobroTransferencia - $this->gastoTransferencia;
        $this->totalCheque = $this->cajaCheques + $this->ventaCheque + $this->cobroCheque - $this->gastoCheque;
        $this->totalPorMedioDePago = $this->totalEfvo + $this->totalTDebito + $this->totalTCredito +
                      $this->totalTransferencia + $this->totalCheque;

        $totales->offsetSet('texto','TOTALES EN CAJA');
        $totales->offsetSet('efectivo',number_format($this->totalEfvo, 2, ',', '.'));
        $totales->offsetSet('tdebito',number_format($this->totalTDebito, 2, ',', '.'));
        $totales->offsetSet('tcredito',number_format($this->totalTCredito, 2, ',', '.'));
        $totales->offsetSet('transferencia',number_format($this->totalTransferencia, 2, ',', '.'));
        $totales->offsetSet('cheque',number_format($this->totalCheque, 2, ',', '.'));
        $totales->offsetSet('total',number_format($this->totalPorMedioDePago, 2, ',', '.'));
    //////////////////////////////    

        $infoCajaInicial = CajaInicial::join('caja_usuarios as cu', 'cu.id', 'caja_inicials.caja_user_id')
            ->where('cu.id', $this->nro_arqueo)
            ->where('cu.estado', '1')
            ->select('caja_inicials.*', DB::RAW("'' as tipoIngreso"))
            ->orderBy('caja_inicials.created_at')->get();
        if ($infoCajaInicial->count()) {
            foreach ($infoCajaInicial as $i) { 
                $i->tipoIngreso = 'Efectivo';               
                if ($i->tipo == '2') {
                    $infoNumCheque = Cheque::find($i->cheque_id);
                    if ($infoNumCheque) {
                        $i->tipoIngreso = 'Cheque N° ' . $infoNumCheque->numero;
                    }
                }
            }
        }

        $listaVentas = Factura::where('arqueo_id', $this->nro_arqueo)
            ->where('estado', 'contado')
            ->where('estado_pago', '1')
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
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->select('g.descripcion', 'movimiento_de_cajas.importe')->get(); 

        return view('livewire.cortes.component',[
                'users'             => $users,
                'infoCajaInicial'   => $infoCajaInicial,
                'listaVentas'       => $listaVentas,
                'listaCobros'       => $listaCobros,
                'listaIngresos'     => $listaIngresos,
                'listaEgresos'      => $listaEgresos,
                'cajaI'             => $cajaI,
                'ingresosPorVentas' => $ingresosPorVentas,
                'cobros'            => $cobros,
                'gastos'            => $gastos,
                'totales'           => $totales
            ]);
    }
    public function resetInput()
    {
        $this->user               = 0;
        $this->cajaInicial        = 0;
        $this->ventas             = 0;
        $this->cobrosCtaCte       = 0;
        $this->otrosIngresos      = 0;
        $this->egresos            = 0;
        $this->cajaFinal          = 0;
        $this->ventaEfvo          = 0;
        $this->ventaTDebito       = 0;
        $this->ventaTCredito      = 0;
        $this->ventaTransferencia = 0;
        $this->ventaCheque        = 0;
        $this->cobroEfvo          = 0;
        $this->cobroTDebito       = 0;
        $this->cobroTCredito      = 0;
        $this->cobroTransferencia = 0;
        $this->cobroCheque        = 0;
        $this->gastoEfvo          = 0;
        $this->gastoTDebito       = 0;
        $this->gastoTCredito      = 0;
        $this->gastoTransferencia = 0;
        $this->gastoCheque        = 0;
        $this->totalEfvo          = 0;
        $this->totalTDebito       = 0;
        $this->totalTCredito      = 0;
        $this->totalTransferencia = 0;
        $this->totalCheque        = 0;
        $this->totalPorMedioDePago = 0;
    

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
        $this->cajaInicial = CajaInicial::join('caja_usuarios as cu', 'cu.id', 'caja_inicials.caja_user_id')
        ->where('cu.id', $this->nro_arqueo)->sum('importe');
        
        
        // CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
        //     ->join('caja_inicials as ci', 'ci.caja_user_id', 'caja_usuarios.id')
        //     ->where('caja_usuarios.caja_usuario_id', $this->user)
        //     ->where('caja_usuarios.estado', '1')->sum('ci.importe');
        $this->ventas = Factura::join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
            ->where('cu.estado', '1')
            ->where('facturas.arqueo_id', $this->nro_arqueo)
            ->where('facturas.estado', 'contado')
            ->sum('facturas.importe');
        $this->ventasCtdo = Factura::join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
            ->join('det_metodo_pagos as det', 'det.factura_id', 'facturas.id')
            ->where('cu.estado', '1')
            ->where('facturas.arqueo_id', $this->nro_arqueo)
            ->where('facturas.estado', 'contado')
            ->where('det.medio_de_pago', '1')
            ->sum('det.importe');
        $this->ventasTDeb = Factura::join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
            ->join('det_metodo_pagos as det', 'det.factura_id', 'facturas.id')
            ->where('cu.estado', '1')
            ->where('facturas.arqueo_id', $this->nro_arqueo)
            ->where('facturas.estado', 'contado')
            ->where('det.medio_de_pago', '2')
            ->sum('det.importe');
        $this->ventasTCred = Factura::join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
            ->join('det_metodo_pagos as det', 'det.factura_id', 'facturas.id')
            ->where('cu.estado', '1')
            ->where('facturas.arqueo_id', $this->nro_arqueo)
            ->where('facturas.estado', 'contado')
            ->where('det.medio_de_pago', '3')
            ->sum('det.importe');
        $this->ventasTransfer = Factura::join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
            ->join('det_metodo_pagos as det', 'det.factura_id', 'facturas.id')
            ->where('cu.estado', '1')
            ->where('facturas.arqueo_id', $this->nro_arqueo)
            ->where('facturas.estado', 'contado')
            ->where('det.medio_de_pago', '4')
            ->sum('facturas.importe');
        $this->ventasCheque = Factura::join('caja_usuarios as cu', 'cu.id', 'facturas.arqueo_id')
            ->join('det_metodo_pagos as det', 'det.factura_id', 'facturas.id')
            ->where('cu.estado', '1')
            ->where('facturas.arqueo_id', $this->nro_arqueo)
            ->where('facturas.estado', 'contado')
            ->where('det.medio_de_pago', '5')
            ->sum('facturas.importe');
        $this->cobrosCtaCte = Recibo::join('det_metodo_pagos as det', 'det.recibo_id', 'recibos.id')
            ->where('recibos.arqueo_id', $this->nro_arqueo)->sum('det.importe');
        $this->otrosIngresos = MovimientoDeCaja::join('otro_ingresos as g', 'g.id', 'movimiento_de_cajas.ingreso_id')
            ->join('caja_usuarios as cu', 'cu.id', 'movimiento_de_cajas.arqueo_id')
            ->where('cu.estado', '1')
            ->where('movimiento_de_cajas.arqueo_id', $this->nro_arqueo) 
            ->where('movimiento_de_cajas.ingreso_id', '<>', null)->sum('importe');
        $this->egresos = MovimientoDeCaja::join('proveedores as g', 'g.id', 'movimiento_de_cajas.egreso_id')
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
            $this->egresos = MovimientoDeCaja::join('proveedores as g', 'g.id', 'movimiento_de_cajas.egreso_id')
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
            $this->egresos = MovimientoDeCaja::join('proveedores as g', 'g.id', 'movimiento_de_cajas.egreso_id')
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
