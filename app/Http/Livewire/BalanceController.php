<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Balance;
use App\Models\Categoria;
use App\Models\CategoriaGasto;
use App\Models\Comercio;
use App\Models\Compra;
use App\Models\CostoFijoEstimado;
use App\Models\Detfactura;
use App\Models\Factura;
use App\Models\MovimientoDeCaja;
use App\Models\Peps;
use App\Models\Producto;
use App\Models\Stock;
use App\Models\StockEnConsignacion;
use App\Models\Subproducto;
use DB;

class BalanceController extends Component
{
    public $valorTotalStock, $e_i, $compras, $e_f, $cmv, $cFijos, $cVariables, $action = 1;
    public $ventas, $p_cmv, $m_c, $p_m_c, $ventasPEq, $p_cF, $p_cV, $calculo;
    public $p_alq, $p_emp, $p_serv, $p_imp, $p_gastos_func, $p_egresos_varios, $p_gan, $p_g_operativos; 
    public $comercioId, $balanceId, $empleados, $servicios, $impuestos;
    public $alquileres, $gastosDeFuncionamiento, $ganancia, $selector;

    public $promedio_margen_l1, $promedio_margen_l2, $promedio_margen_deseado_1, $promedio_margen_deseado_2;
    public $margen1, $margen2;
    public $popularidad, $promedio_costo_potencial, $popularidad_media, $utilidad_marginal;

    public $categoria = 'Elegir', $categorias, $categoriaDesc, $productoId, $productoDesc;
    public $matriz_bcg, $margen, $cantidad_por_precio_venta, $cantidad_por_margen;
    public $total_cantidad_vendida_por_producto, $total_venta_por_producto;
    public $margen_promedio_ponderado_por_producto_en_pesos, $margen_promedio_ponderado_por_producto_en_porcentaje;
    public $cantidad_por_margen_promedio_ponderado_en_pesos;
    public $cantidad_vendida_por_categoria, $total_venta_por_categoria, $margen_deseado_por_categoria;
    public $margen_real_por_categoria, $participacion_en_ventas_por_categoria, $margen_real_ponderado_por_categoria;
    public $detalle_mpp_por_producto, $mix_ideal, $mix_ideal_corregido;
    public $detalle_mpp_por_categoria = [], $total_mpp_por_producto, $total_cantidad_vendida, $total_margen_por_producto;
    public $total_cantidad_por_venta, $cantidad_productos_por_categoria, $habilitar_botones = false;

    public $gastos_operativos, $total_det_g_oper, $ot_gastos, $ot_ingresos, $gastos_financieros, $r_p_t, $util_operativa, $util_bruta;
    public $suma_gastos_operativos, $total_impuestos, $util_neta_antes_impuestos, $util_neta;
    public $p_util_operativa, $resultado_por_tenencia;

    public $total_cf_estimado = 0, $total_a_cubrir_estimado, $punto_de_equilibrio_estimado;
    public $total_a_cubrir, $punto_de_equilibrio;
    public $selected_id, $desc_cf="prueba", $importe_cf=15;

    public $det_gastos_operativos = [], $det_empleados, $det_servicios, $det_alquileres, $det_gastosDeFuncionamiento, $det_ot_egresos, $det_impuestos;

    public $existencia_inicial = [];

