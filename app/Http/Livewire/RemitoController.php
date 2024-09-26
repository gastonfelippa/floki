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
use App\Models\Factura;
use App\Models\Localidad;
use App\Models\Producto;
use App\Models\Remito;
use App\Models\Stock;
use App\Models\StockEnConsignacion;
use App\Models\User;
use DB;

class RemitoController extends Component
{
    //properties
    public $cantidad = 1, $estado='ABIERTO', $inicio_remito, $mostrar_datos;
    public $cliente="Elegir", $empleado="Elegir", $producto="Elegir", $salon =null;
    public $clientes, $empleados, $productos;
    public $selected_id = null, $numRemito;
    public $remitos, $delivery = 0, $action = 1, $forzar_arqueo = 0;  
    public $grabar_encabezado = true, $modificar, $codigo, $barcode;
    public $comercioId, $comercioTipo, $arqueoGralId, $remito_id, $categorias, $articulos =null, $saldoCtaCte;
    public $dirCliente, $apeNomCli, $apeNomRep, $clienteId, $cli_consig, $cli_consig_sing;
    public $comentario, $nro_arqueo, $fecha_inicio, $caja_abierta, $estado_entrega = '0';
    public $contador_filas, $imp_por_hoja, $imp_duplicado;
    public $mostrar_sp = 0, $tiene_sp, $es_producto = 1;
    public $estadoAqueoGral , $ultima_factura;
    
