<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Comercio;
use App\Models\Detcompra;
use App\Models\Detpedido;
use App\Models\Pedido;
use App\Models\Peps;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Stock;
use Carbon\Carbon;
use DB;

class PedidoController extends Component
{
	public $proveedores, $proveedor ='Elegir', $inicio_pedido = true, $pedido_id, $total = 0;
    public $comercioId, $comentario, $selected_id, $search, $action = 1, $empresa, $tab = 'sugerido';
    public $estadoPedido = 'cargado', $producto, $productoId, $pedidoPor = 'productos';
    public $productoPedidoId, $realizarPedidoItem = true, $infoProductoPedido;
    public $producto_id, $cantidad;

    public function render()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio'); 

        $this->proveedores = Proveedor::select('id', 'nombre_empresa')
                            ->orderBy('nombre_empresa')
                            ->where('comercio_id', $this->comercioId)
                            ->whereNotIn('id', Pedido::where('comercio_id', $this->comercioId)
                                                ->where('estado', $this->estadoPedido)
                                                ->select('proveedor_id')->get())
                            ->get();     
        
        //busco información sobre los pedidos cargados
        $info = [];
        if(strlen($this->search) > 0){
            $info = Pedido::join('proveedores as p', 'p.id', 'pedidos.proveedor_id')
                ->where('p.nombre_empresa', 'like', '%' .  $this->search . '%')
                ->where('pedidos.estado', $this->estadoPedido)
                ->where('pedidos.comercio_id', $this->comercioId)
                ->select('pedidos.*', 'p.nombre_empresa')
                ->orderBy('pedidos.updated_at', 'desc')->get();
        }else {
            $info = Pedido::join('proveedores as p', 'p.id', 'pedidos.proveedor_id')
                ->where('pedidos.estado', $this->estadoPedido)
                ->where('pedidos.comercio_id', $this->comercioId)
                ->select('pedidos.*', 'p.nombre_empresa',
                    DB::RAW('0 as importe'))->orderBy('pedidos.updated_at', 'desc')->get();
        }
        if($info->count()){
            foreach ($info as $i){
                $suma_detalle = Detpedido::join('productos as p', 'p.id', 'detpedidos.producto_id')
                    ->where('detpedidos.pedido_id', $i->id)   
                    ->where('p.comercio_id', $this->comercioId)
                    ->select('detpedidos.cantidad', 'p.precio_costo')->get();  
                if($suma_detalle->count()){
                    foreach ($suma_detalle as $j){
                        $i->importe += $j->cantidad * $j->precio_costo;
                    }
                }
            }
        }
         //busco información para sugerir pedidos
        if($this->proveedor != 'Elegir'){
            $infoPedido = Producto::join('producto_proveedores as pp', 'pp.producto_id', 'productos.id')
                ->where('pp.proveedor_id', $this->proveedor)
                ->where('productos.comercio_id', $this->comercioId)
                ->select('productos.*', 'productos.id as productoId', DB::RAW("0 as cantidad_pedido"), 
                    DB::RAW("0 as item_pedido"), DB::RAW("0 as stock_actual"))->get();

            $infoP = Pedido::join('proveedores as p', 'p.id', 'pedidos.proveedor_id')
                ->where('pedidos.proveedor_id', $this->proveedor)
                ->where('pedidos.estado', $this->estadoPedido)
                ->where('pedidos.comercio_id', $this->comercioId)
                ->select('pedidos.id', 'p.nombre_empresa')->get();
            if($infoP->count()){
                $this->pedido_id = $infoP[0]->id;
                $this->empresa = $infoP[0]->nombre_empresa;
            } 
            else $this->pedido_id = false;
        }else $infoPedido = []; 

        if($infoPedido != []){
            foreach($infoPedido as $i){
                $peps = Peps::where('producto_id', $i->productoId)->sum('resto');
                $stock_a_completar = 0;
                $stock_a_completar = $i->stock_ideal - $peps;
                $i->cantidad_pedido = $stock_a_completar;
                $i->stock_actual = $peps;
                if($this->pedido_id){
                    $itemCargado = Detpedido::select('producto_id')
                        ->where('pedido_id', $this->pedido_id)->get();
                    foreach($itemCargado as $l){
                        if($i->productoId == $l->producto_id) $i->item_pedido = true;
                    }    
                }
            }
        }

        //busco detalle de pedido
        $infoDetPedido = Detpedido::select('*')->where('comercio_id', $this->comercioId)->get();

