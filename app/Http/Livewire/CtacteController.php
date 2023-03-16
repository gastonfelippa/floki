<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Banco;
use App\Models\CajaUsuario;
use App\Models\Cheque;
use App\Models\Cliente;
use App\Models\Ctacte;
use App\Models\DetMetodoPago;
use App\Models\Factura;
use App\Models\Recibo;
use App\Models\ReciboFactura;
use App\Models\Localidad;
use Carbon\Carbon;
use DB;

class CtacteController extends Component
{
    //public properties
	public $cliente = 'Elegir', $importeCobrado, $comentario, $suma = 0, $sumaFacturas = 0, $sumaRecibos = 0;           
    public $selected_id = null, $search = '', $ver_historial = 0, $verHistorial = 0;  
    public $comercioId, $action = 1, $nomCli, $numRecibo, $cliSelected = '', $clienteId = '';
    public $nomApeCli, $totalCli, $facturas_a_cobrar = array(), $entrega = 0;
    public $importeFactura, $importeEntrega, $saldo = 0, $entregas = 0, $nro_arqueo, $caja_abierta;
    public $f_de_pago = '1', $nro_comp_pago = '0', $importeCompPago, $mercadopago = '0', $terminarFactura;
    public $totalFactura,  $entregaFactura = 0, $saldoFactura, $bancos, $recibo_id = 0, $infoMediosDePago = '';
    public $banco, $numero, $fecha_de_emision, $fecha_de_pago, $importe, $cuitTitular, $estadoCheque = 'en_cartera';