    public function render()
    {     
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        session(['facturaPendiente' => null]); 

        $this->comercioTipo = session('tipoComercio');
        if($this->comercioTipo == 10){
            $this->cli_consig      = 'Clientes';
            $this->cli_consig_sing = 'Cliente';
        }else{
            $this->cli_consig      = 'Consignatarios';
            $this->cli_consig_sing = 'Consignatario';
        } 
        
        $comercio = Comercio::where('id', $this->comercioId)->get();
        if($comercio->count()){
            $this->imp_por_hoja  = $comercio[0]->imp_por_hoja;
            $this->imp_duplicado = $comercio[0]->imp_duplicado;
        }  
        //busco el estado del Arqueo Gral
        $this->estadoAqueoGral = session('estadoArqueoGral');
        if($this->estadoAqueoGral == 'pendiente'){
            //busco si hay alguna factura abierta para la Caja del usuario logueado
            $record = Factura::where('estado', 'abierta')
                ->where('comercio_id', $this->comercioId)
                ->where('user_id', auth()->user()->id);
            //si hay alguna, la dejo terminar, pero al finalizar vuelvo al home   
            if($record->count()) $this->ultima_factura = 1;  
            else $this->forzar_arqueo = 1;
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
      
        $this->categorias = Categoria::where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        
        if($this->comercioTipo == 10) $cliConsig = '0';
        else $cliConsig = '1';
        $this->clientes = Cliente::where('comercio_id', $this->comercioId)
            ->where('consignatario', $cliConsig)
            ->where('nombre', 'not like', 'FINAL')->orderBy('apellido')->get();
 
        //capturo el id del repartidor Salón 
        $this->salon = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('users.name', '...')
            ->where('users.apellido', 'Salón')
            ->where('uc.comercio_id', $this->comercioId)
            ->select('users.id')->get();        

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
                    ->join('localidades as loc','loc.id','c.localidad_id')
                    ->where('remitos.estado','like','abierto')
                    ->where('remitos.comercio_id', $this->comercioId)
                    ->select('remitos.*', 'remitos.numero as nroRemito','c.nombre as nomCli', 'c.apellido as apeCli','c.calle',
                    'c.numero','loc.descripcion', 'u.name as nomRep', 'u.apellido as apeRep')->get();
                $this->numRemito = $encabezado[0]->nroRemito;
                $this->clienteId = $encabezado[0]->cliente_id;
                $this->remito_id = $encabezado[0]->id;
                $this->inicio_remito = false;
                $this->delivery = 1;
                $this->dirCliente = $encabezado[0]->calle . ' ' . $encabezado[0]->numero . ' - ' . $encabezado[0]->descripcion;
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
                ->select('det_remitos.*', DB::RAW("'' as p_id"), 
                    DB::RAW("'' as codigo"), DB::RAW("'' as producto"), DB::RAW("'' as es_producto"))
                ->where('det_remitos.remito_id', $this->remito_id)
                ->where('det_remitos.comercio_id', $this->comercioId)
                ->orderBy('det_remitos.id')->get();  
            
            $this->contador_filas = 0;
            foreach ($info as $i){
                $this->contador_filas ++;
                    $producto = Producto::find($i->producto_id);
                    $i->p_id        = $producto->id;
                    $i->codigo      = $producto->codigo;
                    $i->producto    = $producto->descripcion;
                    $i->es_producto = 1;
            }
        }    
        $this->verificar_impresion();

        return view('livewire.remitos.component', [
            'info'        => $info,
            'encabezado'  => $encabezado
        ]);
    }
    public function verificar_impresion()
    {
        if($this->contador_filas > 10 && $this->imp_por_hoja != '1') $this->emit('limite_superado');
        elseif($this->contador_filas > 20 && $this->imp_por_hoja == '1') $this->emit('limite_20');
    }
    public function doAction($action)
    {
        $this->action = $action;
        if($this->action == 1) $this->resetInput();
    }
    public function resetInput()
    {
        $this->cantidad       = 1;
        $this->barcode        = '';
        $this->producto       = 'Elegir';
        $this->selected_id    = null;
        $this->action         = 1;
        $this->estado_entrega = 0;
        $this->salon          = null;
        $this->forzar_arqueo  = 0;
        $this->es_producto    = 1;
        $this->mostrar_sp     = 0;
    }    
    public function resetInputTodos()
    {
        $this->cantidad       = 1;
        $this->barcode        = '';        
        $this->cliente        = 'Elegir';
        $this->apeNomCli      = null;
        $this->dirCliente     = null;
        $this->empleado       = 'Elegir';
        $this->producto       = 'Elegir';
        $this->articulos      = '';
        $this->delivery       = 0;
        $this->selected_id    = null;
        $this->action         = 1;
        $this->inicio_remito  = true;
        $this->estado_entrega = '0';
        $this->salon          = null;
        $this->remito_id      = null;
        $this->es_producto    = 1;
        $this->mostrar_sp     = 0;
    }
    protected $listeners = [
        'modCliRep'         => 'modCliRep',
        'deleteRow'         => 'destroy',     
        'anularRemito'      => 'anularRemito',
        'elegirFormaDePago' => 'elegirFormaDePago',
        'enviarDatosPago'   => 'enviarDatosPago',
        'terminar_remito'   => 'terminar_remito', 
        'StoreOrUpdate'     => 'StoreOrUpdate',
        'buscarPorCodigo'   => 'buscarPorCodigo',
        'ocultar_sp'        => 'ocultar_sp'
    ];
    public function ocultar_sp()
    {
        $this->mostrar_sp = 0;
        $this->articulos = null;
    }
    public function verSaldo($id)
    { 
        $this->saldoCtaCte = 0;  

        $info = Ctacte::join('clientes as c', 'c.id', 'cta_cte.cliente_id')
            ->where('c.id', $id)
            ->where('c.comercio_id', $this->comercioId)
            ->select('cta_cte.cliente_id', DB::RAW("'' as importe"))->get(); 

        foreach($info as $i) {
            $sumaFacturas=0;
            $sumaRecibos=0;
                //sumo las facturas del cliente
            $importe = Ctacte::join('facturas as f', 'f.id', 'cta_cte.factura_id') 
                ->where('f.cliente_id', $i->cliente_id)
                ->select('f.importe')->get();
            foreach($importe as $imp){
                $sumaFacturas += $imp->importe; //calculo el total de facturas de cada cliente
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
    public function buscarPorCodigo()
    {
        if($this->barcode){
            $this->mostrar_sp = 0;
            $articulos = Producto::where('codigo', $this->barcode)
                ->where('comercio_id', $this->comercioId)->first();
            $this->producto = $articulos->id;
        }
    } 
    public function buscarArticulo($id)
    {
        //$this->mostrar_sp = 0;
        $this->resetInput();
        $this->articulos = Producto::where('comercio_id', $this->comercioId)
                                ->where('categoria_id', $id)->orderBy('descripcion')->get();
    } 
    public function edit($id, $es_producto)
    {
        $this->selected_id = $id;
        $this->es_producto = $es_producto;
        $record = DetRemito::find($id);
        $this->producto = $record->producto_id;
        $this->cantidad = $record->cantidad;
    }
    public function StoreOrUpdateButton($articuloId)
    {
        if($articuloId == 0 && $this->es_producto == 1){
            $this->validate([
                'producto' => 'not_in:Elegir|required',
                'cantidad' => 'required|numeric|min:0|not_in:0']);
        }
        if($articuloId != 0){                  //si cargo desde los botones
            $this->verificar_stock($articuloId); //valido stock producto
        }else{                                 //si cargo desde el form
            $this->verificar_stock($this->producto); //valido stock producto
        }
    }
    public function verificar_stock($id)
    {
        if($this->inicio_remito && $this->cliente == 'Elegir') $this->emit('cargar_consignatario', $this->cli_consig_sing);
        else{
            if($this->selected_id > 0){                 //si modificamos item
                $record = DetRemito::find($this->selected_id);
                $cantidad_detalle = $record->cantidad;
                $stock = Stock::where('producto_id', $id)->first();
                $stock_local = $stock->stock_actual;   
                $stock_local_nuevo = $stock_local + $cantidad_detalle;
                if($stock_local_nuevo == null) $stock_local_nuevo = 0;
            
                if($stock_local_nuevo >= $this->cantidad) $this->StoreOrUpdate($id);
                else $this->emit('stock_no_disponible', 'local', $stock_local); $this->resetInput(); 
            }else{                                     //si creamos item     
                $stock = Stock::where('producto_id', $id)->first();
                $stock_local = $stock->stock_actual;                
                if($stock_local == null) $stock_local = 0;
            
                if($stock_local >= $this->cantidad) $this->StoreOrUpdate($id);
                else $this->emit('stock_no_disponible', 'local', $stock_local); $this->resetInput();  
            }
        }
    }
    public function StoreOrUpdate($id)
    {
        //busca el id del cliente porque al ser inicio de factura este dato está en memoria 
        //y no está grabado todavía
        $cliente = null;
        if($this->inicio_remito) $cliente = $this->cliente;
        else $cliente = $this->clienteId; 
    
        DB::begintransaction();                       
        try{  
            if($this->selected_id > 0) {       //si queremos modificar un remito 
                    //modifico el detalle
                    $record = DetRemito::find($this->selected_id);
                    $cantidad_detalle = $record->cantidad;     
                    $record->update([                       
                        'producto_id' => $this->producto,
                        'cantidad'    => $this->cantidad
                    ]);
                    //modifico stock local 
                    $record = Stock::where('producto_id', $this->producto)->first(); 
                    $stockAnterior = $record['stock_actual'];        
                    $stockNuevo = $stockAnterior + $cantidad_detalle - $this->cantidad;
                    $record->update(['stock_actual' => $stockNuevo]);
                    //modifico stock consignatario
                    $record = StockEnConsignacion::select('id') 
                        ->where('remito_id', $this->remito_id)
                        ->where('comercio_id', $this->comercioId)
                        ->where('producto_id', $this->producto)->first();
                    $record = StockEnConsignacion::find($record->id); 
                    $record->update(['cantidad' => $this->cantidad]);
            }else{
                if($this->cliente == 'Elegir') $this->cliente = null; else $this->delivery = 1;
                if($this->empleado == 'Elegir'){
                    $this->empleado = null;  
                }else{                         //si es delivery, cambiamos el nro_arqueo
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
                        'user_id'        => auth()->user()->id, //id de quien confecciona ela remito
                        'comercio_id'    => $this->comercioId,
                        'arqueo_id'      => $this->nro_arqueo   //nro. de arqueo de caja de quien cobra el remito
                    ]);

                    $this->inicio_remito = false;
                    $this->remito_id = $remito->id;
                }  
              
                $existe = DetRemito::select('id')       //buscamos si el producto ya está cargado
                    ->where('remito_id', $this->remito_id)
                    ->where('comercio_id', $this->comercioId)
                    ->where('producto_id', $id)->get();  
                if($existe->count()){             //actualizamos solo la cantidad
                    $edit_cantidad = DetRemito::find($existe[0]->id); 
                    $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
                    $edit_cantidad->update(['cantidad' => $nueva_cantidad]);
                }else{
                    if($this->contador_filas == 15 && $this->imp_por_hoja != '1') $this->emit('limite_10');
                    elseif($this->contador_filas == 20 && $this->imp_por_hoja == '1') $this->emit('limite_20');
                    else{                      
                        $add_item = DetRemito::create([         //creamos un nuevo detalle
                            'remito_id'   => $this->remito_id,
                            'producto_id' => $id,
                            'cantidad'    => $this->cantidad,
                            'comercio_id' => $this->comercioId
                        ]);    
                    }                    
                }
                //modifico stock
                    $existe = StockEnConsignacion::select('id')  
                        ->where('remito_id', $this->remito_id)
                        ->where('comercio_id', $this->comercioId)
                        ->where('producto_id', $id)->get();
              
                if ($existe->count()){                                //si el producto ya está cargado
                    $edit_cantidad = StockEnConsignacion::find($existe[0]->id); 
                    $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
                    $edit_cantidad->update(['cantidad' => $nueva_cantidad]);//actualizamos solo la cantidad
                }else{
                        $inv_en_consig = StockEnConsignacion::create([   //sino creamos uno nuevo
                            'cliente_id'  => $cliente,
                            'remito_id'   => $this->remito_id,
                            'producto_id' => $id,
                            'cantidad'    => $this->cantidad,
                            'comercio_id' => $this->comercioId
                        ]);
                }
                //modifico stock local
                $record = Stock::where('producto_id', $id)->first();  
                $stockAnterior = $record['stock_actual'];
                $stockNuevo = $stockAnterior - $this->cantidad;  
                $record->update(['stock_actual' => $stockNuevo]);   
            }
            DB::commit();
            if($this->selected_id > 0) session()->flash('message', 'Registro Actualizado');       
            else session()->flash('message', 'Registro Agregado'); 
            $this->emit('itemGrabado'); 
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }     
        $this->resetInput(); 
        return;          
    }         
    public function terminar_remito()
    {
        $record = Remito::find($this->remito_id);
        $record->update([
            'estado' => 'terminado'
        ]); 
        $this->emit('remitoTerminado');             
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
        $loc     = Localidad::find($dataCli->localidad_id);
        $dataRep = User::find($repartidor);

        if($this->inicio_remito) {
            $this->mostrar_datos = 1;
            $this->apeNomCli = $dataCli->apellido . ' ' . $dataCli->nombre;
            $this->dirCliente = $dataCli->calle . ' ' . $dataCli->numero . ' - ' . $loc->descripcion;
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
    public function destroy($id, $producto_id, $cantidad, $es_producto)
    {
        if ($id) {
            $this->es_producto = $es_producto;
            DB::begintransaction();
            try{
                $detFactura = DetRemito::find($id)->delete();  //elimina item del remito

                $record = Stock::where('producto_id', $producto_id)->first();
                $stockAnterior = $record['stock_actual'];
                $stockNuevo = $stockAnterior + $cantidad;      //suma stock local
                $record->update([                       
                    'stock_actual' => $stockNuevo
                ]);
               
                    $record = StockEnConsignacion::select('id') //elimina el item del stock 
                        ->where('remito_id', $this->remito_id)  //en consignación cuando es un producto
                        ->where('producto_id', $producto_id)
                        ->where('comercio_id', $this->comercioId)->first();
                $record = StockEnConsignacion::find($record->id)->delete();   

                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla' => 'Detalle de Remitos',
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
                $remito->update(['estado' => 'anulado']);

                $remito = Remito::find($id)->delete();

                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Facturas',
                    'estado'          => '0',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $comentario,
                    'comercio_id'     => $this->comercioId
                ]);
                //actualizo stock
                $record = DetRemito::where('remito_id', $id)->get();
                foreach ($record as $i){
                        $record = Stock::where('producto_id', $i->producto_id)->first();   
                        $stockAnterior = $record['stock_actual'];
                        $stockNuevo = $stockAnterior + $i->cantidad;  
                        $record->update(['stock_actual' => $stockNuevo]);
                }
                $record = StockEnConsignacion::where('remito_id', $id)->get();
                foreach ($record as $i) $i->delete();

                session()->flash('msg-ok', 'Registro Anulado con éxito!!');
                DB::commit();               
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se anuló...');
            }
            $this->resetInputTodos();
            return;
        }
    }
}

