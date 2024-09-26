<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Peps;
use App\Models\MovimientoDeStock;
use App\Models\Producto;
use App\Models\Stock;
use App\Models\StockEnConsignacion;
use DB;

class StockController extends Component
{
    public $selected_id, $search, $placeHolderSearch, $info;
    public $action, $stock, $stockHistorial, $producto_id, $producto, $cliente_id;
    public $nombreCliente, $cliente , $clientes, $infoCli;
    public $title, $valorTotalStock, $valorTotalStockPorConsignatario;
    public $valorTotalStockLocal, $valorTotalStockConsignacion;
    public $comercioId, $modConsignaciones, $comercioTipo;
    public $idProductoHistorial, $productoStock, $cantidad;
    public $infoHistorialStock, $e_i, $compras, $ventas, $data, $accion, $ventasTotal, $unidadDeMedida;
    public $modificacion_manual_directa, $modificacion_manual_indirecta, $ventasSinStock, $dataAccion;

    public function mount()
    {
        $this->placeHolderSearch = "'Buscar por 'Código' o por 'Descripción'";
        $this->action = 1;
        $this->cliente = 'Elegir';
        $this->title = "Stock";
        $this->infoHistorialStock = [];

		$this->comercioId = session('idComercio');
        $this->modConsignaciones = session('modConsignaciones');
        $this->comercioTipo = session('tipoComercio');

        if($this->comercioTipo == 11){ //consignación
            $this->clientes = Cliente::where('comercio_id', $this->comercioId)
                    ->where('consignatario', '1')->orderBy('apellido')->get();
        }else{
            $this->clientes = Cliente::join('stock_en_consignacion as s', 's.cliente_id', 'clientes.id')
                ->where('clientes.comercio_id', $this->comercioId)
                ->where('s.remito_id', '<>', null)
                ->groupBy('s.cliente_id')->select('clientes.*', 's.cliente_id')->orderBy('clientes.apellido')->get();
        }

    }

