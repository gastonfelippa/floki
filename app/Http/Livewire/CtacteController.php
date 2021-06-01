<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\CajaUsuario;
use App\Models\Cliente;
use App\Models\Ctacte;
use App\Models\Factura;
use App\Models\Recibo;
use App\Models\ReciboFactura;
use App\Models\Localidad;
use DB;

class CtacteController extends Component
{
    //public properties
	public $f_de_pago, $cliente = 'Elegir', $importeCobrado, $comentario, $suma = 0, $sumaFacturas = 0, $sumaRecibos = 0;           
    public $selected_id = null, $search = '', $ver_historial = 0, $verHistorial = 0;  
    public $comercioId, $action = 1, $nomCli, $numRecibo, $cliSelected = '', $clienteId = '';
    public $nomApeCli, $totalCli, $facturas_a_cobrar = array(), $entrega = 0;
    public $importeFactura, $importeEntrega, $saldo = 0, $entregas = 0, $saldoFactura, $nro_arqueo, $caja_abierta;

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
    
        if(strlen($this->search) == 0){
            $this->resetInput();    
        }
        if(strlen($this->search) > 0 || $this->clienteId != ''){          
            if($this->verHistorial == 1){   
                $info = Ctacte::join('clientes as c', 'c.id', 'cta_cte.cliente_id')
                    ->where('cta_cte.cliente_id', $this->clienteId)
                    ->select('cta_cte.factura_id', 'cta_cte.recibo_id', 'cta_cte.created_at as fecha', 
                            'c.nombre', 'c.apellido', DB::RAW("'' as numero") , DB::RAW("'' as importe"), DB::RAW("'' as importe_factura"))
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
                        ->select('cta_cte.factura_id', 'cta_cte.recibo_id', 'cta_cte.cliente_id', 'cta_cte.created_at as fecha', 
                                 'c.nombre', 'c.apellido', DB::RAW("'' as numero_fac") , DB::RAW("'' as importe"), 
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
                            ->select('f.estado_pago', 'f.importe as importe', 'f.numero')->get();
                            $this->sumaFacturas += $importe[0]->importe; //calculo el total de las facturas de cada cliente
                            if($importe[0]->estado_pago == 0) $i->importe_factura = 1; //aviso de factura para pintar rojo   
                            else $i->importe_factura = 2;  //aviso de factura para pintar rojo/negrita            
                            
                            $i->numero_fac = $importe[0]->numero;                        
                            $i->importe = $importe[0]->importe;
                            
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
                        ->select('numero', 'importe')->get();
                        $i->numero_fac = $importe[0]->numero;
                        $i->importe_factura = 1;        //aviso de factura para pintar rojo                   
                    }else {                         //busco todos los recibos
                        $importe = Recibo::where('id', $i->recibo_id)
                            ->select('numero', 'importe')->get();
                        $i->numero_fac = $importe[0]->numero;
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
        }
        return view('livewire.ctacte.component', [
            'info'        => $info,
            'infoEntrega' => $infoEntrega,
            'clientes'    => $clientes
        ]);
    }

    public function doAction($action)
    {
        $this->action = $action;
    }

    private function resetInput()
    {
        $this->f_de_pago = '';
        $this->importeCobrado = '';
        $this->cliente = 'Elegir';
        $this->comentario = '';
        $this->selected_id = null;    
        $this->search = '';
        $this->verHistorial = 0;
        $this->nomApeCli = '';
        $this->totalCli = '';
        $this->clienteId = '';
        $this->action = 1;
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

    public function StoreOrUpdate()
    { 
        if($this->entrega == 1){
            if($this->importeCobrado == $this->importeFactura[0]){
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El importe a registrar es igual al importe total de la factura... en esta vista solo se registran entregas');
                return;
            }elseif($this->importeCobrado > $this->importeFactura[0]){
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El importe a registrar no puede ser mayor al importe total de la factura...');
                return;
            }  
        }     
        $existe = Recibo::select('*')->where('comercio_id', $this->comercioId)->get();                        // ->where('estado','like','abierta')->get();
        //si es la primera factura, le asigno el nro: 1
        if($existe->count() == 0){
            $this->numRecibo = 1;
        }else{ 
            $encabezado = Recibo::select('numero')
            ->where('comercio_id', $this->comercioId)
            ->orderBy('numero', 'desc')->get();                             
            $this->numRecibo = $encabezado[0]->numero + 1;
        }
        DB::begintransaction();                         //iniciar transacción para grabar
        try{
            $recibo =  Recibo::create([
                'numero'      => $this->numRecibo,            
                'importe'     => $this->importeCobrado,
                'comentario'  => $this->comentario,
                'entrega'     => $this->entrega,
                'cliente_id'  => $this->clienteId, 
                'user_id'     => auth()->user()->id,         
                'comercio_id' => $this->comercioId,
                'arqueo_id'   => $this->nro_arqueo          
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
                    'recibo_id' => $recibo->id,  
                    'factura_id' => $i       
                ]);      
            }  
            session()->flash('msg-ok', 'El Cobro se registró correctamente...');
            DB::commit();          
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        return;
    }

    protected $listeners = [
        'deleteRow'        =>'destroy',           //no se utiliza
        'cobrar_factura'   => 'cobrar_factura',   //no se utiliza    
        'cobrar_saldo'     => 'cobrar_saldo',     //no se utiliza
        'entrega_a_cuenta' => 'entrega_a_cuenta', //no se utiliza    
        'cobrar'           => 'cobrar',      
        'mostrar_facturas' => 'mostrar_facturas',
        'importe_a_cobrar' => 'importe_a_cobrar'     //no se utiliza 
    ]; 

    public function importe_a_cobrar($importe)  //no se utiliza
    {
      //  $this->totalCli = $importe;
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

    public function cobrar_saldo($cliId, $importe)   //no se utiliza
    { 
        // $this->cliente = $cliId;
        // $this->importeCobrado = abs($importe);      
        // $recibo =  Recibo::create([
        //     'cliente_id' => $this->cliente,            
        //     'importe' => $this->importeCobrado,
        //     'comentario' => $this->comentario,
        //     'comercio_id' => $this->comercioId            
        // ]);
        // Ctacte::create([
        //     'cliente_id' => $this->cliente,  
        //     'recibo_id' => $recibo->id            
        // ]);
       
        // if($this->selected_id) session()->flash('message', 'Recibo Actualizado'); 
        // else session()->flash('message', 'Recibo Creado');
        // $this->resetInput();
    }

    public function cobrar($data,$total,$entrega,$cantidad)
    {

        $this->entrega = $entrega;
        $this->facturas_a_cobrar = json_decode($data);
        $this->importeFactura = json_decode($total);
        $this->importeCobrado = json_decode($total);

        if($cantidad > 1){
            $facturasConEntrega =0;
            foreach($this->facturas_a_cobrar as $f){
                $ver = Factura::where('id', $f)
                    ->select('estado_pago')->get();
                if($ver[0]->estado_pago == 2){
                    $facturasConEntrega ++;
                }
            }
            if($facturasConEntrega > 0){
                session()->flash('info', '¡¡¡ATENCIÓN!!! En la selección de facturas existe alguna que tiene entregas a cuenta...');
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
            // $this->saldoFactura = $ver[0]->importe - $totalEntregas;
            $this->importeCobrado = $ver[0]->importe - $totalEntregas;
        }        
        $this->doAction(2);
    }

    public function cobrar_factura($id)  //no se utiliza
    {
        // if($id != '') {           
        //     $record = Factura::find($id);
        //     $record->update([
        //         'estado_pago' => '1'
        //     ]);
        // }              
        // $this->resetInput();
    } 
       
    public function destroy($id)    //no se utiliza
    {
        // if ($id) { //si es un id válido
        //     $record = Ctacte::where('id', $id); //buscamos el registro
        //     $record->delete(); //eliminamos el registro
        //     $this->resetInput(); //limpiamos las propiedades
        // }
    }
}