        if($infoDetPedido->count()){
            $infoDetPedido = Detpedido::join('pedidos as p','p.id','detpedidos.pedido_id')
            ->join('productos as pr','pr.id','detpedidos.producto_id')
            ->where('detpedidos.pedido_id', $this->pedido_id)
            ->select('detpedidos.*', 'pr.descripcion as producto', 'pr.precio_costo', DB::RAW("'' as importe"))
            ->orderBy('detpedidos.id')->get(); 
        }  
        $this->total = 0;
        foreach ($infoDetPedido as $i){
            $i->importe=$i->cantidad * $i->precio_costo;
            $this->total += $i->importe;
        }

        //busco el historial de compras para el producto seleccionado
        if($this->productoId){
            $infoHistorial = Detcompra::join('compras as c', 'c.id', 'det_compras.factura_id')
                ->join('proveedores as p', 'p.id', 'c.proveedor_id')
                ->join('productos as pr', 'pr.id', 'det_compras.producto_id')
                ->where('det_compras.producto_id', $this->productoId)
                ->where('det_compras.comercio_id', $this->comercioId)
                ->select('det_compras.cantidad', 'pr.descripcion',
                    'det_compras.precio','c.created_at', 'c.fecha_fact', 'p.nombre_empresa')
                ->orderBy('c.fecha_fact', 'desc')->get();
            if($infoHistorial->count()) $this->producto = $infoHistorial[0]->descripcion;
            else{
                $producto = Producto::find($this->productoId);
                $this->producto = $producto->descripcion;
            }
        }else $infoHistorial = [];


        //busco el listado de productos a reponer con sus respectivos proveedores
        $fecha_actual = Carbon::now();              
        $infoStock = Stock::join('productos as p', 'p.id', 'stock.producto_id')
            ->join('det_compras as dc', 'dc.producto_id', 'stock.producto_id')
            ->join('compras as c', 'c.id', 'dc.factura_id')
            ->join('proveedores as pr', 'pr.id', 'c.proveedor_id')
            ->where('stock.comercio_id', $this->comercioId)
            ->select('p.descripcion', 'stock.stock_actual', 'stock.stock_ideal', 'stock.stock_minimo',
                'dc.precio','pr.nombre_empresa','c.fecha_fact',DB::RAW("0 as diferencia"),DB::RAW("0 as cantidad_pedido"))
            ->orderBy('precio')->get();
        foreach($infoStock as $i){
            $fecha_fact = $i->fecha_fact; 
            $diferencia = $fecha_actual->diffInDays($fecha_fact);
            $i->diferencia = $diferencia;
            $stock_a_completar = 0;
            $stock_a_completar = $i->stock_ideal - $i->stock_actual;
            $i->cantidad_pedido = $stock_a_completar;
        }