    public function render()
    {      
        if($this->comercioTipo == 11) $this->stockPorCliente($this->cliente); //consignación               

		if(strlen($this->search) && $this->action == 1){  //buscar >> stock local
            $this->buscarTotalStockLocal();
		}elseif($this->action == 1){   //stock local   
            $this->totalStockLocal();
		}elseif($this->action == 2){  //stock consignatario
            $this->totalStockConsignatario();
        }
        //busco el historial de movimientos de stock del producto seleccionado
		if($this->idProductoHistorial){
			$infoHistorial = Peps::join('movimiento_de_stock as mov', 'mov.id', 'peps.mov_stock_id')
                ->join('productos as p', 'p.id', 'peps.producto_id')
                ->where('peps.producto_id', $this->idProductoHistorial)
                ->select('mov.descripcion', 'peps.*', 'p.unidad_de_medida', 'p.presentacion')->get();
            foreach ($infoHistorial as $i) {
                if ($i->mov_stock_id == 1) $this->e_i += $i->cantidad;
                if ($i->mov_stock_id == 2) {
                    $cantidad_real = $i->cantidad * $i->presentacion;
                    $this->compras += $cantidad_real;
                }
                if ($i->mov_stock_id == 3) $this->ventas += $i->cantidad;
                if ($i->mov_stock_id == 4) $this->modificacion_manual_directa += $i->cantidad;
                if ($i->mov_stock_id == 5) $this->modificacion_manual_indirecta += $i->cantidad;
                if ($i->mov_stock_id == 8) $this->ventasSinStock += $i->cantidad;
                $this->unidadDeMedida = $i->unidad_de_medida;                
            }
            $this->ventasTotal = $this->ventas + $this->ventasSinStock;
            $this->cantidad = $this->e_i + $this->compras + $this->ventasTotal + $this->modificacion_manual_directa + $this->modificacion_manual_indirecta;
            $producto = Producto::find($this->idProductoHistorial);
            $this->productoStock = $producto->descripcion;
			$this->emit('abrirModalHistorialStock');
		} else $infoHistorial = [];

        if ($this->accion) {  //busco el detalle de los movimientos de stock del producto
            $this->infoHistorialStock = Peps::join('users as u', 'u.id', 'peps.user_id')
                ->where('peps.producto_id', $this->idProductoHistorial)
                ->where('peps.mov_stock_id', $this->accion)->select('peps.*', 'u.name', 'u.apellido',
                DB::RAW("'' as descProdModif"), DB::RAW("'' as accion"), DB::RAW("'' as user"))
                ->orderBy('peps.created_at', 'desc')->get();      
            if ($this->infoHistorialStock->count() > 0) {
                foreach ($this->infoHistorialStock as $i) {
                    if ($i->cantidad > 0) {
                        $i->accion = "Agrega";
                        $i->cantidad = $i->cantidad * 1;
                    } else {
                        $i->accion = "Quita";
                        $i->cantidad = $i->cantidad * -1;
                    }
                    if ($i->prod_modif_id) {
                        $producto = Producto::find($i->prod_modif_id);
                        $i->descProdModif = $producto->descripcion;
                        if ($i->cant_prod_modif > 0) {
                            $i->accion_prod_modif = "Agrega";
                            $i->cant_prod_modif = $i->cant_prod_modif * 1;
                        } else {
                            $i->accion_prod_modif = "Quita";
                            $i->cant_prod_modif = $i->cant_prod_modif * -1;
                        }    
                    } 
                    $i->user = $i->apellido . ", " . $i->name;
                }
            }
            $data = MovimientoDeStock::find($this->accion);
            $this->data = $data->descripcion;
            $this->accion = null;
            $this->emit('abrirModalDetalleHistorialStock');
        } else $this->infoHistorialStock = [];

        return view('livewire.stock.component', [
            'infoHistorial' => $infoHistorial
        ]);
    }
    public function totalStockLocal()
    {
        $this->info = Producto::select('productos.*', DB::RAW("0 as stock_local"), DB::RAW("'' as stock_en_consignacion"),
            DB::RAW("'' as stock_total"), DB::RAW("0 as subtotal"))
            ->where('comercio_id', $this->comercioId)
            ->where('estado', 'Disponible')
            ->orderBy('descripcion')->get();            
        foreach ($this->info as $i){
            $stock = Peps::where('producto_id', $i->id)->sum('resto');
            if($stock) $i->stock_local = $stock;
            else $i->stock_local = 0;
            
            $stock_en_consig = StockEnConsignacion::where('producto_id', $i->id)->sum('cantidad');  
            if($stock_en_consig) $i->stock_en_consignacion = $stock_en_consig;
            else $i->stock_en_consignacion = 0;

            $i->precio_costo = $i->precio_costo / $i->presentacion;

            $totalLocal = $i->stock_local * $i->precio_costo;
            $this->valorTotalStockLocal += $totalLocal;                                    

            $totalConsignacion = $i->stock_en_consignacion * $i->precio_costo;
            $this->valorTotalStockConsignacion += $totalConsignacion;

            $i->stock_total = $i->stock_local + $i->stock_en_consignacion;
            $i->subtotal = $i->stock_total * $i->precio_costo;
            $this->valorTotalStock += $i->subtotal;                
        }
        //if($this->comercioTipo == 11) { //consignacionas
            $this->verStockEnConsignacion($this->producto_id, null); 
            $this->verHistorialStockEnConsignacion($this->producto_id, $this->cliente_id, null); 
        //}
    }
    public function buscarTotalStockLocal()
    {
        $this->valorTotalStock = 0;
        $this->info = Producto::select('productos.*', DB::RAW("0 as stock_local"), DB::RAW("'' as stock_en_consignacion"),
            DB::RAW("'' as stock_total"), DB::RAW("0 as subtotal"), DB::RAW("'' as producto"))
            ->where('codigo', $this->search)
            ->where('comercio_id', $this->comercioId)
            ->where('estado', 'Disponible')
            ->orWhere('descripcion', 'like', '%' . $this->search .'%')
            ->where('comercio_id', $this->comercioId)
            ->where('estado', 'Disponible')
            ->orderBy('descripcion')->get();
        foreach ($this->info as $i){
            $stock = Peps::where('producto_id', $i->id)->sum('resto');
            if($stock) $i->stock_local = $stock;
            else $i->stock_local = 0;
            
            $stock_en_consig = StockEnConsignacion::where('producto_id', $i->id)->get()->sum('cantidad');  
            if($stock_en_consig) $i->stock_en_consignacion = $stock_en_consig;
            else $i->stock_en_consignacion = 0;  

            $i->stock_total = $i->stock_local + $i->stock_en_consignacion;
            $i->subtotal = ($i->precio_costo / $i->presentacion) * $i->stock_total;
            $this->valorTotalStock += $i->subtotal; 
            $i->producto = 1;
        }
    }
    public function totalStockConsignatario()
    {
        $this->info = Producto::select('id', 'codigo', 'descripcion', DB::RAW("'' as stock_en_consignacion"), 
            DB::RAW("'' as stock_total"), DB::RAW("'' as producto"), DB::RAW("'' as descProducto"))
            ->where('comercio_id', $this->comercioId)
            ->where('estado', 'Disponible')
            ->orderBy('descripcion')->get();
    }
    public function resetInput()
    {
        $this->search                        = '';
        $this->producto_id                   = null;
        $this->producto                      = null;
        $this->cliente_id                    = null;
        $this->cliente                       = 'Elegir';
        $this->infoHistorialStock            = [];
        $this->dataAccion                    = null;
        $this->cantidad                      = 0;
        $this->e_i                           = 0;
        $this->compras                       = 0;
        $this->ventas                        = 0;
        $this->ventasSinStock                = 0;
        $this->modificacion_manual_directa   = 0;
        $this->modificacion_manual_indirecta = 0;
        $this->valorTotalStock               = 0;            
        $this->valorTotalStockLocal          = 0;
        $this->valorTotalStockConsignacion   = 0;
    }    
    protected $listeners = [
        'productoHistorial',
        'verHistorialStock'
    ];    
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
    public function stockPorCliente($id)
    {
        $this->valorTotalStockPorConsignatario = 0;
        $this->infoCli = StockEnConsignacion::join('clientes as c', 'c.id', 'stock_en_consignacion.cliente_id')
            ->where('stock_en_consignacion.cliente_id', $id)
            ->select('stock_en_consignacion.producto_id', DB::RAW("'' as articuloId"), DB::RAW("'' as articuloCodigo"), 
                     DB::RAW("'' as cantidad"),  DB::RAW("'' as articuloDesc"), DB::RAW("'' as precio_venta"), DB::RAW("0 as subtotal"))
            ->groupBy('stock_en_consignacion.producto_id')->get();
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
                    ->join('productos as p', 'p.id', 'stock_en_consignacion.producto_id')
                    ->where('stock_en_consignacion.cliente_id', $id)
                    ->select('sp.id', 'sp.descripcion', 'p.precio_venta_l2')->first();
                $i->articuloId = $stock_en_consig->id;
                $i->articuloCodigo = $stock_en_consig->id;
                $i->articuloDesc = $stock_en_consig->descripcion;
                $i->precio_venta_l2 = $stock_en_consig->precio_venta_l2;

                $stock_en_consig_cantidad = 0; //StockEnConsignacion::where('subproducto_id', $i->subproducto_id)
                
            }
            $i->cantidad = $stock_en_consig_cantidad;
            $i->subtotal = $i->cantidad * $i->precio_venta;
            $this->valorTotalStockPorConsignatario += $i->subtotal;
        }
        $this->producto_id=null;      
    }
    public function recargarPagina()
    {
        $this->resetInput();
    }
    public function verStockEnConsignacion($producto_id, $prod)
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
       
        if($this->producto_id != null) $this->emit('abrirModal');
    }
    public function verHistorialStockEnConsignacion($producto_id, $cliente_id, $prod)
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
    public function productoHistorial($idProducto)
	{
		$this->idProductoHistorial = $idProducto;
        $this->resetInput();
	}
    public function verHistorialStock($accion)
    {
        $this->accion = $accion;
        $this->dataAccion = $accion;
    }
}
