<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\StockEnConsignacion;
use DB;

class StockController extends Component
{
    public $comercioId, $selected_id, $search = '', $placeHolderSearch = "Buscar por 'Código de producto' o por descripción";
    public $action = 1, $stock, $stockHistorial, $producto_id=null, $producto=null, $cliente_id=null;
    public $nombreCliente, $valorizar = false, $totalStock = 10;

    public function render()
    {
        //busca el comercio que está en sesión
		$this->comercioId = session('idComercio');

		if(strlen($this->search) && $this->action == 1) {
			$info = Producto::select('id', 'codigo', 'descripcion', 'stock',  DB::RAW("'' as stock_en_consignacion"), 
                DB::RAW("'' as stock_total"))
				->where('codigo', $this->search)
				->where('comercio_id', $this->comercioId)
				->orWhere('descripcion', 'like', '%' . $this->search .'%')
				->where('comercio_id', $this->comercioId)
				->orderBy('descripcion')
				->get();
            foreach ($info as $i){
                $stock_en_consig = StockEnConsignacion::select('cantidad')
                    ->where('producto_id', $i->id)->get();
                if($stock_en_consig->count()){
                    $stock_en_consig = StockEnConsignacion::where('producto_id', $i->id)
                        ->get()->sum('cantidad');
                }else $stock_en_consig = null;
                $i->stock_en_consignacion = $stock_en_consig;
                $i->stock_total = $i->stock + $stock_en_consig;
            }
            $this->producto_id=null;
		}elseif(strlen($this->search) && $this->action == 2) {
			$info = StockEnConsignacion::join('productos as p', 'p.id', 'stock_en_consignacion.producto_id')
                ->join('clientes as c', 'c.id', 'stock_en_consignacion.cliente_id')
				->where('c.id', $this->search)
				->where('stock_en_consignacion.comercio_id', $this->comercioId)
				->orWhere('c.apellido', 'like', '%' . $this->search .'%')
				->where('stock_en_consignacion.comercio_id', $this->comercioId)
				->orWhere('c.nombre', 'like', '%' . $this->search .'%')
				->where('stock_en_consignacion.comercio_id', $this->comercioId)
                ->select('p.id', 'p.codigo','p.descripcion', 'p.precio_venta_l2', 'c.id as clienteId', DB::RAW("'' as cantidad"))
                ->groupBy('p.id', 'p.codigo','p.descripcion', 'p.precio_venta_l2', 'c.id')
				->get();   
            foreach ($info as $i){
                $stock_en_consig = StockEnConsignacion::join('clientes as c', 'c.id', 'stock_en_consignacion.cliente_id')
                    ->where('stock_en_consignacion.producto_id', $i->id)
                    ->where('stock_en_consignacion.cliente_id', $i->clienteId)
                    ->get()->sum('cantidad'); 
                $i->cantidad = $stock_en_consig;
            }
            $this->producto_id=null;
		}elseif($this->action == 1){
            $info = Producto::select('id', 'codigo', 'descripcion', 'stock', DB::RAW("'' as stock_en_consignacion"), 
            DB::RAW("'' as stock_total"))
				->where('comercio_id', $this->comercioId)
				->orderBy('descripcion')
				->get();
            foreach ($info as $i){
                $stock_en_consig = StockEnConsignacion::select('cantidad')
                    ->where('producto_id', $i->id)->get();
                if($stock_en_consig->count()){
                    $stock_en_consig = StockEnConsignacion::where('producto_id', $i->id)
                        ->get()->sum('cantidad');
                }else $stock_en_consig = null;
                $i->stock_en_consignacion = $stock_en_consig;
                $i->stock_total = $i->stock + $stock_en_consig;
            } 
            $this->verStockEnConsignacion($this->producto_id);
            $this->verHistorialStockEnConsignacion($this->producto_id, $this->cliente_id);
		}elseif($this->action == 2){
            $info = Producto::select('id', 'codigo', 'descripcion', 'stock', DB::RAW("'' as stock_en_consignacion"), 
            DB::RAW("'' as stock_total"))
				->where('comercio_id', $this->comercioId)
				->orderBy('descripcion')
				->get();
            foreach ($info as $i){
                $stock_en_consig = StockEnConsignacion::select('cantidad')
                    ->where('producto_id', $i->id)->get();
                if($stock_en_consig->count()){
                    $stock_en_consig = StockEnConsignacion::where('producto_id', $i->id)
                        ->get()->sum('cantidad');
                }else $stock_en_consig = null;
                $i->stock_en_consignacion = $stock_en_consig;
                $i->stock_total = $i->stock + $stock_en_consig;
            } 
            $this->verStockEnConsignacion($this->producto_id);
            $this->verHistorialStockEnConsignacion($this->producto_id, $this->cliente_id);
        }
        return view('livewire.stock.component', [
            'info' => $info
        ]);
    }
    public function doAction($action)
    {
        $this->action = $action;
        if($action == 1) $this->placeHolderSearch = "Buscar por código de producto o por descripción";
        else $this->placeHolderSearch = "Buscar por código de cliente, por nombre o por apellido";
        $this->resetInput();
    }
    public function resetInput()
    {
        $this->search      = '';
        $this->producto_id = null;
        $this->producto    = null;
        $this->cliente_id  = null;
    }
    public function valorizar()
    {
        if($this->valorizar) $this->valorizar = false;
        else $this->valorizar = true;
    }
    public function recargarPagina()
    {
        $this->resetInput();
    }
    public function verStockEnConsignacion($producto_id)
    {
        $this->producto_id = $producto_id;
        if($this->producto_id != null)
        {
            $producto = Producto::find($this->producto_id);
            $this->producto = $producto['descripcion'];
        }
        $this->stock = StockEnConsignacion::join('productos as p', 'p.id', 'stock_en_consignacion.producto_id')
            ->join('clientes as c', 'c.id', 'stock_en_consignacion.cliente_id')
            ->where('stock_en_consignacion.comercio_id', $this->comercioId)
            ->where('stock_en_consignacion.producto_id', $producto_id)
            ->select('c.nombre','c.apellido', 'p.descripcion', 'p.id', 'c.id as clienteId',DB::RAW("'' as cantidad"))
            ->groupBy('c.nombre','c.apellido', 'p.descripcion', 'p.id', 'c.id')->get();
        foreach ($this->stock as $i){
            $cantidad = StockEnConsignacion::join('clientes as c', 'c.id', 'stock_en_consignacion.cliente_id')
                ->where('producto_id', $i->id)
                ->where('c.id', $i->clienteId)
                ->get()->sum('cantidad');
            $i->cantidad = $cantidad;
        }
        if($this->producto_id != null) $this->emit('abrirModal');
    }
    public function verHistorialStockEnConsignacion($producto_id,$cliente_id)
    {
        $this->producto_id = $producto_id;
        $this->cliente_id = $cliente_id;
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
        if($cliente_id != null) $this->emit('abrirModalHistorial');
    }
}