    public function render()
    {
         //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');

        //vemos si tenemos una caja habilitada para nuestro user_id 
        //en cuyo caso podremos cobrar, sino solo podremos ver e imprimir resúmenes de cta
        $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', auth()->user()->id)
            ->where('caja_usuarios.estado', '1')->get();
        $this->caja_abierta = $caja_abierta->count();  
        if($caja_abierta->count() > 0){
            $this->nro_arqueo = $caja_abierta[0]->id;
            // $this->fecha_inicio = $caja_abierta[0]->created_at;  
        }

        $infoEntrega = '';
        $this->importeEntrega = 0;
        $clientes = Cliente::select()->where('comercio_id', $this->comercioId)->orderBy('apellido', 'asc')->get();
        $this->bancos = Banco::all()->where('comercio_id', $this->comercioId);

        if(strlen($this->search) == 0){
            $this->resetInput();    
        }
        if(strlen($this->search) > 0 || $this->clienteId != ''){          
            if($this->verHistorial == 1){   
                $info = Ctacte::join('clientes as c', 'c.id', 'cta_cte.cliente_id')
                    ->where('cta_cte.cliente_id', $this->clienteId)
                    ->select('cta_cte.factura_id', 'cta_cte.recibo_id', 
                            'c.nombre', 'c.apellido', DB::RAW("'' as fecha") , DB::RAW("'' as numero") , DB::RAW("'' as importe"), DB::RAW("'' as importe_factura"))
                    ->orderBy('cta_cte.created_at', 'desc')->get();
            }else{ //verHistorial = 0
                if($this->clienteId == ''){
                    $info = Ctacte::join('clientes as c', 'c.id', 'cta_cte.cliente_id')
                        ->join('localidades as loc', 'loc.id', 'c.localidad_id')
                        ->where('c.nombre', 'like', '%' .  $this->search . '%')
                        ->where('c.comercio_id', $this->comercioId)
                        ->orWhere('c.apellido', 'like', '%' .  $this->search . '%')
                        ->where('c.comercio_id', $this->comercioId)
                        ->select('cta_cte.cliente_id', 'c.nombre', 'c.apellido', 'c.calle', 'c.numero', 'loc.descripcion as localidad', DB::RAW("'' as numero_fac") , DB::RAW("'' as importe_factura"))
                        ->groupBy('cta_cte.cliente_id', 'c.nombre', 'c.apellido', 'c.calle', 'c.numero', 'loc.descripcion')
                        ->orderBy('c.apellido')->orderBy('c.nombre')->get(); 
                }else{
                    $info = Ctacte::join('clientes as c', 'c.id', 'cta_cte.cliente_id')
                        ->join('facturas as f', 'f.id', 'cta_cte.factura_id')
                        ->where('cta_cte.cliente_id', $this->clienteId)
                        ->where('f.estado', 'ctacte')
                        ->where('f.estado_pago', '0')
                        ->orWhere('cta_cte.cliente_id', $this->clienteId)
                        ->where('f.estado', 'ctacte')
                        ->where('f.estado_pago', '2')
                        ->select('cta_cte.factura_id', 'cta_cte.recibo_id', 'cta_cte.cliente_id', 
                                 'c.nombre', 'c.apellido', DB::RAW("'' as fecha"), DB::RAW("'' as numero_fac") , DB::RAW("'' as importe"), 
                                 DB::RAW("'' as importe_factura"), DB::RAW("'' as resto"))
                        ->orderBy('cta_cte.created_at')->get();                     
                }
            } 
        }else{
            $this->verHistorial = 0;
            $this->clienteId = '';
            $info = Ctacte::join('clientes as c', 'c.id', 'cta_cte.cliente_id')
                ->join('localidades as loc', 'loc.id', 'c.localidad_id')
                ->where('c.comercio_id', $this->comercioId)
                ->select('cta_cte.cliente_id', 'c.nombre', 'c.apellido', 'c.calle', 'c.numero', 'loc.descripcion as localidad', DB::RAW("'' as importe"), DB::RAW("'' as importe_factura"))
                ->groupBy('cta_cte.cliente_id', 'c.nombre', 'c.apellido', 'c.calle', 'c.numero', 'loc.descripcion')
                ->orderBy('c.apellido')->orderBy('c.nombre')->get();
        }

        if(strlen($this->search) == 0){
                foreach($info as $i) {
                    $this->sumaFacturas=0;
                    $this->sumaRecibos=0;
                     //verifico si el registro es una factura o es un recibo
                    $registroCtaCte = Ctacte::where('cta_cte.cliente_id', $i->cliente_id)->get();
                    foreach($registroCtaCte as $r) {   
                        if($r->factura_id != null) {    //si es factura las voy sumando
                            $importe = Factura::where('id', $r->factura_id)
                            ->where('estado', 'ctacte')
                            ->where('estado_pago', '0')
                            ->orWhere('id', $r->factura_id)
                            ->where('estado', 'ctacte')
                            ->where('estado_pago', '2')
                            ->select('numero', 'importe')->get();
                            foreach($importe as $imp){
                                $this->sumaFacturas += $imp->importe; //calculo el total de las facturas de cada cliente
                                $i->numero_fac = $imp->numero;
                            }
                        }else {                         //busco todos los recibos
                            $importe = Recibo::where('id', $r->recibo_id)
                            ->where('entrega', 1)
                            ->select('id','numero', 'importe')->get();
                            foreach($importe as $imp){
                                $verEstadoPagoFactura = ReciboFactura::join('facturas as f','f.id','recibo_facturas.factura_id')
                                    ->where('recibo_facturas.recibo_id',$imp->id)
                                    ->where('f.estado_pago','2')->get();
                                if($verEstadoPagoFactura->count() > 0){
                                    $this->sumaRecibos += $imp->importe; //calculo el total de recibos de cada cliente
                                    $i->numero_fac = $imp->numero; 
                                }
                            }
                        }
                    }
                    if($this->sumaFacturas == 0) $this->sumaRecibos = 0;  //si no debe nada, no muestro las entregas
                    //calculo el total para cada cliente
                    $i->importe = $this->sumaFacturas - $this->sumaRecibos;
                    $this->totalCli = $i->importe;
                    //pinto el importe de diferente color
                    if($i->importe < 0) $i->importe_factura = 0;
                    else $i->importe_factura = 1;
                }
        }

        if(strlen($this->search) > 0 || $this->clienteId != ''){
            if($this->verHistorial == 0){   
                if($this->clienteId == ''){
                    foreach($info as $i) {
                        $this->sumaFacturas=0;
                        $this->sumaRecibos=0;
                         //verifico si el registro es una factura o es un recibo
                        $registroCtaCte = Ctacte::where('cta_cte.cliente_id', $i->cliente_id)->get();
                        foreach($registroCtaCte as $r) {   
                            if($r->factura_id != null) {    //si es factura las voy sumando
                                $importe = Factura::where('id', $r->factura_id)
                                ->where('estado', 'ctacte')
                                ->where('estado_pago', '0')
                                ->orWhere('id', $r->factura_id)
                                ->where('estado', 'ctacte')
                                ->where('estado_pago', '2')
                                ->select('numero', 'importe')->get();
                                foreach($importe as $imp){
                                    $this->sumaFacturas += $imp->importe; //calculo el total de las facturas de cada cliente
                                    $i->numero_fac = $imp->numero;
                                }
                            }else {                         //busco todos los recibos
                                $importe = Recibo::where('id', $r->recibo_id)
                                ->where('entrega', 1)
                                ->select('id','numero', 'importe')->get();
                                foreach($importe as $imp){
                                    $verEstadoPagoFactura = ReciboFactura::join('facturas as f','f.id','recibo_facturas.factura_id')
                                        ->where('recibo_facturas.recibo_id',$imp->id)
                                        ->where('f.estado_pago','2')->get();
                                    if($verEstadoPagoFactura->count() > 0){
                                        $this->sumaRecibos += $imp->importe; //calculo el total de recibos de cada cliente
                                        $i->numero_fac = $imp->numero; 
                                    }
                                }
                            }
                        }
                        if($this->sumaFacturas == 0) $this->sumaRecibos = 0;  //si no debe nada, no muestro las entregas
                        //calculo el total para cada cliente
                        $i->importe = $this->sumaFacturas - $this->sumaRecibos;
                        $this->totalCli = $i->importe;
                        //pinto el importe de diferente color
                        if($i->importe < 0) $i->importe_factura = 0;
                        else $i->importe_factura = 1;
                    }      
                }else{  //si verHistorial = 0 y clienteId != ''
                    $this->sumaFacturas=0;
                    $this->sumaRecibos=0;
                    if($info->count() > 0){        //si debe algo... 
                        foreach($info as $i) {     //busco todas las facturas                            
                            $importe = Ctacte::join('facturas as f', 'f.id', 'cta_cte.factura_id') 
                                ->where('f.id', $i->factura_id)
                                ->where('f.estado', 'ctacte')
                                ->where('f.estado_pago', '0')
                                ->orWhere('f.id', $i->factura_id)
                                ->where('f.estado', 'ctacte')
                                ->where('f.estado_pago', '2')
                                ->select('f.estado_pago', 'f.importe as importe', 'f.numero', 'f.created_at')->get();
                            $this->sumaFacturas += $importe[0]->importe; //calculo el total de las facturas de cada cliente
                            if($importe[0]->estado_pago == 0) $i->importe_factura = 1; //aviso de factura para pintar rojo   
                            else $i->importe_factura = 2;  //aviso de factura para pintar rojo/negrita            
                            
                            $i->numero_fac = $importe[0]->numero;
                            $i->fecha      = $importe[0]->created_at;                        
                            $i->importe    = $importe[0]->importe;
                            
                            //busco las entregas y calculo el resto de las facturas que correspondan                           
                            if($importe[0]->estado_pago == 2){
                                $this->entregas = 0;
                                $pagos = ReciboFactura::join('recibos as r', 'r.id', 'recibo_facturas.recibo_id')
                                    ->where('recibo_facturas.factura_id', $i->factura_id)
                                    ->select('r.importe')->get();
                                foreach($pagos as $p){
                                    $this->entregas += $p->importe;
                                }
                                $this->importeEntrega += $this->entregas;
                                $i->resto = $i->importe - $this->entregas;
                            }
                        } 
                        $this->totalCli = $this->sumaFacturas;
                        //calculo el saldo del cliente seleccionado
                        $this->saldo = $this->totalCli - $this->importeEntrega;
                    }else{  //si el saldo es cero, dejo todo en cero...
                        $this->totalCli = $this->sumaFacturas;
                        $this->importeEntrega = 0;
                    }
                }
            }else{  //si verHistorial = 1
                foreach($info as $i) {
                    if($i->factura_id != null) {    //busco todas las facturas
                        $importe = Factura::where('id', $i->factura_id)
                        ->select('numero', 'importe', 'created_at')->get();
                        $i->numero_fac = $importe[0]->numero;
                        $i->fecha = $importe[0]->created_at;
                        $i->importe_factura = 1;        //aviso de factura para pintar rojo                   
                    }else {                         //busco todos los recibos
                        $importe = Recibo::where('id', $i->recibo_id)
                            ->select('numero', 'importe', 'created_at')->get();
                        $i->numero_fac      = $importe[0]->numero;
                        $i->fecha           = $importe[0]->created_at;
                        $i->importe_factura = 0;        //aviso de recibo para pintar verde
                    }
                    $i->importe = $importe[0]->importe;
                    if($i->importe_factura == 0) $this->suma += $i->importe;
                    else $this->suma -= $i->importe;
                }
            }                 
        }else{
            $this->suma=0;
            foreach($info as $i) {
                $this->sumaFacturas=0;
                $this->sumaRecibos=0;
                //verifico si el registro es una factura o es un recibo
                $registroCtaCte = Ctacte::where('cta_cte.cliente_id', $i->cliente_id)->get();
                foreach($registroCtaCte as $r) {   
                    if($r->factura_id != null) {    //si es factura las voy sumando
                        $importe = Factura::where('id', $r->factura_id)
                        ->where('estado', 'ctacte')
                        ->where('estado_pago', '0')
                        ->orWhere('id', $r->factura_id)
                        ->where('estado', 'ctacte')
                        ->where('estado_pago', '2')
                        ->select('importe')->get();
                        foreach($importe as $imp){
                            $this->sumaFacturas += $imp->importe; //calculo el total de las facturas de cada cliente
                        }
                    }else {                         //busco todos los recibos
                        $importe = Recibo::where('id', $r->recibo_id)
                        ->where('entrega', 1)
                        ->select('id','importe')->get();
                        foreach($importe as $imp){
                            $verEstadoPagoFactura = ReciboFactura::join('facturas as f','f.id','recibo_facturas.factura_id')
                            ->where('recibo_facturas.recibo_id',$imp->id)
                            ->where('f.estado_pago','2')->get();
                            if($verEstadoPagoFactura->count() > 0)
                            $this->sumaRecibos += $imp->importe; //calculo el total de recibos de cada cliente
                        }
                    }
                }
                if($this->sumaFacturas == 0) $this->sumaRecibos = 0;  //si no debe nada, no muestro las entregas
                // calculo el total para cada cliente
                $i->importe = $this->sumaFacturas - $this->sumaRecibos;
                //solo calculo el importe del total gral si se están mostrando todos los clientes
                $this->suma += $i->importe;
                //pinto el importe de diferente color
                if($i->importe < 0) $i->importe_factura = 0;
                else $i->importe_factura = 1;
            }

            //$this->recibo_id = 1;
            
            $this->infoMediosDePago = DetMetodoPago::where('recibo_id', $this->recibo_id)
                ->where('comercio_id', $this->comercioId)
                ->select('*', DB::RAW("'' as medio_pago"))->orderBy('id')->get(); 
            
            $this->entregaFactura = 0;
            if($this->infoMediosDePago->count()){
                foreach($this->infoMediosDePago as $i){
                    $this->entregaFactura += $i->importe;
                    switch ($i->medio_de_pago) {
                        case '1':
                            $i->medio_pago = 'Efectivo';
                            break;
                        case '2':
                            $i->medio_pago = 'Tarj. de Débito';
                            break;
                        case '3':
                            $i->medio_pago = 'Tarj. de Crédito';
                            break;
                            case '4':
                            $i->medio_pago = 'Transferencia Bancaria';
                            break;
                        case '5':
                            $i->medio_pago = 'Cheque N°';
                            break;
                        default:
                            $i->medio_pago = '';
                    }
                }
            } 
            $this->saldoFactura = $this->totalFactura - $this->entregaFactura;
        }
        return view('livewire.ctacte.component', [
            'info'             => $info,
            'infoEntrega'      => $infoEntrega,
            'clientes'         => $clientes
        ]);
    }
    protected $listeners = [  
        'preparar_cobro'    => 'preparar_cobro',   
        'StoreOrUpdate'     => 'StoreOrUpdate',   
        'mostrar_facturas'  => 'mostrar_facturas',
        'enviarDatosCheque' => 'enviarDatosCheque',
        'enviarDatosPago'   => 'enviarDatosPago',
        'agregarBanco'      => 'agregarBanco'
    ];
    public function doAction($action)
    { 
        $this->action = $action;
    }
    private function resetInput()
    {
        //$this->f_de_pago = '1';
        $this->importeCobrado = '';
        $this->cliente        = 'Elegir';
        $this->comentario     = '';
        $this->selected_id    = null;    
        $this->search         = '';
        $this->verHistorial   = 0;
        $this->nomApeCli      = '';
        $this->totalCli       = '';
        $this->clienteId      = '';
        $this->action         = 1;
        $this->estadoCheque   = 'en_cartera';
    }
    public function clearClientSelected()
    {
        $this->resetInput();
    }
    public function verHistorial($tipo)
    {
        $this->verHistorial = $tipo;
    }
    public function edit($id)
    {
        $record = Ctacte::findOrFail($id);
        $this->selected_id = $id;
        $this->cliente = $record->cliente_id;
        $this->f_de_pago = $record->created_at;
        $this->importeCobrado = $record->importe;
        $this->comentario = $record->comentario;
    } 
    public function verificar_saldo()    //busca si hay alguna factura que el estado_pago no sea =1
    {        
        $saldo = Factura::where('cliente_id', $this->clienteId)
            ->where('estado_pago', '<>', '1')
            ->where('estado', '<>', 'anulado')
            ->where('estado', '<>', 'pendiente')->get();
        if($saldo->count() == 0){
            $record = Cliente::find($this->clienteId);
            $record->update([
                'saldo' => '0'
            ]);
        }
        $this->resetInput();
        return;
    } 
    public function enviarDatosPago($tipo,$nro,$importe)
    {
        $this->f_de_pago = $tipo;
        $this->nro_comp_pago = $nro;
        $this->importeCompPago = $importe;
    }
    public function agregarBanco($data)
    {
        $info = json_decode($data);
        DB::begintransaction();                 
        try{
            $add_item = Banco::create([         
                'descripcion' => mb_strtoupper($info->banco),
                'sucursal'    => ucwords($info->sucursal),
                'comercio_id' => $this->comercioId
            ]);
            $this->bancos = $add_item->id;
            DB::commit();
            $this->emit('bancoCreado');  
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }     
        $this->f_de_pago = '1'; 
        return;  
    }
    public function mostrar_facturas($id)
    {
        if($this->clienteId == ''){
            if($id != ''){
                $this->clienteId = $id;
                $cli = Cliente::where('id', $id)->select('nombre','apellido')->first();
                $this->nomApeCli = $cli->apellido . ' ' . $cli->nombre;
            }
        }
    }
    public function enviarDatosCheque($data)
    {
        $info = json_decode($data);
        $this->banco            = $info->banco;
        $this->numero           = $info->numero;
        $this->fecha_de_emision = Carbon::parse($info->fechaDeEmision)->format('Y,m,d') . ' 00:00:00';
        $this->fecha_de_pago    = Carbon::parse($info->fechaDePago)->format('Y,m,d') . ' 00:00:00';
        $this->importe          = $info->importe;
        $this->cuitTitular      = $info->cuitTitular;
        $this->f_de_pago        = '5';
    }
    public function StoreOrUpdate($formaDePago, $nroCompPago, $importe, $terminarFactura)
    {
        $this->importeCobrado = $importe;
        if ($this->f_de_pago == '5') {
            $this->importeCobrado = $this->importe;
            $this->estadoCheque = 'en_caja';
        }
        if($this->entrega == 1){
            if($this->importeCobrado == $this->totalFactura){
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El importe a registrar es igual al importe total de la factura... en esta vista solo se registran entregas');
                return;
            }elseif($this->importeCobrado > $this->totalFactura){
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El importe a registrar no puede ser mayor al importe total de la factura...');
                return;
            }  
        }else{
            if($this->importeCobrado < $this->totalFactura){
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El importe a del cheque es menor al importe a cobrar... en esta vista solo se registran cobros totales');
                return;
            }
        }
        $existe = Recibo::select('*')->where('comercio_id', $this->comercioId)->get();  
        if(!$existe->count()) $this->numRecibo = 1; //si es el primer recibo, le asigno el nro: 1
        else{ 
            $encabezado = Recibo::select('numero')
                ->where('comercio_id', $this->comercioId)
                ->orderBy('numero', 'desc')->get();                             
            $this->numRecibo = $encabezado[0]->numero + 1;
        }
        $this->f_de_pago = $formaDePago;
        $this->nro_comp_pago = $nroCompPago;
        $this->importeCompPago = $importe;
        
        DB::begintransaction();                        
        try{
            if ($this->f_de_pago == 5) {
                $this->nro_comp_pago = $this->numero;            
                $cheque = Cheque::create([         
                    'cliente_id'       => $this->clienteId,
                    'banco_id'         => $this->banco,
                    'numero'           => $this->numero,
                    'fecha_de_emision' => $this->fecha_de_emision,
                    'fecha_de_pago'    => $this->fecha_de_pago,
                    'importe'          => $this->importe,
                    'cuit_titular'     => $this->cuitTitular,
                    'estado'           => $this->estadoCheque,
                    'comercio_id'      => $this->comercioId
                ]);
            } 
            $recibo =  Recibo::create([
                'numero'        => $this->numRecibo,            
                'importe'       => $this->importeCobrado,
                'entrega'       => $this->entrega,
                'cliente_id'    => $this->clienteId, 
                'user_id'       => auth()->user()->id,         
                'comercio_id'   => $this->comercioId,
                'arqueo_id'     => $this->nro_arqueo          
            ]);
            $this->recibo_id = $recibo->id;
            $record = DetMetodoPago::create([ 
                'recibo_id'     => $this->recibo_id,
                'medio_de_pago' => $this->f_de_pago,
                'num_comp_pago' => $this->nro_comp_pago, 
                'importe'       => $this->importeCompPago,
                'arqueo_id'     => $this->nro_arqueo,
                'comercio_id'   => $this->comercioId
            ]);
            Ctacte::create([
                'cliente_id' => $this->clienteId,  
                'recibo_id' => $recibo->id            
            ]);
            foreach($this->facturas_a_cobrar as $i){
                if($this->entrega == 0){
                    $record = Factura::find($i); 
                    $record->update([
                        'estado_pago' => '1'
                    ]);
                }else{
                    $record = Factura::find($i); 
                    $record->update([
                        'estado_pago' => '2'
                    ]);
                }
                ReciboFactura::create([
                    'recibo_id'  => $recibo->id,  
                    'factura_id' => $i       
                ]);      
            }
            if($this->terminarFactura == 1){    //si se cancela la factura
                $this->recibo_id = 0;
                $this->emit('facturaCobrada');
            }else $this->emit('cobroRegistrado'); 
            
            DB::commit();
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }  
        $this->resetInput();
    }
    public function preparar_cobro($data, $total, $entrega, $cantidad) //Id,Importe,Total/Entrega,Cantidad de facturas
    {
        //si es una entrega, $data y $total son arrays
        //si es un pago total o saldo total, $data es array y $total no lo es
        $this->entrega = $entrega;
        $this->facturas_a_cobrar = json_decode($data);  //array con Id de las facturas que se cobran
        $importeFactura = json_decode($total);  //array con el Importe Total de las facturas que se cobran

        if($this->entrega == 0){    //si es un pago total o saldo
            $this->totalFactura = $importeFactura; //lo uso para comparar con $this->importeCobrado
            $this->saldoFactura = $importeFactura;
            $this->terminarFactura = 1;
        }else{                //si es una entrega
            $this->totalFactura = $importeFactura[0];
            $this->saldoFactura = $importeFactura[0];
            $this->entregaFactura = 0;
            $this->terminarFactura = 0;
        }        

        if($cantidad > 1){
            $contadorFacturasConEntrega =0;
            foreach($this->facturas_a_cobrar as $i){
                $ver = Factura::where('id', $i)
                    ->select('estado_pago')->first();
                if($ver->estado_pago == 2){
                    $contadorFacturasConEntrega ++;
                }
            }
            if($contadorFacturasConEntrega > 0){
                $this->emit('facturaConEntrega');
                return;
            }
        }

        $cli = Cliente::where('id', $this->clienteId)->get();
        $this->nomCli = $cli[0]->apellido . ' ' . $cli[0]->nombre;

        $ver = Factura::where('id', $this->facturas_a_cobrar[0])
            ->select('importe', 'estado_pago')->get();

        if($ver[0]->estado_pago == 2){
            $totalEntregas = 0;
            $pagos = ReciboFactura::join('recibos as r', 'r.id', 'recibo_facturas.recibo_id')
                ->where('recibo_facturas.factura_id', $this->facturas_a_cobrar[0])
                ->select('r.importe')->get();
            foreach($pagos as $p){
                $totalEntregas += $p->importe;
            }
            $this->totalFactura = $ver[0]->importe;
            $this->entregaFactura = $totalEntregas;
            $this->saldoFactura = $ver[0]->importe - $totalEntregas;
        }
        $this->f_de_pago = '1';        
        $this->doAction(2);
    }
}
