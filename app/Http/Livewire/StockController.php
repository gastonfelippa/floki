<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Stock;
use App\Models\StockEnConsignacion;
use App\Models\Subproducto;
use DB;

class StockController extends Component
{
    public $selected_id, $search = '', $placeHolderSearch = "Buscar por 'Código' o por 'Descripción'";
    public $action = 1, $stock, $stockHistorial, $producto_id=null, $producto=null, $cliente_id=null;
    public $nombreCliente, $cliente = 'Elegir', $clientes, $infoCli;
    public $title = "Stock", $valorTotalStock = 0, $valorTotalStockPorConsignatario = 0;
    public $valorTotalStockLocal = 0, $valorTotalStockConsignacion = 0;
    public $info_sp, $mostrar_subproducto = 0, $subproducto_id;
    public $comercioId, $modConsignaciones, $comercioTipo;

    public function render()
    {
        //busca el comercio que está en sesión
		$this->comercioId = session('idComercio');
        $this->modConsignaciones = session('modConsignaciones');
        $this->comercioTipo = session('tipoComercio');

        if($this->comercioTipo == 11){
            $this->clientes = Cliente::where('comercio_id', $this->comercioId)
                    ->where('consignatario', '1')->orderBy('apellido')->get();
        }else{
            $this->clientes = Cliente::join('stock_en_consignacion as s', 's.cliente_id', 'clientes.id')
                ->where('clientes.comercio_id', $this->comercioId)
                ->where('s.remito_id', '<>', null)
                ->groupBy('s.cliente_id')->select('clientes.*', 's.cliente_id')->orderBy('clientes.apellido')->get();
        }
        $this->stockPorCliente($this->cliente);
          

		if(strlen($this->search) && $this->action == 1){  //buscar >> stock local
            $this->valorTotalStock = 0;
            $info = Producto::select('productos.*', DB::RAW("0 as stock_local"), DB::RAW("'' as stock_en_consignacion"),
                DB::RAW("'' as stock_total"), DB::RAW("0 as subtotal"), DB::RAW("'' as producto"))
                ->where('codigo', $this->search)
				->where('comercio_id', $this->comercioId)
				->orWhere('descripcion', 'like', '%' . $this->search .'%')
				->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();  
            foreach ($info as $i){
                $this->info_sp = Subproducto::join('productos as p', 'p.id', 'subproductos.producto_id')
                    ->select('subproductos.*', 'p.precio_costo', DB::RAW("0 as stock_local"), DB::RAW("0 as stock_en_consignacion"),
                        DB::RAW("0 as stock_total"), DB::RAW("0 as subtotal"), DB::RAW("'' as producto"))
                    ->where('producto_id', $i->id)->get();                
                if($this->info_sp->count()){
                    $this->mostrar_subproducto = 1;
                    foreach ($this->info_sp as $l){
                        $stock = Stock::where('subproducto_id', $l->id)->first();
                        if($stock->count()) $l->stock_local = $stock->stock_actual;
                        $stock_en_consig = StockEnConsignacion::where('subproducto_id', $l->id)->get()->sum('cantidad');
                        if($stock_en_consig != null) $l->stock_en_consignacion = $stock_en_consig;
                        $l->stock_total = $l->stock_local + $l->stock_en_consignacion;
                        $l->subtotal = $l->stock_total * $l->precio_costo;
                        $this->valorTotalStock += $l->subtotal;
                        $l->producto = 0; 
                    }
                }else{
                    $this->mostrar_subproducto = 0;
                    $stock = Stock::where('producto_id', $i->id)->first();
                    if($stock) $i->stock_local = $stock->stock_actual;
                    else $i->stock_local = 0;
                    
                    $stock_en_consig = StockEnConsignacion::where('producto_id', $i->id)->get()->sum('cantidad');  
                    if($stock_en_consig) $i->stock_en_consignacion = $stock_en_consig;
                    else $i->stock_en_consignacion = 0;  

                    $i->stock_total = $i->stock_local + $i->stock_en_consignacion;
                    $i->subtotal = $i->stock_total * $i->precio_costo;
                    $this->valorTotalStock += $i->subtotal; 
                    $i->producto = 1;
                }  
            }
		}elseif($this->action == 1){   //stock local
            $this->mostrar_subproducto = 0;
            $this->valorTotalStock = 0;            
            $this->valorTotalStockLocal = 0;
            $this->valorTotalStockConsignacion = 0;
            $info = Producto::select('productos.*', DB::RAW("0 as stock_local"), DB::RAW("'' as stock_en_consignacion"),
                DB::RAW("'' as stock_total"), DB::RAW("0 as subtotal"))
                ->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();            
            foreach ($info as $i){
                $tiene_sp = Subproducto::where('producto_id', $i->id)->get();
                if($tiene_sp->count()){
                    $total = 0;
                    $total_c = 0;
                    foreach ($tiene_sp as $l){
                        $stock = Stock::where('subproducto_id', $l->id)->first();
                        if($stock->count()) $total += $stock->stock_actual;

                        $stock_en_consig = StockEnConsignacion::where('subproducto_id', $l->id)->get()->sum('cantidad'); 
                        if($stock_en_consig) $total_c += $stock_en_consig;
                    }
                    $i->stock_local = $total;
                    $i->stock_en_consignacion = $total_c;
                }else{
                    $stock = Stock::where('producto_id', $i->id)->first();
                    if($stock) $i->stock_local = $stock->stock_actual;
                    else $i->stock_local = 0;
                   
                    $stock_en_consig = StockEnConsignacion::where('producto_id', $i->id)->get()->sum('cantidad');  
                    if($stock_en_consig) $i->stock_en_consignacion = $stock_en_consig;
                    else $i->stock_en_consignacion = 0;
                } 
                $totalLocal = $i->stock_local * $i->precio_costo;
                $this->valorTotalStockLocal += $totalLocal;                                    

                $totalConsignacion = $i->stock_en_consignacion * $i->precio_costo;
                $this->valorTotalStockConsignacion += $totalConsignacion;

                $i->stock_total = $i->stock_local + $i->stock_en_consignacion;
                $i->subtotal = $i->stock_total * $i->precio_costo;
                $this->valorTotalStock += $i->subtotal;
            }
            $this->verStockEnConsignacion($this->producto_id, null);   //null,null
            $this->verHistorialStockEnConsignacion($this->producto_id, $this->cliente_id, null);  //null,null
		}elseif($this->action == 2){  //stock consignatario
            $info = Producto::select('id', 'codigo', 'descripcion', DB::RAW("'' as stock_en_consignacion"), 
            DB::RAW("'' as stock_total"), DB::RAW("'' as producto"), DB::RAW("'' as descProducto"))
                ->where('comercio_id', $this->comercioId)
                ->orderBy('descripcion')
                ->get();
        }
        return view('livewire.stock.component', [
            'info' => $info
        ]);
    }
    public function stockPorCliente($id){
        $this->valorTotalStockPorConsignatario = 0;
        $this->infoCli = StockEnConsignacion::join('clientes as c', 'c.id', 'stock_en_consignacion.cliente_id')
            ->where('stock_en_consignacion.cliente_id', $id)
            ->select('stock_en_consignacion.producto_id', 'stock_en_consignacion.subproducto_id', DB::RAW("'' as articuloId"), DB::RAW("'' as articuloCodigo"), 
                     DB::RAW("'' as cantidad"),  DB::RAW("'' as articuloDesc"), DB::RAW("'' as precio_venta"), DB::RAW("0 as subtotal"))
            ->groupBy('stock_en_consignacion.producto_id', 'stock_en_consignacion.subproducto_id')->get();
        foreach ($this->infoCli as $i){
            if($i->producto_id != null){
                if($this->comercioTipo == 11){
                    $stock_en_consig = StockEnConsignacion::join('clientes as c', 'c.id', 'stock_en_consignacion.cliente_id')
                        ->join('productos as p', 'p.id', 'stock_en_consignacion.producto_id')
                        ->where('stock_en_consignacion.producto_id', $i->producto_id)
                        ->where('stock_en_consignacion.cliente_id', $id)
                        ->select('p.id', 'p.codigo', 'p.descripcion', 'p.precio_venta_l2 as precio_venta')->first();
                }else{
                    $stock_en_consig = StockEnConsignacion::join('clientes as c', 'c.id', 'stock_en_consignacion.cliente_id')
                        ->join('productos as p', 'p.id', 'stock_en_consignacion.producto_id')
                        ->where('stock_en_consignacion.producto_id', $i->producto_id)
                        ->where('stock_en_consignacion.cliente_id', $id)
                        ->select('p.id', 'p.codigo', 'p.descripcion', 'p.precio_venta_l1 as precio_venta')->first();
                }
                $i->articuloId = $stock_en_consig->id;
                $i->articuloCodigo = $stock_en_consig->codigo;
                $i->articuloDesc = $stock_en_consig->descripcion;
                $i->precio_venta = $stock_en_consig->precio_venta;

                $stock_en_consig_cantidad = StockEnConsignacion::where('producto_id', $i->producto_id)
                    ->where('cliente_id', $id)
                    ->get()->sum('cantidad');
            }else{
                $stock_en_consig = StockEnConsignacion::join('clientes as c', 'c.id', 'stock_en_consignacion.cliente_id')
                    ->join('subproductos as sp', 'sp.id', 'stock_en_consignacion.subproducto_id')    
                    ->join('productos as p', 'p.id', 'sp.producto_id')
                    ->where('stock_en_consignacion.subproducto_id', $i->subproducto_id)
                    ->where('stock_en_consignacion.cliente_id', $id)
                    ->select('sp.id', 'sp.descripcion', 'p.precio_venta_l2')->first();
                $i->articuloId = $stock_en_consig->id;
                $i->articuloCodigo = $stock_en_consig->id;
                $i->articuloDesc = $stock_en_consig->descripcion;
                $i->precio_venta_l2 = $stock_en_consig->precio_venta_l2;

                $stock_en_consig_cantidad = StockEnConsignacion::where('subproducto_id', $i->subproducto_id)
                    ->where('cliente_id', $id)
                    ->get()->sum('cantidad');
            }
            $i->cantidad = $stock_en_consig_cantidad;
            $i->subtotal = $i->cantidad * $i->precio_venta;
            $this->valorTotalStockPorConsignatario += $i->subtotal;
        }
        $this->producto_id=null;    
        $this->subproducto_id=null;    
    }
    public function doAction($action)
    {
        $this->action = $action;
        if($action == 1) $this->title = "Stock"; 
        else{
            if($this->comercioTipo == 10) $this->title = "Stock en Condicional";
            else $this->title = "Stock en Consignación";
        }
        $this->resetInput();
    }
    public function resetInput()
    {
        $this->search      = '';
        $this->producto_id = null;
        $this->producto    = null;
        $this->cliente_id  = null;
        $this->cliente     = 'Elegir';
    }
    public function recargarPagina()
    {
        $this->resetInput();
    }
    public function verStockEnConsignacion($producto_id, $prod)
    {
        $this->producto_id = $producto_id;
        if($prod == 1){    //si es un producto
            if($this->producto_id != null)
            {
                $producto = Producto::find($this->producto_id);
                $this->producto = $producto['descripcion'];
            }
            $this->stock = StockEnConsignacion::join('productos as p', 'p.id', 'stock_en_consignacion.producto_id')
                ->join('clientes as c', 'c.id', 'stock_en_consignacion.cliente_id')
                ->where('stock_en_consignacion.comercio_id', $this->comercioId)
                ->where('stock_en_consignacion.producto_id', $producto_id)
                ->select('c.nombre','c.apellido', 'p.descripcion', 'p.id', 'c.id as clienteId',
                    DB::RAW("'' as cantidad"), DB::RAW("'' as producto"))
                ->groupBy('c.nombre','c.apellido', 'p.descripcion', 'p.id', 'c.id')->get();
            foreach ($this->stock as $i){
                $cantidad = StockEnConsignacion::join('clientes as c', 'c.id', 'stock_en_consignacion.cliente_id')
                    ->where('producto_id', $i->id)
                    ->where('c.id', $i->clienteId)
                    ->get()->sum('cantidad');
                $i->cantidad = $cantidad;
                $i->producto = 1;
            }
        }else{        //si es un subproducto
            if($this->producto_id != null)
            {
                $producto = Subproducto::find($this->producto_id);
                $this->producto = $producto['descripcion'];
            }
            $this->stock = StockEnConsignacion::join('subproductos as p', 'p.id', 'stock_en_consignacion.subproducto_id')
                ->join('clientes as c', 'c.id', 'stock_en_consignacion.cliente_id')
                ->where('stock_en_consignacion.comercio_id', $this->comercioId)
                ->where('stock_en_consignacion.subproducto_id', $producto_id)
                ->select('c.nombre','c.apellido', 'p.descripcion', 'p.id', 'c.id as clienteId',
                    DB::RAW("'' as cantidad"), DB::RAW("'' as producto"))
                ->groupBy('c.nombre','c.apellido', 'p.descripcion', 'p.id', 'c.id')->get();
            foreach ($this->stock as $i){
                $cantidad = StockEnConsignacion::join('clientes as c', 'c.id', 'stock_en_consignacion.cliente_id')
                    ->where('subproducto_id', $i->id)
                    ->where('c.id', $i->clienteId)
                    ->get()->sum('cantidad');
                $i->cantidad = $cantidad;
                $i->producto = 0;
            }
        }
        if($this->producto_id != null) $this->emit('abrirModal');
    }
    public function verHistorialStockEnConsignacion($producto_id, $cliente_id, $prod)
    {
        $this->producto_id = $producto_id;
        $this->cliente_id = $cliente_id;
        if($prod == 1){    //si es un producto
            if($this->producto_id != null)
            {
                $producto = Producto::find($this->producto_id);
                $this->producto = $producto['descripcion'];
            }
            if($this->cliente_id != null)
            {
                $record = Cliente::find($this->cliente_id);
                $this->nombreCliente = $record['apellido'] . ' ' . $record['nombre'];
            }
            $this->stockHistorial = StockEnConsignacion::where('stock_en_consignacion.cliente_id', $cliente_id)
                ->where('stock_en_consignacion.comercio_id', $this->comercioId)
                ->where('stock_en_consignacion.producto_id', $producto_id)
                ->select('stock_en_consignacion.*', DB::RAW("'' as tipo_comprobante"), DB::RAW("'' as num_comprobante"))->get();
            foreach ($this->stockHistorial as $i){
                if($i->remito_id != null){
                    $comprobante = StockEnConsignacion::join('remitos as r', 'r.id', 'stock_en_consignacion.remito_id')
                        ->where('r.id', $i->remito_id)
                        ->select('r.numero')->first();
                    $i->num_comprobante = $comprobante->numero;
                    $i->tipo_comprobante = 'REM';
                }else{
                    $comprobante = StockEnConsignacion::join('facturas as f', 'f.id', 'stock_en_consignacion.factura_id')
                    ->where('f.id', $i->factura_id)
                    ->select('f.numero')->first();
                    $i->num_comprobante = $comprobante->numero;
                    $i->tipo_comprobante = 'FAC';
                }
            }
        }else{        //si es un subproducto
            if($this->producto_id != null)
            {
                $producto = Subproducto::find($this->producto_id);
                $this->producto = $producto['descripcion'];
            }
            if($this->cliente_id != null)
            {
                $record = Cliente::find($this->cliente_id);
                $this->nombreCliente = $record['apellido'] . ' ' . $record['nombre'];
            }
            $this->stockHistorial = StockEnConsignacion::where('stock_en_consignacion.cliente_id', $cliente_id)
                ->where('stock_en_consignacion.comercio_id', $this->comercioId)
                ->where('stock_en_consignacion.subproducto_id', $producto_id)
                ->select('stock_en_consignacion.*', DB::RAW("'' as tipo_comprobante"), DB::RAW("'' as num_comprobante"))->get();
            foreach ($this->stockHistorial as $i){
                if($i->remito_id != null){
                    $comprobante = StockEnConsignacion::join('remitos as r', 'r.id', 'stock_en_consignacion.remito_id')
                        ->where('r.id', $i->remito_id)
                        ->select('r.numero')->first();
                    $i->num_comprobante = $comprobante->numero;
                    $i->tipo_comprobante = 'REM';
                }else{
                    $comprobante = StockEnConsignacion::join('facturas as f', 'f.id', 'stock_en_consignacion.factura_id')
                    ->where('f.id', $i->factura_id)
                    ->select('f.numero')->first();
                    $i->num_comprobante = $comprobante->numero;
                    $i->tipo_comprobante = 'FAC';
                }
            }
        }
        if($cliente_id != null) $this->emit('abrirModalHistorial');
    }
}