    public function render()
    {        
        try {
            $this->comercioId = session('idComercio');
            $calculo = Comercio::find($this->comercioId);
            $this->calculo = $calculo->calcular_precio_de_venta;
        } catch (\Throwable $th) {
            return view('errors.509'); 
        } 

        $this->categorias = Categoria::where('tipo_id', 2)
                ->where('comercio_id', $this->comercioId)
                ->orWhere('tipo_id', 3)
                ->where('comercio_id', $this->comercioId)->get();

        $info_cf_estimado = CostoFijoEstimado::where('comercio_id', $this->comercioId)->get();
        $this->total_cf_estimado = 0;
        if($info_cf_estimado->count() > 0){
            foreach ($info_cf_estimado as $i) {
                $this->total_cf_estimado += $i->importe;
            }
        }

        $this->existenciaInicial();
        $this->compras();
        $this->resultadoPorTenencia();
        $this->stock_actual();

        $this->cmv = $this->e_i + $this->compras + $this->resultado_por_tenencia - $this->e_f;
     
        $this->ventas();
        $this->porcentajes();
        $this->egresos();
        //$this->detalle_egresos(19);
        

        $this->m_c = $this->ventas - $this->cmv;
        if($this->ventas){
            $this->p_m_c = ($this->m_c * 100) / $this->ventas;
            $this->util_operativa = $this->m_c - $this->suma_gastos_operativos;
            $this->p_util_operativa = ($this->util_operativa * 100) / $this->ventas;
        }

        $this->util_neta_antes_impuestos = $this->util_operativa - $this->gastos_financieros - 
            $this->ot_gastos + $this->ot_ingresos;

//   ->where('productos.tipo', 'not like', 'Art. Compra')
//                 ->where('productos.tipo', 'not like', 'Art. Elaborado')

//Detfactura::onlyTrashed()->find(30)->restore();
        if(!$this->selector) $this->selector = '1';
        if($this->selector == '1'){     //por producto
            $info = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
                ->join('rubros as r', 'r.id', 'c.rubro_id')
                ->join('comercios as com', 'com.id', 'productos.comercio_id')
                ->where('productos.comercio_id', $this->comercioId)
                ->where('c.tipo_id', 2)              
                ->where('productos.estado', 'Disponible')
                ->orWhere('productos.comercio_id', $this->comercioId)
                ->where('c.tipo_id', 3)              
                ->where('productos.estado', 'Disponible')
                ->select('productos.id', 'productos.descripcion', 'productos.precio_costo', 
                    'productos.precio_venta_l1',  'productos.precio_venta_l2',
                    'productos.precio_venta_sug_l1', 'productos.precio_venta_sug_l2',
                    'c.margen_1', 'c.margen_2', 'com.calcular_precio_de_venta', 
                    DB::RAW("0 as margen_actual_l1"),DB::RAW("0 as margen_actual_l2"),
                    DB::RAW("0 as diferencia_margen_1"), DB::RAW("0 as diferencia_margen_2"))->orderBy('productos.descripcion')->get();
          
            if($info->count() > 0){
                $suma_margen_actual_l1 = 0;
                $suma_margen_actual_l2 = 0;
                $suma_margen_1 = 0;
                $suma_margen_2 = 0;
                $contador = 0;
                foreach ($info as $i) { 
                    if($i->precio_venta_l1 > 0){  
                        if($this->calculo == 0){   //si agrego el margen al precio de costo del producto
                            //obtengo el margen actual del producto
                            if($i->precio_costo > 0) $margen_actual_l1 = $i->precio_venta_l1 / $i->precio_costo;
                            else $margen_actual_l1 = 0;
                            $i->margen_actual_l1 = $margen_actual_l1 * 100;
                            $i->margen_actual_l1 = round($i->margen_actual_l1, 2) - 100;

                            //obtengo el rango de 10%+ y 10%- sobre el margen requerido por la Categoría del producto
                            $margen1_mas_10 = $i->margen_1 * 1.1;
                            $margen1_menos_10 = $i->margen_1 - ($i->margen_1 * 0.1); 

                            //hago las comparaciones
                            if($i->margen_actual_l1 >= $i->margen_1){
                                if($i->margen_actual_l1 > $margen1_mas_10) $i->diferencia_margen_1 = '>>';
                                else $i->diferencia_margen_1 = '>=';
                            }elseif($i->margen_actual_l1 < $i->margen_1){
                                if($i->margen_actual_l1 < $margen1_menos_10) $i->diferencia_margen_1 = '<<';
                                else $i->diferencia_margen_1 = '<';
                            }

                            if($i->precio_costo > 0) $margen_actual_l2 = $i->precio_venta_l2 / $i->precio_costo;
                            else $margen_actual_l2 = 0;
                            $i->margen_actual_l2 = $margen_actual_l2 * 100;
                            $i->margen_actual_l2 = round($i->margen_actual_l2, 2) - 100;

                            $margen2_mas_10 = $i->margen_2 * 1.1;
                            $margen2_menos_10 = $i->margen_2 - ($i->margen_2 * 0.1);

                            if($i->margen_actual_l2 >= $i->margen_2){
                                if($i->margen_actual_l2 > $margen2_mas_10) $i->diferencia_margen_2 = '>>';
                                else $i->diferencia_margen_2 = '>=';
                            }elseif($i->margen_actual_l2 < $i->margen_2){
                                if($i->margen_actual_l2 < $margen2_menos_10) $i->diferencia_margen_2 = '<<';
                                else $i->diferencia_margen_2 = '<';
                            } 
                        }else{      //si obtengo el margen desde el precio de venta del producto
                            if($i->precio_costo > 0 ) $margen_actual_l1 = 1 - ($i->precio_costo / $i->precio_venta_l1);
                            else $margen_actual_l1 = 1;
                            $i->margen_actual_l1 = $margen_actual_l1 * 100;
                            $i->margen_actual_l1 = round($i->margen_actual_l1, 2);

                            $margen1_mas_10 = $i->margen_1 * 1.1;
                            $margen1_menos_10 = $i->margen_1 - ($i->margen_1 * 0.1);

                            //if($i->id == 79) dd($i->margen_actual_l1 , $i->margen_1);
                            if($i->margen_actual_l1 >= $i->margen_1){
                                if($i->margen_actual_l1 > $margen1_mas_10) $i->diferencia_margen_1 = '>>';
                                else $i->diferencia_margen_1 = '>=';
                            }elseif($i->margen_actual_l1 < $i->margen_1){
                                if($i->margen_actual_l1 < $margen1_menos_10) $i->diferencia_margen_1 = '<<';
                                else $i->diferencia_margen_1 = '<';
                            }   

                            if($i->precio_costo > 0) $margen_actual_l2 = 1 - ($i->precio_costo / $i->precio_venta_l2);
                            else $margen_actual_l2 = 1;
                            $i->margen_actual_l2 = $margen_actual_l2 * 100;
                            $i->margen_actual_l2 = round($i->margen_actual_l2, 2);

                            $margen2_mas_10 = $i->margen_2 * 1.1;
                            $margen2_menos_10 = $i->margen_2 - ($i->margen_2 * 0.1);

                            if($i->margen_actual_l2 >= $i->margen_2){
                                if($i->margen_actual_l2 > $margen2_mas_10) $i->diferencia_margen_2 = '>>';
                                else $i->diferencia_margen_2 = '>=';
                            }elseif($i->margen_actual_l2 < $i->margen_2){
                                if($i->margen_actual_l2 < $margen2_menos_10) $i->diferencia_margen_2 = '<<';
                                else $i->diferencia_margen_2 = '<';
                            } 
                        }
                    }
                    $suma_margen_actual_l1 += $i->margen_actual_l1;
                    $suma_margen_actual_l2 += $i->margen_actual_l2;
                    $suma_margen_1 += $i->margen_1;
                    $suma_margen_2 += $i->margen_2;
                    $contador += 1;
                }             
                $this->promedio_margen_l1 = $suma_margen_actual_l1 / $contador;
                $this->promedio_margen_l2 = $suma_margen_actual_l2 / $contador;
                $this->promedio_margen_deseado_1 = $suma_margen_1 / $contador;
                $this->promedio_margen_deseado_2 = $suma_margen_2 / $contador;
                if($this->promedio_margen_l1 >= $this->promedio_margen_deseado_1) $this->margen1 = true;
                else $this->margen1 = false; 
                if($this->promedio_margen_l2 >= $this->promedio_margen_deseado_2) $this->margen2 = true;
                else $this->margen2 = false; 
            }else{
                $this->promedio_margen_l1 = 0;
                $this->promedio_margen_l2 = 0;
                $this->promedio_margen_deseado_1 = 0;
                $this->promedio_margen_deseado_2 = 0;
                // if($this->promedio_margen_l1 >= $this->promedio_margen_deseado_1) $this->margen1 = true;
                // else $this->margen1 = false; 
                // if($this->promedio_margen_l2 >= $this->promedio_margen_deseado_2) $this->margen2 = true;
                // else $this->margen2 = false; 
            }
        }elseif($this->selector == '2'){    //por categoría
            $info = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
                ->where('productos.comercio_id', $this->comercioId)
                ->where('c.tipo_id', 2) 
                ->where('productos.estado', 'Disponible')
                ->orWhere('c.tipo_id', 3) 
                ->where('productos.estado', 'Disponible')
                ->groupBy('productos.categoria_id', 'c.descripcion', 'c.margen_1', 'c.margen_2')
                ->select('productos.categoria_id', 'c.descripcion', 'c.margen_1', 'c.margen_2',
                    DB::RAW("0 as diferencia_margen_1"), DB::RAW("0 as diferencia_margen_2"),
                    DB::RAW("0 as promedio_por_categoria"))
                ->orderBy('c.descripcion')->get();
            if($info){
                $margen_real = 0;
                foreach ($info as $i) {
                    $data = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
                        ->where('productos.categoria_id', $i->categoria_id)
                        ->where('productos.comercio_id', $this->comercioId)
                        ->where('c.tipo_id', 'not like', 1)
                        ->where('productos.estado', 'Disponible')
                        ->select('productos.precio_costo', 'productos.precio_venta_l1', 
                            'productos.precio_venta_l2', 'productos.categoria_id')->get();                     
                    $cantidadDeItems = $data->count();
                    $margen_total_l1 = 0;
                    $margen_total_l2 = 0;
                    foreach ($data as $j) {
                        if($j->precio_venta_l1 > 0 && $j->precio_venta_l2 > 0 && $j->precio_costo > 0){ 
                            if($j->precio_costo > 0){
                                $margen_actual_l1 = 1 - ($j->precio_costo / $j->precio_venta_l1);
                                $margen_actual_l2 = 1 - ($j->precio_costo / $j->precio_venta_l2);
                            }else {
                                $margen_actual_l1 = 0;
                                $margen_actual_l2 = 0;
                            } 
                            $margen_actual_l1 = $margen_actual_l1 * 100;
                            $margen_total_l1 += $margen_actual_l1;
                            $margen_actual_l2 = $margen_actual_l2 * 100;
                            $margen_total_l2 += $margen_actual_l2;
                        }
                    }
                    $i->promedio_por_categoria_l1 = $margen_total_l1 / $cantidadDeItems; 
                    $i->promedio_por_categoria_l1 = round($i->promedio_por_categoria_l1, 2);                       
                    if($i->promedio_por_categoria_l1 >= $i->margen_1) $i->diferencia_margen_1 = '>=';
                    else $i->diferencia_margen_1 = '<';                   
                    $i->promedio_por_categoria_l2 = $margen_total_l2 / $cantidadDeItems; 
                    $i->promedio_por_categoria_l2 = round($i->promedio_por_categoria_l2, 2);                       
                    if($i->promedio_por_categoria_l2 >= $i->margen_2) $i->diferencia_margen_2 = '>=';
                    else $i->diferencia_margen_2 = '<';                   
                }
            }
        }else{                             //por rubro
            $info = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
                ->join('rubros as r', 'r.id', 'c.rubro_id')
                ->where('productos.comercio_id', $this->comercioId)
                ->where('c.tipo_id', 'not like', 1)
                ->where('productos.estado', 'Disponible')
                ->groupBy('productos.categoria_id', 'c.margen_1', 'c.rubro_id')
                ->select('productos.categoria_id', 'c.margen_1', 'c.rubro_id',
                    DB::RAW("0 as diferencia_margen"), DB::RAW("0 as promedio_por_rubro"))->get();
            if($info){
                $margen_real = 0;
                foreach ($info as $i) {
                    $data = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
                        ->where('productos.categoria_id', $i->categoria_id)
                        ->where('productos.comercio_id', $this->comercioId)
                        ->where('c.tipo_id', 'not like', 1)
                        ->where('productos.estado', 'Disponible')
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
        
        //$this->calcularTotalACubrirEstimado(0,0);
        $this->puntoDeEquilibrio();

        if($this->categoria != 'Elegir'){
            $this->habilitar_botones = true;
            $this->calcularMargenPopularidad();
        }else $this->resetInput();

        if($this->categoria != 'Elegir'){
            $this->habilitar_botones = true; 
            $this->detalleMppPorCategoria();
        }else $this->resetInput();

        $this->matrizBCG();

        return view('livewire.balance.component', [
            'info'             => $info,
            'info_cf_estimado' => $info_cf_estimado
        ]);
    }
    public function doAction($action, $id)
    {
        if($id > 0){
            $producto = Producto::find($id);
            $this->productoId = $producto->id; 
            $this->productoDesc = $producto->descripcion;
            $this->detalleMppPorProducto();  
        }else $this->producto = '';        
        $this->action = $action;
    }
    protected $listeners = [
        'actualizarPrecioLista'       => 'actualizarPrecioLista',
        'calcularTotalACubrir'        => 'calcularTotalACubrir',
        'doAction'                    => 'doAction',
        'guardarCostosFijosEstimados' => 'guardarCostosFijosEstimados',
        'eliminarCostoFijoEstimado'   => 'eliminarCostoFijoEstimado',
        'calcularTotalACubrirEstimado'=> 'calcularTotalACubrirEstimado',
        'detalle_egresos'             => 'detalle_egresos'
    ];
    protected function resetInput()
    {
        $this->mix_ideal_corregido       = 0;
        $this->total_mpp_por_producto    = 0;
        $this->detalle_mpp_por_categoria = [];
        $this->habilitar_botones         = false;
    }
    public function existenciaInicial()
    {
        $this->e_i = 0;
        $infoBalance = Balance::where('comercio_id', $this->comercioId)
            ->orderBy('existencia_inicial', 'desc')->first();
        if($infoBalance){
            $this->balanceId = $infoBalance->id;
            $this->e_i = $infoBalance->existencia_inicial;
        }else {
            $e_ini = Peps::where('mov_stock_id', 1)
                    ->where('comercio_id', $this->comercioId)
                    ->select('cantidad', 'costo_historico')->get();
            if($e_ini->count() > 0){
                foreach ($e_ini as $i) {
                    $this->e_i += $i->cantidad * $i->costo_historico;
                }
            }             
        }
    }
    public function calcularMargenPopularidad()
    {
        $this->mix_ideal = 0;
        $this->mix_ideal_corregido = 0;
        $this->cantidad_productos_por_categoria = 0;
        $mix_ideal = Producto::where('categoria_id', $this->categoria)
            ->where('productos.estado', 'Disponible')->get();
        if($mix_ideal->count() > 0){
            $this->cantidad_productos_por_categoria = $mix_ideal->count();
            $this->mix_ideal = 100 / $mix_ideal->count();
            $this->mix_ideal_corregido = $this->mix_ideal * 0.7;
        }   
        $categoriaDesc = Categoria::find($this->categoria);
        $this->categoriaDesc = $categoriaDesc->descripcion;       
    }
    public function detalleMppPorCategoria() 
    {
        $this->total_mpp_por_producto = 0;
        $this->total_venta_por_categoria = 0;
        $this->total_cantidad_vendida = 0;
        //busco los productos que pertenezcan a la categoría
        $this->detalle_mpp_por_categoria = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
            ->where('productos.categoria_id', $this->categoria)
            ->where('productos.estado', 'Disponible')
            ->select('productos.id', 'productos.descripcion', 'c.margen_1',
                    DB::RAW('0 as cantidad_vendida'), DB::RAW('0 as mpp_en_pesos'),
                    DB::RAW('0 as cantidad_por_mpp_en_pesos'), DB::RAW('0 as total_venta_por_producto'),
                    DB::RAW('0 as margen_real_por_producto'), DB::RAW('0 as participacion_en_ventas_por_cantidad'), 
                    DB::RAW('0 as participacion_en_ventas_por_importe'), DB::RAW('0 as mpp_por_producto'),
                    DB::RAW('0 as popularidad'), DB::RAW('0 as alto_margen'), DB::RAW('"" as clasificacion'))->get();            
        //calculo el total de venta de la categoría sumando los detalles de factura en los que figuren
        //los productos antes encontrados
        if($this->detalle_mpp_por_categoria->count() > 0){
            foreach ($this->detalle_mpp_por_categoria as $i) {                    
                $detalle_mpp_por_producto = Detfactura::where('producto_id', $i->id)
                    ->select('cantidad', 'precio')->get();
                $total_cantidad_vendida = 0;
                $suma_cantidad_por_venta = 0;
                if($detalle_mpp_por_producto->count() > 0){
                    foreach ($detalle_mpp_por_producto as $j) {
                        $total_cantidad_vendida += $j->cantidad;
                        $cantidad_por_precio = $j->cantidad * $j->precio;
                        $suma_cantidad_por_venta += $cantidad_por_precio;
                    }
                }
                $this->total_venta_por_categoria += $suma_cantidad_por_venta;
                $this->total_cantidad_vendida += $total_cantidad_vendida;
            }
        }
        //calculo el total_mpp_por_producto para luego compararlo con los mpp de cada producto y 
        //determinar si el margen es alto o bajo
        $margen_1 = 0;
        if($this->detalle_mpp_por_categoria->count() > 0){
            $margen_1 = $this->detalle_mpp_por_categoria[0]->margen_1;
            foreach ($this->detalle_mpp_por_categoria as $i) { 
                $detalle_mpp_por_producto = Detfactura::where('producto_id', $i->id)
                    ->select('cantidad', 'precio', 'costo')->get();
                if($detalle_mpp_por_producto->count() > 0){   
                    $suma_cantidad_por_venta = 0;
                    $suma_cantidad_por_margen = 0;
                    foreach ($detalle_mpp_por_producto as $j) {
                        $margen = 0;
                        $cantidad_por_precio = 0;
                        $cantidad_por_margen = 0;

                        $margen = $j->precio - $j->costo;
                        $cantidad_por_precio = $j->cantidad * $j->precio;
                        $cantidad_por_margen = $j->cantidad * $margen;                         

                        $suma_cantidad_por_venta += $cantidad_por_precio;
                        $suma_cantidad_por_margen += $cantidad_por_margen;
                    }                   
                    $i->margen_real_por_producto = ($suma_cantidad_por_margen / $suma_cantidad_por_venta) * 100;
                    $i->total_venta_por_producto = $suma_cantidad_por_venta;
                    $i->participacion_en_ventas_por_importe = ($i->total_venta_por_producto / $this->total_venta_por_categoria) * 100;
                    $i->mpp_por_producto = ($i->margen_real_por_producto / 100) * ($i->participacion_en_ventas_por_importe / 100) * 100;
                }else{
                    $i->mpp_por_producto = 0;
                }
                $this->total_mpp_por_producto += $i->mpp_por_producto;
            }
        }
        //////si tomo el 100% del margen ideal para el mpp de la categoria
        //$this->total_mpp_por_producto = $margen_1;

        //////si tomo el 95% del margen ideal como base para el mpp de la categoria
        //$this->total_mpp_por_producto = $margen_1 * 0.95;

        //////si tomo el 100% calculado como base para el mpp de la categoria
        $this->total_mpp_por_producto = round($this->total_mpp_por_producto,2);

        //////si tomo el 95% del calculado como base para el mpp de la categoria
        //$this->total_mpp_por_producto = $this->total_mpp_por_producto * 0.95;

        if($this->detalle_mpp_por_categoria->count() > 0){
            $contador = 0;
            foreach ($this->detalle_mpp_por_categoria as $i) { 
                $contador ++;                   
                $detalle_mpp_por_producto = Detfactura::where('producto_id', $i->id)
                    ->select('cantidad', 'precio', 'costo')->get();
                if($detalle_mpp_por_producto->count() > 0){                    
                    $suma_cantidad = 0;
                    $suma_cantidad_por_venta = 0;
                    $suma_cantidad_por_margen = 0;
                    $total_cantidad_vendida_por_producto = 0;
                    $total_margen_por_producto = 0;
                    foreach ($detalle_mpp_por_producto as $j) {
                        $margen = 0;
                        $cantidad_por_precio = 0;
                        $cantidad_por_margen = 0;

                        $margen = $j->precio - $j->costo;
                        $cantidad_por_precio = $j->cantidad * $j->precio;
                        $cantidad_por_margen = $j->cantidad * $margen;                         

                        $suma_cantidad += $j->cantidad;
                        $suma_cantidad_por_venta += $cantidad_por_precio;
                        $suma_cantidad_por_margen += $cantidad_por_margen;
                    }
                    $i->cantidad_vendida = $suma_cantidad;                    
                    $i->margen_real_por_producto = round(($suma_cantidad_por_margen / $suma_cantidad_por_venta) * 100,2);
                    $total_cantidad_vendida_por_producto = $suma_cantidad;
                    $total_margen_por_producto = $suma_cantidad_por_margen;
                    $i->mpp_en_pesos = $total_margen_por_producto / $total_cantidad_vendida_por_producto;
                    $i->cantidad_por_mpp_en_pesos = $i->cantidad_vendida * $i->mpp_en_pesos;
                    $i->total_venta_por_producto = $suma_cantidad_por_venta;
                    $i->participacion_en_ventas_por_cantidad = ($i->cantidad_vendida / $this->total_cantidad_vendida) * 100;
                    $i->participacion_en_ventas_por_importe = ($i->total_venta_por_producto / $this->total_venta_por_categoria) * 100;
                    $i->mpp_por_producto = ($i->margen_real_por_producto / 100) * ($i->participacion_en_ventas_por_importe / 100) * 100;
                }else{
                    $i->margen_real_por_producto = 0;
                    $i->mpp_en_pesos = 0;
                    $i->cantidad_por_mpp_en_pesos = 0;
                    $i->total_venta_por_producto = 0;
                    $i->participacion_en_ventas_por_cantidad = 0;
                    $i->participacion_en_ventas_por_importe = 0;
                    $i->mpp_por_producto = 0;
                }

                //MATRIZ BCG
                if ($i->participacion_en_ventas_por_cantidad >= $this->mix_ideal_corregido) $i->popularidad = true;
                else $i->popularidad = false;
              
                if ($i->margen_real_por_producto >= $this->total_mpp_por_producto) $i->alto_margen = true;
                else $i->alto_margen = false;

                if ($i->popularidad === true && $i->alto_margen === true){
                    $i->clasificacion = 'ESTRELLA';
                } elseif ($i->popularidad === true && $i->alto_margen === false) {
                    $i->clasificacion = 'POPULAR';
                } elseif ($i->popularidad ===false && $i->alto_margen === true) {
                    $i->clasificacion = 'IMPOPULAR';
                } else{
                    $i->clasificacion = 'PERDEDOR';
                }
            } 
        }
    }
    public function detalleMppPorProducto() 
    {
        $this->total_cantidad_vendida_por_producto = 0;
        $this->total_cantidad_por_venta = 0;
        $this->total_margen_por_producto = 0;
        $this->margen_promedio_ponderado_por_producto_en_pesos = 0;
        $this->margen_promedio_ponderado_por_producto_en_porcentaje = 0;

        $this->detalle_mpp_por_producto = Detfactura::where('producto_id', $this->productoId)->select('created_at', 
            'cantidad', 'precio', 'costo', DB::RAW('"" as fecha'), DB::RAW('0 as margen'), 
            DB::RAW('0 as cantidad_por_precio'), DB::RAW('0 as cantidad_por_margen'))->get();
        $suma_cantidad = 0;
        $suma_cantidad_por_venta = 0;
        $suma_cantidad_por_margen = 0;
        if($this->detalle_mpp_por_producto->count() > 0){
            foreach ($this->detalle_mpp_por_producto as $i) {
                $i->fecha = date_format($i->created_at, "d-m-Y");
                $i->margen = $i->precio - $i->costo;
                $i->cantidad_por_precio = $i->cantidad * $i->precio;
                $i->cantidad_por_margen = $i->cantidad * $i->margen;
                $suma_cantidad += $i->cantidad;
                $suma_cantidad_por_venta += $i->cantidad_por_precio;
                $suma_cantidad_por_margen += $i->cantidad_por_margen;
            }
            $this->total_cantidad_vendida_por_producto = $suma_cantidad;
            $this->total_cantidad_por_venta = $suma_cantidad_por_venta;
            $this->total_margen_por_producto = $suma_cantidad_por_margen;
            $this->margen_promedio_ponderado_por_producto_en_pesos = $this->total_margen_por_producto / $this->total_cantidad_vendida_por_producto;
            $this->margen_promedio_ponderado_por_producto_en_porcentaje = ($this->total_margen_por_producto / $suma_cantidad_por_venta) * 100;
        }
       
    }
    public function matrizBCG() 
    {
        $this->matriz_bcg = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
            ->join('rubros as r', 'r.id', 'c.rubro_id')
            ->where('productos.comercio_id', $this->comercioId)
            ->where('productos.categoria_id', $this->categoria)
            ->where('c.tipo_id', 'not like', 1)
            ->where('productos.estado', 'Disponible')  
            ->orWhere('productos.comercio_id', $this->comercioId)
            ->where('productos.categoria_id', $this->categoria)
            ->where('c.tipo_id', 'not like', 4)
            ->where('productos.estado', 'Disponible')  
            ->orderBy('productos.descripcion')
            ->select('productos.id', 'productos.descripcion', 'productos.categoria_id', 
                DB::RAW('"" as fecha'), DB::RAW('0 as cantidad'), DB::RAW('0 as precio'),  
                DB::RAW('0 as costo'),DB::RAW('0 as margen'), DB::RAW('0 as cantidad_por_precio'), 
                DB::RAW('0 as cantidad_por_margen'))->get();
            
        foreach ($this->matriz_bcg as $i) {
            $detalle = Detfactura::where('producto_id', $i->id)->select('created_at', 
            'cantidad', 'precio', 'costo')->get();
        
            if($detalle->count() > 0){
                foreach ($detalle as $j) {
                    $i->fecha = date_format($j->created_at, "d-m-Y");
                    $i->cantidad = $j->cantidad;
                    $i->precio = $j->precio;
                    $i->costo = $j->costo;
                    $i->margen = $j->precio - $j->costo;
                    $i->cantidad_por_precio = $j->cantidad * $j->precio;
                    $i->cantidad_por_margen = $j->cantidad * $i->margen;
                }
            }
            
        }
    }
    public function resultadoPorTenencia()
    {
        $rpt = Peps::join('productos as p', 'p.id', 'peps.producto_id')
                ->where('peps.comercio_id', $this->comercioId)
                ->where('peps.resto', '>', 0)
                ->select('peps.resto', 'peps.costo_historico', 'p.precio_costo')->get();
        $totalRPT = 0;
        if($rpt->count() > 0){
            foreach ($rpt as $i) {
                $diferenciaCeCostos = $i->precio_costo - $i->costo_historico;
                $resPorTenencia = $i->resto * $diferenciaCeCostos;
                $totalRPT += $resPorTenencia;
            }
        }
        $this->resultado_por_tenencia = $totalRPT;
    }
    public function actualizarPrecioLista($info)
    {
        $data = json_decode($info);
        DB::begintransaction();                        
        try{ 
            $record = Producto::find($data->id);
            $record->update([
                'precio_venta_l1' => $data->precio_l1,
                'precio_venta_l2' => $data->precio_l2
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
        $e_final = Peps::where('comercio_id', $this->comercioId)->select('resto', 'costo_historico')->get();
        //$existenciaInicial = 0;
        if($e_final->count() > 0){
            foreach ($e_final as $i) {
                $this->e_f += $i->resto * $i->costo_historico;
            }
        }
        //$this->e_i = $existenciaInicial; 
        // $info = Producto::select('productos.id', 'productos.precio_costo', DB::RAW("0 as stock_local"), 
        //     DB::RAW("'' as stock_en_consignacion"), DB::RAW("'' as stock_total"), DB::RAW("0 as subtotal"))
        //     ->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get(); 
        // if($info->count() > 0){           
        //     foreach ($info as $i){
        //         $stock = Stock::where('producto_id', $i->id)->first();
        //         if($stock) $i->stock_local = $stock->stock_actual;
        //         else $i->stock_local = 0;
                
        //         $stock_en_consig = StockEnConsignacion::where('producto_id', $i->id)->get()->sum('cantidad');  
        //         if($stock_en_consig) $i->stock_en_consignacion = $stock_en_consig;
        //         else $i->stock_en_consignacion = 0;
        
        //         $i->stock_total = $i->stock_local + $i->stock_en_consignacion;
        //         $i->subtotal = $i->stock_total * $i->precio_costo;
        //         $this->e_f += $i->subtotal;
        //     }
        // }
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
        $this->gastos_operativos = CategoriaGasto::where('comercio_id', $this->comercioId)
            ->where('tipo', 1)
            ->select('id', 'descripcion', DB::RAW("'0' as suma_importe"), DB::RAW("'0' as porcentaje"))
            ->get();
        $this->suma_gastos_operativos = 0;
        if($this->gastos_operativos->count() > 0){
            foreach ($this->gastos_operativos as $i) {
                $sumaImporte = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
                    ->join('categoria_gastos as cg', 'cg.id', 'g.categoria_id')
                    ->where('cg.id', $i->id)
                    ->sum('movimiento_de_cajas.importe');
                $i->suma_importe = $sumaImporte;
                if($this->ventas) $i->porcentaje = ($sumaImporte * 100) / $this->ventas;
                $this->suma_gastos_operativos += $sumaImporte;                
            }
        }
        if ($this->ventas > 0) {
            $this->p_g_operativos = ($this->suma_gastos_operativos * 100) / $this->ventas;
        }
        

        $this->alquileres = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            ->join('categoria_gastos as cg', 'cg.id', 'g.categoria_id')
            ->where('cg.id', 1)  //alquileres
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->sum('movimiento_de_cajas.importe');
        $this->empleados = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            ->join('categoria_gastos as cg', 'cg.id', 'g.categoria_id')
            ->where('cg.id', 25)  //sueldos
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->orWhere('cg.id', 8)  //comisiones
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->orWhere('cg.id', 10) //sueldos extras
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->sum('movimiento_de_cajas.importe');
        $this->servicios = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            ->join('categoria_gastos as cg', 'cg.id', 'g.categoria_id')
            ->where('cg.id', 26)  //servicios
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->sum('movimiento_de_cajas.importe');
        $this->impuestos = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            ->join('categoria_gastos as cg', 'cg.id', 'g.categoria_id')
            ->where('cg.id', 27)  //impuestos
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
        $this->ot_gastos = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            ->join('categoria_gastos as cg', 'cg.id', 'g.categoria_id')
            ->where('cg.id', 9)  //gastos de envío
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->orWhere('cg.id', 12) //publicidad
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->orWhere('cg.id', 14)  //colaboraciones
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->sum('movimiento_de_cajas.importe');
        //$this->gastos_operativos = $this->alquileres + $this->empleados + $this->servicios + $this->gastosDeFuncionamiento;
        
        
        //$this->gastos_financieros = 1500;
        
        if($this->ventas){
            $this->p_alq = ($this->alquileres * 100) / $this->ventas;
            $this->p_emp = ($this->empleados * 100) / $this->ventas;
            $this->p_serv = ($this->servicios * 100) / $this->ventas;
            $this->p_imp = ($this->impuestos * 100) / $this->ventas;
            $this->p_gastos_func = ($this->gastosDeFuncionamiento * 100) / $this->ventas;
            $this->p_egresos_varios = ($this->ot_gastos * 100) / $this->ventas;
             
            $this->ganancia = $this->ventas - $this->cmv - $this->alquileres - $this->servicios -
                              $this->impuestos - $this->gastosDeFuncionamiento - $this->ot_gastos;
            $this->p_gan = 100 - $this->p_cmv - $this->p_alq - $this->p_emp - $this->p_serv - $this->p_imp - 
                           $this->p_gastos_func - $this->p_egresos_varios;
        } 
    }
    public function detalle_egresos($id, $descripcion)
    {
        $this->det_gastos_operativos = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
            ->join('categoria_gastos as cg', 'cg.id', 'g.categoria_id')
            ->where('cg.id', $id) 
            ->where('movimiento_de_cajas.comercio_id', $this->comercioId)
            ->select('g.descripcion', 'movimiento_de_cajas.importe', DB::RAW("'0' as total"))
            ->get();
        $this->total_det_g_oper = 0;
        if ($this->det_gastos_operativos->count() > 0) {
            foreach ($this->det_gastos_operativos as $i) {
                $this->total_det_g_oper += $i->importe;
            }
        } 
        $this->emit('verDetalleGastosOperativos', $descripcion);
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
       //$this->calcularTotalACubrir(0,0);
    }
    public function guardarCostosFijosEstimados($info)
    {
        $data = json_decode($info);
        
        DB::begintransaction();                        
        try{ 
            if($data->id > 0){
                $record = CostoFijoEstimado::find($data->id);
                $record->update([
                    'descripcion' => $data->descripcion,
                    'importe'     => $data->importe
                ]);
            }else{
                $record = CostoFijoEstimado::create([
                    'descripcion' => $data->descripcion,
                    'importe'     => $data->importe,
                    'comercio_id' => $this->comercioId
                ]);
            }
            DB::commit();
            if($data->id > 0) $this->emit('agregarCF', 'modificado');
            else $this->emit('agregarCF', 'agregado');

        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        return;
    }
    public function eliminarCostoFijoEstimado($id)
    {
        DB::begintransaction();
        try{
            $record = CostoFijoEstimado::find($id)->delete();
            DB::commit();  
            $this->emit('registroEliminado');             
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
        }   
    }
    public function calcularTotalACubrir($deudas, $ganancia_deseada)
    {
        //dd($deudas, $ganancia_deseada);
        if(!$deudas) $deudas = 0;
        if(!$ganancia_deseada) $ganancia_deseada = 0;
        $this->total_a_cubrir = $this->cFijos + $deudas + $ganancia_deseada;
        $this->punto_de_equilibrio = ($this->total_a_cubrir * 100) / $this->promedio_margen_deseado_1;
    }
    public function calcularTotalACubrirEstimado($deudas, $ganancia_deseada)
    {
        //dd($deudas, $ganancia_deseada);
        if($this->promedio_margen_deseado_1 > 0){
            //dd($deudas, $ganancia_deseada);
            if(!$deudas) $deudas = 0;
            if(!$ganancia_deseada) $ganancia_deseada = 0;
            $this->total_a_cubrir_estimado = $this->total_cf_estimado + $deudas + $ganancia_deseada;
            $this->punto_de_equilibrio_estimado = ($this->total_a_cubrir_estimado * 100) / $this->promedio_margen_deseado_1;
        }
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
    public function anularVentas()
    {
        DB::begintransaction();                        
        try{ 
            $record = Compra::where('comercio_id', $this->comercioId)->get();
            
            foreach ($record as $i) {
                $i->delete();
            }
            DB::commit();
            $this->emit('hola');
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        return;

        
    }
}