        return view('livewire.pedidos.component', [
            'info'          => $info,
            'infoPedido'    => $infoPedido,
            'infoDetPedido' => $infoDetPedido,
            'infoHistorial' => $infoHistorial,
            'infoStock'     => $infoStock
        ]);
    }    
    protected $listeners = [
        'deleteRow'              =>'destroy',
        'deleteItem'             =>'destroyItem',
        'grabar'                 => 'grabar',
        'GrabarItem'             => 'GrabarItem',
        'hacerPedido'            => 'hacerPedido',     
        'recibirPedido'          => 'recibirPedido',     
        'realizarPedidoProducto' => 'realizarPedidoProducto',    
        'verificarProducto'      => 'verificarProducto'     
    ];
    public function doAction($action)
    {
        $this->resetInput();
        if($action == 3){
            $this->estadoPedido = "cargado";
            $this->action = 2;
        }else $this->action = $action;

    }
    private function resetInput()
    {
        $this->proveedor          = 'Elegir';
        $this->selected_id        = null;    
        $this->search             = '';
        $this->action             = 1;
        $this->inicio_pedido      = true;
        $this->pedido_id          = null;
        $this->total              = 0;
        $this->tab                = 'sugerido';
        $this->empresa            = null;
        $this->productoId         = null;
        $this->producto_id        = null;
        $this->cantidad           = null;
        $this->productoPedidoId   = null;
        $this->realizarPedidoItem = true;
    }
    public function edit($proveedor_id)
    {
        $this->action = 2;
        $this->proveedor = $proveedor_id;
        $this->inicio_pedido = false;
        if($this->estadoPedido == 'cargado') $this->tab = "pedido";
        else $this->tab = "pedido";
    }
    public function editItem($detPedido_id)
    {
        $this->selected_id = $detPedido_id;
    }
    public function realizarPedidoProducto($value)
    {
        if($value){
            $this->realizarPedidoItem = $value;
            $this->GrabarItem($this->producto_id, $this->cantidad);
        }
    }
    public function verificarProducto($producto_id, $cantidad, $selectedId)
    {
        $this->producto_id = $producto_id;
        $this->cantidad = $cantidad;
        $this->selected_id = $selectedId;

        //$this->productoPedidoId = $producto_id;
        //busco si el producto ya está pedido con anterioridad a otro proveedor
        if($this->producto_id != null){
            $this->infoProductoPedido = Detpedido::join('pedidos as p', 'p.id', 'detpedidos.pedido_id')
                ->join('proveedores as pr', 'pr.id', 'p.proveedor_id')
                ->join('productos as prod', 'prod.id', 'detpedidos.producto_id')
                ->where('detpedidos.producto_id', $this->producto_id)
                ->where('pr.id', '<>', $this->proveedor)
                ->where('p.estado', 'cargado')
                ->where('detpedidos.comercio_id', $this->comercioId)
                ->orWhere('detpedidos.producto_id', $this->producto_id)
                ->where('pr.id', '<>', $this->proveedor)
                ->where('p.estado', 'pedido')
                ->where('detpedidos.comercio_id', $this->comercioId)
                ->select('pr.nombre_empresa', 'p.estado', 'detpedidos.cantidad', 'prod.descripcion',
                    'p.updated_at')->first();
        
            if($this->infoProductoPedido){
               //dd($this->infoProductoPedido);
                $fecha = Carbon::parse($this->infoProductoPedido->updated_at)->format('d-m-Y');
                //$this->emit('productosPedido',3,2,3,4,5);
                $this->emit('productosPedido',$this->infoProductoPedido->cantidad,
                $this->infoProductoPedido->descripcion, $this->infoProductoPedido->nombre_empresa,$fecha,2);
                $this->realizarPedidoItem = null;
            }
            $this->GrabarItem($this->producto_id, $this->cantidad);
        }
        
    }
    public function buscarHistorial($producto_id)
    {
        $this->productoId = $producto_id;
        $this->tab = "sugerido";
    }
    public function GrabarItem($producto_id, $cantidad)
    {
        if($this->realizarPedidoItem){ 
            DB::begintransaction();
            try{
                if(!$this->selected_id){
                    if($this->inicio_pedido) {
                        $pedido =  Pedido::create([
                            'proveedor_id' => $this->proveedor,            
                            'estado'       => 'cargado',
                            'comercio_id'  => $this->comercioId            
                        ]);
                        $this->inicio_pedido = false;
                        $this->pedido_id = $pedido->id;
                        session()->flash('msg-ok', 'Pedido Creado');
                    }
                    $detpedido =  Detpedido::create([
                        'pedido_id'   => $this->pedido_id,
                        'cantidad'    => $cantidad,            
                        'producto_id' => $producto_id,
                        'comercio_id' => $this->comercioId            
                    ]);
                    $this->tab = "sugerido";
                    session()->flash('msg-ok', 'Item agregado'); 
                }else{
                    $existe = Detpedido::select('id')           //buscamos si el producto ya está cargado
                        ->where('pedido_id', $this->pedido_id)
                        ->where('comercio_id', $this->comercioId)
                        ->where('producto_id', $producto_id)->get();
                    if ($existe->count()){
                        $edit_cantidad = Detpedido::find($existe[0]->id); 
                        $edit_cantidad->update(['cantidad' => $cantidad]);
                        session()->flash('msg-ok', 'Pedido Modificado');              
                    }else session()->flash('msg-ok', 'Item no encontrado');
                    $this->tab = "pedido";
                }
                DB::commit(); 
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
            }
            return;
        }
    }  
    public function destroy($id, $comentario)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $pedido = Pedido::find($id);
                $pedido->update(['estado' => 'anulado']);

                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Pedidos',
                    'estado'          => '0',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $comentario,
                    'comercio_id'     => $this->comercioId
                ]);

                session()->flash('msg-ok', 'Pedido anulado exitosamente!!');
                DB::commit();               
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El Pedido no se anuló...');
            }
            $this->resetInput();
            return;
        }
    }   
    public function destroyItem($id)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $detPedido = Detpedido::find($id)->delete();
                session()->flash('msg-ok', 'Item eliminado exitosamente!!');
                DB::commit();               
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El Item no se eliminó...');
            }
            $this->tab = "pedido";
            return;
        }
    }   
    public function hacerPedido()
    {
        DB::begintransaction();
        try{
            $pedido = Pedido::find($this->pedido_id);
            $pedido->update(['estado' => 'pedido']);
            session()->flash('msg-ok', 'Pedido registrado exitosamente!!');
            DB::commit();               
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El Pedido no se registró...');
        }
        $this->resetInput();
        return;
    }   
    public function recibirPedido()
    {
        DB::begintransaction();
        try{
            $pedido = Pedido::find($this->pedido_id);
            $pedido->update(['estado' => 'recibido']);
            session()->flash('msg-ok', 'Pedido recibido exitosamente!!');
            DB::commit();               
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El estado del Pedido no se registró...');
        }
        $this->resetInput();
        return;
    }   
}
