<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\CajaUsuario;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Comercio;
use App\Models\Ctacte;
use App\Models\DetRemito;
use App\Models\Producto;
use App\Models\Remito;
use App\Models\StockEnConsignacion;
use App\Models\User;
use Carbon\Carbon;
use DB;

class RemitoController extends Component
{
    //properties
    public $cantidad = 1, $precio, $estado='ABIERTO', $inicio_remito, $mostrar_datos;
    public $cliente="Elegir", $empleado="Elegir", $producto="Elegir", $salon =null;
    public $clientes, $empleados, $productos;
    public $selected_id = null, $search, $numRemito, $action = 1;
    public $remitos,  $total, $importe, $totalAgrabar, $delivery = 0;  
    public $grabar_encabezado = true, $modificar, $codigo, $barcode;
    public $comercioId, $arqueoGralId, $remito_id, $categorias, $articulos =null, $saldoCtaCte, $saldoACobrar;
    public $dirCliente, $apeNomCli, $apeNomRep, $clienteId;
    public $comentario, $nro_arqueo, $fecha_inicio, $caja_abierta, $estado_entrega = '0';
    public $contador_filas, $imp_por_hoja, $imp_duplicado, $stock_antes_de_modificar;
    
    public function render()
    {     
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        $comercio = Comercio::where('id', $this->comercioId)->get();
        if($comercio->count())
        {
            $this->imp_por_hoja  = $comercio[0]->imp_por_hoja;
            $this->imp_duplicado = $comercio[0]->imp_duplicado;
        }

        //vemos si tenemos una caja habilitada con nuestro user_id
        $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', auth()->user()->id)
            ->where('caja_usuarios.estado', '1')->select('caja_usuarios.*')->get();
        $this->caja_abierta = $caja_abierta->count();
        if($caja_abierta->count()){
            $this->nro_arqueo = $caja_abierta[0]->id;  //este es el nro_arqueo del cajero, pero puede cambiar por el del delivery
            $this->fecha_inicio = $caja_abierta[0]->created_at;
            //busca si hay que hacer el arqueo gral.
            $this->arqueoGralId = session('idArqueoGral');
            if($this->arqueoGralId == 0){  //debe hacer el arqueo gral.
                return view('arqueodecaja');
            }
        }        

        $this->productos = Producto::where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        $this->clientes = Cliente::where('comercio_id', $this->comercioId)->orderBy('apellido')->get();
        $this->categorias = Categoria::where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        
        //muestro solo los repartidores que tienen caja abierto
        $this->empleados = User::join('model_has_roles as mhr', 'mhr.model_id', 'users.id')
            ->join('roles as r', 'r.id', 'mhr.role_id')
            ->join('caja_usuarios as cu', 'cu.caja_usuario_id', 'users.id')
            ->where('r.alias', 'Repartidor')
            ->where('r.comercio_id', $this->comercioId)
            ->where('cu.estado', '1')
            ->select('users.id', 'users.name', 'users.apellido')->get();
        
        //capturo el id del repartidor Salón 
        $this->salon = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('users.name', '...')
            ->where('users.apellido', 'Salón')
            ->where('uc.comercio_id', $this->comercioId)
            ->select('users.id')->get();        

        // $dProducto = Producto::find($this->producto);
        // if($dProducto != null) $this->precio = $dProducto->precio_venta_l2; 
        // else $this->precio = '';
        $encabezado = Remito::select('*')->where('comercio_id', $this->comercioId)->withTrashed()->get(); 
        //si es la primera remito, le asigno el nro: 1
        if($encabezado->count() == 0){
            $this->numRemito = 1;
            $this->inicio_remito = true;       
        }else{  //sino, busco si hay alguna remito abierto
            $encabezado = Remito::where('remitos.estado','like','abierto')->where('remitos.comercio_id', $this->comercioId)
                ->select('remitos.*', 'remitos.numero as nroRemito')->get();
                //verifico si es delivery para recuperar los datos de Cli/Rep
            if($encabezado->count() > 0 && $encabezado[0]->cliente_id <> null){                
                $encabezado = Remito::join('clientes as c','c.id','remitos.cliente_id')
                    ->join('users as u','u.id','remitos.repartidor_id')
                    ->where('remitos.estado','like','abierto')
                    ->where('remitos.comercio_id', $this->comercioId)
                    ->select('remitos.*', 'remitos.numero as nroRemito','c.nombre as nomCli', 'c.apellido as apeCli','c.calle',
                    'c.numero', 'u.name as nomRep', 'u.apellido as apeRep')->get();
                $this->numRemito = $encabezado[0]->nroRemito;
                $this->clienteId = $encabezado[0]->cliente_id;
                $this->remito_id = $encabezado[0]->id;
                $this->inicio_remito = false;
                $this->delivery = 1;
                $this->dirCliente = $encabezado[0]->calle . ' ' . $encabezado[0]->numero;
                $this->verSaldo($encabezado[0]->cliente_id);
                $this->mostrar_datos = 0;
            }elseif($encabezado->count() > 0) {
                $this->inicio_remito = false;
                $this->numRemito = $encabezado[0]->nroRemito;
                $this->remito_id = $encabezado[0]->id;
                $this->delivery = 0;          //dice si el remito es delivery
                $this->mostrar_datos = 0;     //muestra datos del modal, no de la BD       
            }else {                           //si no hay una remito abierto le sumo 1 a la última
                $encabezado = Remito::select('numero')
                ->where('comercio_id', $this->comercioId)
                ->withTrashed()
                ->orderBy('numero', 'desc')->get();                             
                $this->numRemito = $encabezado[0]->numero + 1;
                $this->delivery = 0;
                $this->inicio_remito = true;
            }
        }
        $info = DetRemito::select('*')->where('comercio_id', $this->comercioId)->get();
        if($info->count() > 0){
            $info = DetRemito::join('remitos as r','r.id','det_remitos.remito_id')
                ->join('productos as p','p.id','det_remitos.producto_id')
                ->select('det_remitos.*', 'p.id as producto_id', 'p.codigo', 'p.descripcion as producto')
                ->where('det_remitos.remito_id', $this->remito_id)
                ->where('det_remitos.comercio_id', $this->comercioId)
                ->orderBy('det_remitos.id', 'asc')->get();  
        }    
        $this->contador_filas = 0;
        foreach ($info as $i){
            $this->contador_filas ++;
        }

        return view('livewire.remitos.component', [
            'info'        => $info,
            'encabezado'  => $encabezado
        ]);
    }
    public function doAction($action)
    {
        $this->action = $action;
    }
    public function resetInput()
    {
        $this->cantidad           = 1;
        $this->barcode            = '';
        $this->precio             = '';
        $this->producto           = 'Elegir';
        $this->selected_id        = null;
        $this->action             = 1;
        $this->search             = '';
        $this->estado_entrega     = 0;
        $this->salon              = null;
        $this->f_de_pago          = null;
        $this->nro_comp_pago      = null;
        $this->mercadopago        = null;
        $this->comentarioPago     = '';
        $this->forzar_arqueo      = 0;
    }    
    public function resetInputTodos()
    {
        $this->cantidad       = 1;
        $this->barcode        = '';
        $this->precio         = '';        
        $this->cliente        = 'Elegir';
        $this->dirCliente     = null;
        $this->empleado       = 'Elegir';
        $this->producto       = 'Elegir';
        $this->articulos      = '';
        $this->delivery       = 0;
        $this->selected_id    = null;
        $this->action         = 1;
        $this->search         = '';
        $this->inicio_remito = true;
        $this->estado_entrega = '0';
        $this->salon          = null;
        $this->remito_id     = null;
    }
    protected $listeners = [
        'modCliRep'         => 'modCliRep',
        'deleteRow'         => 'destroy',
        'factura_contado'   => 'factura_contado',
        'factura_ctacte'    => 'factura_ctacte',      
        'anularFactura'     => 'anularFactura',
        'elegirFormaDePago' => 'elegirFormaDePago',
        'enviarDatosPago'   => 'enviarDatosPago',
        'dejar_pendiente'   => 'dejar_pendiente', 
        'StoreOrUpdate'     => 'StoreOrUpdate'
    ];
    public function verSaldo($id)
    {            
        $info2 = Ctacte::join('clientes as c', 'c.id', 'cta_cte.cliente_id')
            ->where('c.id', $id)
            ->where('c.comercio_id', $this->comercioId)
            ->select('cta_cte.cliente_id', DB::RAW("'' as importe"))->get(); 

        foreach($info2 as $i) {
            $sumaFacturas=0;
            $sumaRecibos=0;
                //sumo las remitos del cliente
            $importe = Ctacte::join('recibos as f', 'f.id', 'cta_cte.recibo_id') 
                ->where('f.cliente_id', $i->cliente_id)
                ->select('f.importe')->get();
            foreach($importe as $imp){
                $sumaFacturas += $imp->importe; //calculo el total de remitos de cada cliente
            }
                //sumo los recibos del cliente
            $importe = Ctacte::join('recibos as r', 'r.id', 'cta_cte.recibo_id') 
                ->where('r.cliente_id', $i->cliente_id)
                ->select('r.importe')->get();
            foreach($importe as $imp){
                $sumaRecibos += $imp->importe; //calculo el total de recibos de cada cliente
            }
            //calculo el total para cada cliente
            $i->importe = $sumaRecibos - $sumaFacturas;
            $this->saldoCtaCte = $i->importe;
        }
    }   
    public function buscarArticulo($id)
    {
        $this->articulos = Producto::where('comercio_id', $this->comercioId)
                                ->where('categoria_id', $id)->orderBy('descripcion', 'asc')->get();
    } 
    public function edit($id)
    {
        $record = DetRemito::find($id);
        $this->selected_id = $id;
        $this->producto    = $record->producto_id;
        $this->cantidad    = $record->cantidad;
    }
    public function StoreOrUpdateButton($articuloId)
    {
        if($articuloId != 0) $this->producto = $articuloId;
        $this->verificar_stock();
    }
    public function verificar_stock()
    {
        $producto = Producto::find($this->producto);
        $stock_local = $producto->stock;
        if($stock_local == null) $stock_local = 0;
        if($stock_local >= $this->cantidad){
            $this->StoreOrUpdate();
        }else $this->emit('stock_no_disponible', 'local', $stock_local);
    }
    public function StoreOrUpdate()
    {
        //busca el id del cliente porque al ser inicio de factura este dato está en memoria 
        //y no está grabado todavía
        $cliente = null;
        if($this->inicio_remito) $cliente = $this->cliente;
        else $cliente = $this->clienteId; 
    
        $this->validate([
            'producto' => 'not_in:Elegir'
        ]);            
        $this->validate([
            'cantidad' => 'required|numeric|min:0|not_in:0',
            'producto' => 'required'
        ]);    
        DB::begintransaction();                       
        try{  
            if($this->selected_id > 0) {                      //si queremos modificar un remito, 
                $record = DetRemito::find($this->selected_id);//primero modificamos el detalle,
                $cantidad_detalle = $record->cantidad;        //luego modificamos el stock local 
                $record->update([                    //y al final modificamos el stock del consignatario    
                    'producto_id' => $this->producto,
                    'cantidad'    => $this->cantidad
                ]);
                $record = Producto::find($this->producto);
                $stockAnterior = $record['stock']; 
                if($cantidad_detalle > $this->cantidad){         //agrego stock local
                    $stock_a_agregar = $cantidad_detalle - $this->cantidad;
                    $stockNuevo = $stockAnterior + $stock_a_agregar;
                }elseif($cantidad_detalle < $this->cantidad){    //resto stock local
                    $stock_a_descontar = $this->cantidad - $cantidad_detalle;
                    $stockNuevo = $stockAnterior - $stock_a_descontar; 
                }   
                $record->update([                       
                    'stock' => $stockNuevo
                ]);
                $record = StockEnConsignacion::select('id')      //modifico el stock del consignatario
                    ->where('remito_id', $this->remito_id)
                    ->where('comercio_id', $this->comercioId)
                    ->where('producto_id', $this->producto)->first();
                $record = StockEnConsignacion::find($record->id); 
                $record->update([                                                 
                    'cantidad' => $this->cantidad
                ]);
            }else{
                if($this->cliente == 'Elegir') $this->cliente = null; else $this->delivery = 1;
                if($this->empleado == 'Elegir'){
                    $this->empleado = null;  
                }else{            //si es delivery, cambiamos el nro_arqueo
                    $this->estado_entrega = 1;    
                    $nroArqueo = CajaUsuario::where('caja_usuarios.caja_usuario_id', $this->empleado)
                        ->where('caja_usuarios.estado', '1')->get();
                    if($nroArqueo->count()){
                        $this->nro_arqueo = $nroArqueo[0]->id;  //este es el nro_arqueo del delivery
                    }  
                }  
                if($this->inicio_remito) {
                    $remito = Remito::create([
                        'numero'         => $this->numRemito,
                        'cliente_id'     => $this->cliente,
                        'estado'         => 'abierto',
                        'repartidor_id'  => $this->empleado,
                        'user_id'        => auth()->user()->id, //id de quien confecciona la remito
                        'comercio_id'    => $this->comercioId,
                        'arqueo_id'      => $this->nro_arqueo   //nro. de arqueo de caja de quien cobra la remito
                    ]);

                    $this->inicio_remito = false;
                    $this->remito_id = $remito->id;
                }  
                $existe = DetRemito::select('id')              //buscamos si el producto ya está cargado
                    ->where('remito_id', $this->remito_id)
                    ->where('comercio_id', $this->comercioId)
                    ->where('producto_id', $this->producto)->get();
                if($existe->count()){
                    $edit_cantidad = DetRemito::find($existe[0]->id); 
                    $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
                    $edit_cantidad->update([                //actualizamos solo la cantidad                                      
                        'cantidad' => $nueva_cantidad
                    ]);
                }else{
                    if($this->contador_filas == 15 && $this->imp_por_hoja != '1') $this->emit('limite_10');
                    elseif($this->contador_filas == 20 && $this->imp_por_hoja == '1') $this->emit('limite_20');
                    else{
                        $add_item = DetRemito::create([         //creamos un nuevo detalle
                            'remito_id'   => $this->remito_id,
                            'producto_id' => $this->producto,
                            'cantidad'    => $this->cantidad,
                            'comercio_id' => $this->comercioId
                        ]);                       
                    }
                } 
                $existe = StockEnConsignacion::select('id')  
                    ->where('remito_id', $this->remito_id)
                    ->where('comercio_id', $this->comercioId)
                    ->where('producto_id', $this->producto)->get();
                if ($existe->count()){                                      //si el producto ya está cargado
                    $edit_cantidad = StockEnConsignacion::find($existe[0]->id); 
                    $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
                    $edit_cantidad->update(['cantidad' => $nueva_cantidad]);//actualizamos solo la cantidad
                }else{
                    $inv_en_consig = StockEnConsignacion::create([          //sino creamos uno nuevo
                        'cliente_id'  => $cliente,
                        'remito_id'   => $this->remito_id,
                        'producto_id' => $this->producto,
                        'cantidad'    => $this->cantidad,
                        'comercio_id' => $this->comercioId
                    ]);
                }
                $record = Producto::find($this->producto);   //resto stock local
                $stockAnterior = $record['stock'];
                $stockNuevo = $stockAnterior - $this->cantidad;  
                $record->update(['stock' => $stockNuevo]);
               
            }
            DB::commit();
            if($this->selected_id > 0) session()->flash('message', 'Registro Actualizado');       
            else session()->flash('message', 'Registro Agregado');  
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }     
        $this->resetInput(); 
        return;          
    }    
    public function factura_contado()
    {
        DB::begintransaction();                         //iniciar transacción para grabar
        try{
            $record = Remito::find($this->remito_id);
            $record->update([
                'estado'        => 'contado',
                'estado_pago'   => '1',
                'importe'       => $this->total,
                'forma_de_pago' => $this->f_de_pago,
                'nro_comp_pago' => $this->nro_comp_pago,  //nro ticket tarjeta o nro transferencia
                'mercadopago'   => $this->mercadopago,
                'comentario'    => $this->comentarioPago
            ]);
            DB::commit();
            $this->emit('facturaCobrada');
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }              
        $this->resetInputTodos();
    }
    public function factura_ctacte($cliId)
    {
        $info = json_decode($cliId);
        $this->clienteId = $info->cliente_id;
        DB::begintransaction();                         //iniciar transacción para grabar
        try{ 
            $record = Remito::find($this->remito_id);
            $record->update([
                'cliente_id' => $this->clienteId,
                'estado' => 'ctacte',
                'estado_pago' => '0',
                'importe' => $this->total
            ]);
            Ctacte::create([
                'cliente_id' => $this->clienteId,
                'remito_id' => $this->remito_id
            ]);
            $record = Cliente::find($this->clienteId); //marca que el cliente tiene un saldo en ctacte
            $record->update([
                'saldo' => '1'
            ]);
            $this->emit('facturaCtaCte');
            DB::commit();               
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInputTodos();
        return;
    }        
    public function dejar_pendiente()
    {
        $record = Remito::find($this->remito_id);
        $record->update([
            'estado' => 'terminado'
        ]);              
        session()->flash('message', 'Remito Terminado'); 
        $this->resetInputTodos();
    }
    public function modCliRep($data)
    {
        $info = json_decode($data);
        $repartidor='';
        //si el repartidor es el Salon, el nro_arqueo debe ser el de la caja que está facturando
        if($info->empleado_id == "Salon"){
            $repartidor = $this->salon[0]->id; 
            $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', auth()->user()->id)
                ->where('caja_usuarios.estado', '1')->get();
            $this->nro_arqueo = $caja_abierta[0]->id;  //este es el nro_arqueo de la Caja que facturó  
        }else{       //sino debemos buscar el nro_arqueo del delivery
            $repartidor = $info->empleado_id;
            $dataRep = User::find($repartidor);
            $this->apeNomRep = $dataRep->apellido . ' ' . $dataRep->name;        
            $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', $dataRep->id)
                ->where('caja_usuarios.estado', '1')->get();
            $this->nro_arqueo = $caja_abierta[0]->id;             
        } 
        $dataCli = Cliente::find($info->cliente_id);
        // dd($repartidor);
        $dataRep = User::find($repartidor);

        if($this->inicio_remito) {
            $this->mostrar_datos = 1;
            $this->apeNomCli = $dataCli->apellido . ' ' . $dataCli->nombre;
            $this->dirCliente = $dataCli->calle . ' ' . $dataCli->numero;
            $this->verSaldo($dataCli->id);
            $this->apeNomRep = $dataRep->apellido . ' ' . $dataRep->name;
            $this->cliente = $info->cliente_id;
            $this->empleado = $repartidor;
        }else {
            if($this->estado_entrega == 0) $this->estado_entrega = '1';
            $record = Remito::find($info->remito_id);
            $record->update([
                'cliente_id'    => $info->cliente_id,
                'repartidor_id' => $repartidor,
                'estado'        => 'abierto',
                'arqueo_id'     => $this->nro_arqueo
            ]);
            $this->delivery = 1;
            $this->mostrar_datos = 0;
        }
        session()->flash('message', 'Encabezado Modificado...');
    }
    public function destroy($id, $producto_id, $cantidad)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $detFactura = DetRemito::find($id)->delete();  //elimina item del remito

                $record = Producto::find($producto_id);        //suma stock local
                $stockAnterior = $record['stock'];
                $stockNuevo = $stockAnterior + $cantidad;  
                $record->update([                       
                    'stock' => $stockNuevo
                ]);
                $record = StockEnConsignacion::select('id')    //elimina el item del stock en consignación
                    ->where('remito_id', $this->remito_id)
                    ->where('producto_id', $producto_id)
                    ->where('comercio_id', $this->comercioId)->first();
                $record = StockEnConsignacion::find($record->id)->delete();   

                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla' => 'Detalle de Facturas',
                    'estado' => '0',
                    'user_delete_id' => auth()->user()->id,
                    'comentario' => $this->comentario,
                    'comercio_id' => $this->comercioId
                ]);
                DB::commit();               
                session()->flash('msg-ok', 'Registro eliminado con éxito!!');
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
            }
            $this->resetInput();
            return;
        }
    }
    public function anularRemito($id, $comentario)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $remito = Remito::find($id);
                $remito->update([                    
                    'estado' => 'anulado'
                    ]);
                $remito = Remito::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Facturas',
                    'estado'          => '0',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $comentario,
                    'comercio_id'     => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Registro Anulado con éxito!!');
                DB::commit();               
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se anuló...');
            }
            $this->resetInput();
            return;
        }
    }
}

