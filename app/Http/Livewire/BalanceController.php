<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Balance;
use App\Models\Compra;
use App\Models\Factura;
use App\Models\MovimientoDeCaja;
use App\Models\Producto;
use App\Models\Stock;
use App\Models\StockEnConsignacion;
use App\Models\Subproducto;
use DB;

class BalanceController extends Component
{
    public $valorTotalStock, $e_i, $compras, $e_f, $cmv, $cFijos, $cVariables, $action = 1;
    public $ventas, $p_cmv, $m_c, $p_m_c, $ventasPEq, $p_cF, $p_cV;
    public $p_alq, $p_emp, $p_serv, $p_imp, $p_gastos_func, $p_egresos_varios, $p_gan; 
    public $comercioId, $balanceId, $empleados, $servicios, $impuestos;
    public $alquileres, $gastosDeFuncionamiento, $egresosVarios, $ganancia, $selector;

    public function render()
    {
        //busca el comercio que está en sesión
		$this->comercioId = session('idComercio');
     
        $infoBalance = Balance::where('comercio_id', $this->comercioId)
            ->orderBy('existencia_inicial', 'desc')->select('*')->first();
        if($infoBalance){
            $this->balanceId = $infoBalance->id;
            $this->e_i = $infoBalance->existencia_inicial;
        }

        $this->compras();
        $this->stock_actual();
        $this->cmv = $this->e_i + $this->compras - $this->e_f;
        
        $this->ventas();
        $this->porcentajes();
        $this->egresos();
        $this->puntoDeEquilibrio();

        $this->m_c = $this->ventas - $this->cmv;
        if($this->ventas) $this->p_m_c = ($this->m_c * 100) / $this->ventas;

        if(!$this->selector) $this->selector = '1';
        if($this->selector== '1'){     //por producto
            $info = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
                ->where('productos.comercio_id', $this->comercioId)
                ->where('productos.tipo', 'not like', 'Art. Compra')
                ->select('productos.id', 'productos.descripcion', 'productos.precio_costo', 'productos.precio_venta_sug_l1',
                    'productos.precio_venta_l1', 'c.margen_1', DB::RAW("0 as margen_actual"),
                    DB::RAW("0 as diferencia_margen"))->orderBy('productos.descripcion')->get();
            if($info){
                $margen_actual = 0;
                foreach ($info as $i) {
                    if($i->precio_costo > 0) $margen_actual = 1 - ($i->precio_costo / $i->precio_venta_l1);
                    else $margen_actual = 0;
                    $i->margen_actual = $margen_actual * 100;
                    $i->margen_actual = round($i->margen_actual, 0);
                    if($i->margen_actual >= $i->margen_1) $i->diferencia_margen = '>=';
                    else $i->diferencia_margen = '<';
                }
            } 
        }elseif($this->selector == '2'){    //por categoría
            $info = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
                ->where('productos.comercio_id', $this->comercioId)
                ->where('productos.tipo', 'not like', 'Art. Compra')
                ->groupBy('productos.categoria_id', 'c.descripcion', 'c.margen_1')
                ->select('productos.categoria_id', 'c.descripcion', 'c.margen_1',
                    DB::RAW("0 as diferencia_margen"), DB::RAW("0 as promedio_por_categoria"))
                ->orderBy('c.descripcion')->get();
            if($info){
                $margen_real = 0;
                foreach ($info as $i) {
                    $data = Producto::where('categoria_id', $i->categoria_id)
                        ->where('productos.comercio_id', $this->comercioId)
                        ->where('productos.tipo', 'not like', 'Art. Compra')
                        ->select('productos.precio_costo', 'productos.precio_venta_l1', 
                            'productos.categoria_id')->get();
                    $cantidad = $data->count();
                    $margen_total = 0;
                    foreach ($data as $j) {
                        if($j->precio_costo > 0) $margen_actual = 1 - ($j->precio_costo / $j->precio_venta_l1);
                        else $margen_actual = 0;
                        $margen_actual = $margen_actual * 100;
                        $margen_total += $margen_actual;
                    }
                    $i->promedio_por_categoria = $margen_total / $data->count(); 
                    $i->promedio_por_categoria = round($i->promedio_por_categoria, 0);                       
                    if($i->promedio_por_categoria >= $i->margen_1) $i->diferencia_margen = '>=';
                    else $i->diferencia_margen = '<';                   
                }
            }
        }else{                             //por rubro
            $info = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
                ->join('rubros as r', 'r.id', 'c.rubro_id')
                ->where('productos.comercio_id', $this->comercioId)
                ->where('productos.tipo', 'not like', 'Art. Compra')
                ->groupBy('productos.categoria_id', 'c.margen_1', 'c.rubro_id')
                ->select('productos.categoria_id', 'c.margen_1', 'c.rubro_id',
                    DB::RAW("0 as diferencia_margen"), DB::RAW("0 as promedio_por_rubro"))->get();
            if($info){
                $margen_real = 0;
                foreach ($info as $i) {
                    $data = Producto::where('categoria_id', $i->categoria_id)
                        ->where('productos.comercio_id', $this->comercioId)
                        ->where('productos.tipo', 'not like', 'Art. Compra')
                        ->select('productos.precio_costo', 'productos.precio_venta_l1', 
                            'productos.categoria_id')->get();
                    $cantidad = $data->count();
                    $margen_total = 0;
                    foreach ($data as $j) {
                        $margen_actual = 1 - ($j->precio_costo / $j->precio_venta_l1);
                        $margen_actual = $margen_actual * 100;
                        $margen_total += $margen_actual;
                    }
                    $i->promedio_por_categoria = $margen_total / $data->count(); 
                    $i->promedio_por_categoria = round($i->promedio_por_categoria, 0);                       
                    if($i->promedio_por_categoria >= $i->margen_1) $i->diferencia_margen = '>=';
                    else $i->diferencia_margen = '<';                   
                }
            }

/////calcular el promedio de cada rubro a partir de los promedios de las categorías




            dd($info);
            foreach ($info as $i) {
                # code...
            }
        }
        return view('livewire.balance.component', [
            'info' => $info
        ]);
    }
    public function doAction($action)
    {
        $this->action = $action;
    }
    protected $listeners = [
        'actualizarPrecioLista' => 'actualizarPrecioLista'
    ];
    public function actualizarPrecioLista($info)
    {
        $data = json_decode($info);
        DB::begintransaction();                        
        try{ 
            $record = Producto::find($data->id);
            $record->update([
                'precio_venta_l1' => $data->precio
            ]);
            DB::commit();               
            $this->emit('actualizarPrecio');
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        return;
    }  
    public function stock_actual()
    {
        $this->e_f = 0;
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
                $i->stock_local = $stock->stock_actual;
               
                $stock_en_consig = StockEnConsignacion::where('producto_id', $i->id)->get()->sum('cantidad');  
                $i->stock_en_consignacion = $stock_en_consig;
            } 
            $i->stock_total = $i->stock_local + $i->stock_en_consignacion;
            $i->subtotal = $i->stock_total * $i->precio_costo;
            $this->e_f += $i->subtotal;
        }
    }
    public function compras()
    {
        $this->compras = Compra::where('comercio_id', $this->comercioId)->get()->sum('importe');
    }
    public function ventas()
    {
        $this->ventas = Factura::where('comercio_id', $this->comercioId)->get()->sum('importe');        
    }
    public function porcentajes()
    {
        if($this->ventas) $this->p_cmv = ($this->cmv * 100) / $this->ventas;
    }
    public function egresos()
    {
        $this->alquileres = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            ->join('categoria_gastos as cg', 'cg.id', 'g.categoria_id')
            ->where('cg.id', 1)  //alquileres
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->sum('movimiento_de_cajas.importe');
        $this->empleados = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            ->join('categoria_gastos as cg', 'cg.id', 'g.categoria_id')
            ->where('cg.id', 6)  //sueldos
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->orWhere('cg.id', 8)  //comisiones
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->orWhere('cg.id', 10) //sueldos extras
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->sum('movimiento_de_cajas.importe');
        $this->servicios = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            ->join('categoria_gastos as cg', 'cg.id', 'g.categoria_id')
            ->where('cg.id', 2)  //servicios
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->sum('movimiento_de_cajas.importe');
        $this->impuestos = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            ->join('categoria_gastos as cg', 'cg.id', 'g.categoria_id')
            ->where('cg.id', 5)  //impuestos
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->sum('movimiento_de_cajas.importe');
        $this->gastosDeFuncionamiento = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            ->join('categoria_gastos as cg', 'cg.id', 'g.categoria_id')
            ->where('cg.id', 3)  //seguros
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->orWhere('cg.id', 4)  //gastos administrativos
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->orWhere('cg.id', 11)  //honorarios
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->orWhere('cg.id', 13)  //combustibles
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->orWhere('cg.id', 15) //art. limpieza
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->sum('movimiento_de_cajas.importe');
        $this->egresosVarios = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            ->join('categoria_gastos as cg', 'cg.id', 'g.categoria_id')
            ->where('cg.id', 9)  //gastos de envío
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->orWhere('cg.id', 12) //publicidad
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->orWhere('cg.id', 14)  //colaboraciones
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->sum('movimiento_de_cajas.importe');
        if($this->ventas){
            $this->p_alq = ($this->alquileres * 100) / $this->ventas;
            $this->p_emp = ($this->empleados * 100) / $this->ventas;
            $this->p_serv = ($this->servicios * 100) / $this->ventas;
            $this->p_imp = ($this->impuestos * 100) / $this->ventas;
            $this->p_gastos_func = ($this->gastosDeFuncionamiento * 100) / $this->ventas;
            $this->p_egresos_varios = ($this->egresosVarios * 100) / $this->ventas;

            $this->ganancia = $this->ventas - $this->cmv - $this->alquileres - $this->servicios -
                              $this->impuestos - $this->gastosDeFuncionamiento - $this->egresosVarios;
            $this->p_gan = 100 - $this->p_cmv - $this->p_alq - $this->p_emp - $this->p_serv - $this->p_imp - 
                           $this->p_gastos_func - $this->p_egresos_varios;
        } 
    }
    public function puntoDeEquilibrio()
    {
        $this->cFijos = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            ->join('categoria_gastos as cg', 'cg.id', 'g.categoria_id')
            ->where('cg.tipo', '1')  //costos fijos
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->sum('movimiento_de_cajas.importe');
        $this->cVariables = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            ->join('categoria_gastos as cg', 'cg.id', 'g.categoria_id')
            ->where('cg.tipo', '2')  //costos variables
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->sum('movimiento_de_cajas.importe');
        $this->ventasPEq = $this->ventas - $this->cFijos - $this->cVariables;
    }
    public function grabarEI()
    {  
        DB::begintransaction();
        try{ 
            $record = Balance::create([
                'existencia_inicial'  => $this->e_f,
                'comercio_id'   => $this->comercioId
            ]);
            session()->flash('msg-ok', 'Balance iniciado exitosamente!!!');
            DB::commit();               
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se creó...');
        }
    }
}
