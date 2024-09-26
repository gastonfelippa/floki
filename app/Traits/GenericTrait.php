<?php

namespace App\Traits;

use App\Models\DetReceta;
use App\Models\Peps;
use App\Models\Producto;
use App\Models\Receta;
use App\Models\Stock;
use DB;

trait GenericTrait
{
    public $comercioId, $userId;
  
    public function actualizarStockTrait($accion, $agregarVenta, $inicioFactura, $detalleCompraId, 
        $detalleVentaId, $cantidad, $productoId, $costoHistorico, $stockActual)
    {
        DB::beginTransaction();
        try {
            $accion_1_status = true;
            $accion_2_status = true;
            $accion_3_status = true;
            $accion_4_status = true;
            $accion_5_status = true;
            $accion_6_status = true;
            $accion_7_status = true;
            $accion_8_status = true;
            //busca el comercio y el usuario que están en sesión
            $this->comercioId = session('idComercio');
            $this->userId = auth()->user()->id;
        
            //CUANDO SE ELABORA DE ANTEMANO un producto TIPO Art. Venta c/receta, o Art. Elaborado c/receta
            //se debe cargar el stock MANUALMENTE
            //de modo que al vender este producto se descuente stock del mismo directamente
            //si ese stock está en cero significa que ya no hay más de ese producto.
            //Ahora bien, supongamos que son platos del día, por más que no se elaboren todos de antemano,
            //debe cargarse el stock para saber cuántos se pueden vender.
            //Diferente es si el valor es nulo, en este caso descuenta stock de sus ingredientes.

            /////IMPORTANTE/////

            //El Stock Inicial solo se permite cargar al AGREGAR un Producto NUEVO y al hacerlo
            //éste será el Stock Actual.
    
            if ($accion == 1) { //Existencia Inicial
                DB::beginTransaction();
                try {
                    $peps = Peps::create([
                        'mov_stock_id'    => $accion,
                        'producto_id'     => $productoId,
                        'cantidad'        => $stockActual,
                        'resto'           => $stockActual,
                        'costo_historico' => $costoHistorico,
                        'user_id'         => $this->userId,
                        'comercio_id'     => $this->comercioId
                    ]);
                    DB::commit();
                } catch (Exception $th) {
                    DB::rollback();
                    $accion_1_status = false;
                } 
            } elseif ($accion == 2) {//Compra de mercadería

                $presentacion = Producto::where('id',$productoId)->select('id','presentacion')->first();
                //dd($presentacion);
                $cantidad_real = $cantidad * $presentacion->presentacion;

                //primero busco si tengo alguna 'venta sin stock' con 'resto < 0' para este producto
                //en cuyo caso descuento primero el o los restos de esos registros y lo que reste, así sea 0,
                //lo reflejo en el 'resto del registro de compra'
                DB::beginTransaction();
                try {
                    $sinStock = Peps::where('mov_stock_id', 8)
                        ->where('producto_id', $productoId)
                        ->where('resto', '<', 0)
                        ->where('comercio_id', $this->comercioId)->select('id', 'resto')->get();
                    $cantidad_a_descontar = $cantidad_real;
                    if ($sinStock->count() > 0) {
                        foreach ($sinStock as $i) {
                            $cantidad_a_descontar = $i->resto + $cantidad_a_descontar; 
                            if ($cantidad_a_descontar >= 0) $i->update(['resto' => 0]);
                            else {
                                $i->update(['resto' => $cantidad_a_descontar]);
                                $cantidad_a_descontar = 0;
                            }
                            if ($cantidad_a_descontar == 0) break;
                        }
                    }
                    $peps = Peps::create([
                        'mov_stock_id'    => $accion,
                        'det_compra_id'   => $detalleCompraId,
                        'producto_id'     => $productoId,
                        'cantidad'        => $cantidad,
                        'resto'           => $cantidad_a_descontar,
                        'costo_historico' => $costoHistorico,
                        'user_id'         => $this->userId,
                        'comercio_id'     => $this->comercioId
                    ]);
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollback();
                    $accion_2_status = false;  
                }
            } elseif ($accion == 3) {//Venta de mercadería
                //cabe aclarar que por acá solo pasarán las ventas que tengan stock, ya sea del producto o, en su caso,
                //de sus ingredientes, puesto que en caso contrario habrán sido derivadas a $accion == 8 (Venta sin stock)
                $agregar_venta_producto = true;
                $agregar_venta_ingrediente = true;
                $descontar_peps = true;
                //calculo merma del producto como tal, luego si sus ingredientes también tienen mermas, serán consideradas
                $producto = Producto::find($productoId); 
                if ($producto->merma > 0) $cantidad = $cantidad + (($cantidad * $producto->merma)/100);                
                DB::beginTransaction();
                try { 
                    if ($agregarVenta) { //si agrego una venta, debo descontar stock   
                        $record = Peps::where('producto_id', $productoId)->where('comercio_id', $this->comercioId)
                            ->where('resto', '>', 0)->select('id','resto')->orderBy('id')->get();
                        if ($record->count() > 0) { // si hay restos, descuento desde el producto
                            $agregar_venta_producto = $this->agregarVentaProducto($record, $productoId, $cantidad, $detalleVentaId, $accion);
                        } else {                    //sino, descuento desde sus ingredientes
                            $agregar_venta_ingrediente = $this->agregarVentaIngrediente($productoId, $cantidad, $detalleVentaId, $accion);                               
                        }
                    } else {            //si descuento una venta , debo agregar stock
                        //primero verifico si hay VentasSinStock para el producto o sus ingredientes en donde 'resto' sea menor a cero
                        $peps = $this->descontarVentaSinStock($productoId, $cantidad, $detalleVentaId); 
                        if($peps) {  //si no hay errores                   
                            //dd('pepseer: ' . $peps);
                            //si queda algo por descontar, SOLO será de un producto SIN receta, porque de lo
                            //contrario, debió descontarse completamente al llamar a $this->descontarVentaSinStock()
                            if ($peps > 0) $descontar_peps = $this->descontarVentaProducto($productoId, $peps, $detalleVentaId);
                        }
                    }
                    //dd($agregar_venta_producto , $agregar_venta_ingrediente , $descontar_peps);
                    if ($agregar_venta_producto && $agregar_venta_ingrediente && $descontar_peps) DB::commit();
                    else {
                        DB::rollback();
                        $accion_3_status = false;
                    }
                } catch (Exception $e) {
                    DB::rollback();
                    $accion_3_status = false;
                }        
            } elseif ($accion == 4) {//Modificación manual
                $stockManualStatus = true;
                DB::beginTransaction();
                try {
                    $tiene_receta = false;
                    $producto = Producto::find($productoId);  //averiguo si tiene receta
                    if ($producto->tiene_receta == 'si') $tiene_receta = true;

                    //averiguo el stock histórico del producto para compararlo con el $stockActual ingresado
                    $stockPeps = Peps::where('producto_id', $productoId)->where('comercio_id', $this->comercioId)->sum('resto');
                        
                    if ($stockPeps) {       
                        if ($stockActual) $diferenciaDeStock = $stockActual - $stockPeps; 
                        else $diferenciaDeStock = $stockPeps * -1;    
                    } else $diferenciaDeStock = $stockActual;
               
                    //el valor de $diferenciaDeStock me dirá si agregué o desconté stock
 
                    if ($stockActual === null) {//si debo "anular" el stock, corresponde "anular" todos los restos del producto
                        $record = Peps::where('producto_id', $productoId)
                            ->where('comercio_id', $this->comercioId)->select('id', 'resto')->get();

                        if ($record->count() > 0) foreach ($record as $i) $i->update(['resto' => null]);

                        //creo el movimiento
                        $peps = Peps::create([
                            'mov_stock_id'    => 4, // Modificación Manual Directa
                            'producto_id'     => $productoId,
                            'cantidad'        => $diferenciaDeStock,
                            'user_id'         => $this->userId,
                            'comercio_id'     => $this->comercioId
                        ]);

                        if ($tiene_receta) {//en este caso además debo agregar el stock que estoy anulando a sus ingredientes
                            $stockManualStatus = $this->agregarStockIngredienteManual($productoId, $diferenciaDeStock);
                        }
                    } elseif ($stockActual == 0) { //si debo dejar stock cero, dejo en cero a todos los "restos"
                        $record = Peps::where('producto_id', $productoId)
                            ->where('comercio_id', $this->comercioId)
                            ->where('mov_stock_id', 1)
                            ->orWhere('producto_id', $productoId)
                            ->where('comercio_id', $this->comercioId)
                            ->where('mov_stock_id', 2)
                            ->orWhere('producto_id', $productoId)
                            ->where('comercio_id', $this->comercioId)
                            ->where('mov_stock_id', 8)
                            ->select('id', 'resto')->get();
                        if ($record->count() > 0) foreach ($record as $i) $i->update(['resto' => 0]);
                        //creo el movimiento
                        $peps = Peps::create([
                            'mov_stock_id'    => 4, // Modificación Manual Directa
                            'producto_id'     => $productoId,
                            'cantidad'        => $diferenciaDeStock,
                            'user_id'         => $this->userId,
                            'comercio_id'     => $this->comercioId
                        ]);
                        
                        //teniendo en cuenta que siempre que deje en cero un stock_actual de un Art. C/receta
                        //será que éste anteriormente tenía stock positivo, ya que nunca podrá tener stock negativo
                        //porque el sistema no lo permite para este tipo de artículo, 
                        //es por ello que debo agregar a sus ingredientes el stock que estoy dejando en cero 
                        if ($tiene_receta) {
                            $stockManualStatus = $this->agregarStockIngredienteManual($productoId, $diferenciaDeStock);
                        }
                    } else {  //sino, veré si estoy agregando o quitando stock
                        if ($diferenciaDeStock > 0) { //si agrego stock 
                            $stockManualStatus = $this->agregarStockProductoManual($productoId, $diferenciaDeStock, $costoHistorico);
                            if ($tiene_receta) $stockManualStatus = $this->descontarStockIngredienteManual($productoId, $diferenciaDeStock, $costoHistorico);
                        } else {                    //si descuento stock
                            $stockManualStatus = $this->descontarStockProductoManual($productoId, $diferenciaDeStock);
                            if ($tiene_receta) $stockManualStatus = $this->agregarStockIngredienteManual($productoId, $diferenciaDeStock);
                        }
                    }
                    if ($stockManualStatus) {
                        DB::commit();
                        $accion_4_status = true;
                    } else {
                        DB::rollback();
                        $accion_4_status = false;
                    }                    
                } catch (Exception $e) {
                   DB::rollback();
                   $accion_4_status = false;
                }   
            } elseif ($accion == 5) {//Baja mercadería por mal estado
            } elseif ($accion == 6) {//Ingreso mercadería por devolución
            } elseif ($accion == 7) {//Egreso mercadería por devolución
            } elseif ($accion == 8) {//Venta sin stock
                DB::beginTransaction();
                try {
                    // por aquí solo vendrán las ventas de productos o ingredientes que no tengan stock
                    $producto = Producto::find($productoId);  //calculo merma del producto
                    if ($producto->merma > 0) $cantidad = $cantidad + (($cantidad * $producto->merma)/100);

                    if ($agregarVenta) { //debo descontar stock o sumarlo en negativo en algunos casos
                        $cantidad_a_descontar = $cantidad * -1;
                        //verifico si el producto NO TIENE STOCK NULO o NO TIENE RECETA
                        $peps = Peps::where('producto_id', $productoId)->where('comercio_id', $this->comercioId)
                                    ->where('resto', '<>', null)->get();
                        if ($peps->count() > 0 || $producto->tiene_receta == 'no') {  //si el stock NO es NULL, lo modifico
                            $accion_8_status = $this->agregarVentaSinStockProducto($productoId, $cantidad_a_descontar, $detalleVentaId, $costoHistorico);
                        } else {           //si el stock es nulo, pero tiene receta, descuento desde ella
                            $accion_8_status = $this->agregarVentaSinStockIngrediente($productoId, $cantidad_a_descontar, $detalleVentaId, $costoHistorico);
                        }
                    }
                    if ($accion_8_status) DB::commit();
                    else DB::rollback();
                } catch (\Exception $th) {
                    DB::rollback();
                    $accion_8_status = false;
                }
            }
    // dd($accion_1_status , $accion_2_status, $accion_3_status,$accion_4_status , $accion_5_status,
    //     $accion_6_status , $accion_7_status , $accion_8_status);
            if ($accion_1_status && $accion_2_status && $accion_3_status && $accion_4_status && $accion_5_status &&
                $accion_6_status && $accion_7_status && $accion_8_status) {
                DB::commit();
                return true;
            } else DB::rollback();    
        } catch (Exception $e) {
            DB::rollback();
        }
    }

    public function agregarStockProductoManual($productoId, $diferenciaDeStock, $costoHistorico) //chequeado
    {
        DB::beginTransaction();
        try {
            $cantidad_a_agregar = $diferenciaDeStock;
            //primero se lo agrego a las compras ordenadas desc y luego a la EI
            $record = Peps::where('producto_id', $productoId)
                ->where('comercio_id', $this->comercioId)
                ->where('mov_stock_id', 2)
                ->whereColumn('cantidad', '>', 'resto')
                ->orderBy('id', 'desc')->get(); 
            if ($record->count() > 0) { //si hay algún registro, agrego a éste lo que me permita
                                        //continuando con el siguiente si lo necesitara.                        
                foreach ($record as $i) {
                    $nuevo_resto = $i->resto + $cantidad_a_agregar; 
                    if ($nuevo_resto <= $i->cantidad){   
                        $i->update(['resto' => $nuevo_resto]);
                        $cantidad_a_agregar = 0;
                        break;
                    }else {
                        $cantidad_a_grabar = $i->cantidad - $i->resto;
                        $i->update(['resto' => $i->cantidad]);
                        $cantidad_a_agregar -= $cantidad_a_grabar; 
                    }   
                }
            }   
            //si todavía restan agregar más unidades de stock y los registros de compra están
            //igualados en 'cantidad' y 'resto', debo agregarlos a la Existencia
            //Inicial, solo en la columna 'resto'. El valor de 'costo_historico' sera el valor de
            //la variable $costoHistorico, que indica el costo actual del producto.  
            if ($cantidad_a_agregar > 0) {
                $record = Peps::where('mov_stock_id', 1)->where('producto_id', $productoId)
                    ->where('comercio_id', $this->comercioId)->first();
                if ($record) {
                    $resto = $record->resto + $cantidad_a_agregar;
                    $record->update([
                        'resto'           => $resto,
                        'costo_historico' => $costoHistorico,
                    ]);                    
                } else {
                    //creo la EI
                    $peps = Peps::create([
                        'mov_stock_id'    => 1,
                        'producto_id'     => $productoId,
                        'resto'           => $diferenciaDeStock,
                        'costo_historico' => $costoHistorico,
                        'user_id'         => $this->userId,
                        'comercio_id'     => $this->comercioId
                    ]);                   
                } 
            }
            //creo el movimiento
            $peps = Peps::create([
                'mov_stock_id'    => 4, // Modificación Manual Directa
                'producto_id'     => $productoId,
                'cantidad'        => $diferenciaDeStock,
                'user_id'         => $this->userId,
                'comercio_id'     => $this->comercioId
            ]); 
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
        }
    }
    public function descontarStockIngredienteManual($productoId, $diferenciaDeStock, $costoHistorico)//chequeado
    {
        $diferenciaDeStockParaMovimiento = $diferenciaDeStock;       
        DB::beginTransaction();
        try {
            $receta = Receta::where('producto_id', $productoId)->where('comercio_id', $this->comercioId)->select('id')->first();
            if($receta){  
                $principal = DetReceta::join('productos as p', 'p.id', 'det_recetas.producto_id')
                    ->where('det_recetas.comercio_id', $this->comercioId)
                    ->where('det_recetas.receta_id', $receta->id)
                    ->where('det_recetas.principal', 'si')                                    
                    ->select('det_recetas.*', 'p.merma')->get();
                if($principal->count() > 0){
                    foreach ($principal as $i) {
                        $cantidad_para_grabar_movimiento = 0;
                        //calculo merma
                        if ($i->merma > 0) $cantidad_real = $i->cantidad + (($i->cantidad * $i->merma)/100);
                        else $cantidad_real = $i->cantidad;
                        $resto_a_grabar = $diferenciaDeStock * $cantidad_real; //cantidad de productos modificados * cantidad de receta
                        $cantidad_para_grabar_movimiento = $resto_a_grabar;

                        //primero se lo quito a la EI, si tiene resto,
                        $record = Peps::where('producto_id', $i->producto_id)->where('mov_stock_id', 1)
                                ->where('comercio_id', $this->comercioId)->first();
                        if ($record) {
                            $resto_a_grabar = $record->resto - $cantidad_para_grabar_movimiento;                 
                            if ($resto_a_grabar >= 0) $record->update([ 'resto' => $resto_a_grabar ]);
                            else $record->update([ 'resto' => 0 ]); 
                        }       
                                                       
                        //si aún queda algo por descontar, lo hago en alguna compra en donde  
                        //'cantidad' sea mayor a 'resto' para descontar sobre éste
                        if ($resto_a_grabar < 0) { 
                            $record = Peps::where('producto_id', $i->producto_id)->where('mov_stock_id', 2)
                                ->where('comercio_id', $this->comercioId)
                                ->whereColumn('cantidad', '>', 'resto')->first(); 
                            if ($record) {
                                $resto_a_grabar = $resto_a_grabar * -1;
                                $nuevo_resto = $record->resto - $resto_a_grabar; //   1 = 2-1
                                if ($nuevo_resto >= 0) $record->update(['resto' => $nuevo_resto]);
                                else {
                                    $record->update(['resto' => 0]);
                                    $resto_a_grabar = $nuevo_resto;
                                }   
                            }
                        } 
                        //y finalmente a las compras en donde 'cantidad' sea igual a 'resto'
                        if ($resto_a_grabar < 0) {
                            $record = Peps::where('producto_id', $i->producto_id)->where('mov_stock_id', 2)
                                ->whereColumn('cantidad', 'resto')->where('comercio_id', $this->comercioId)
                                ->orderBy('id')->get(); 
                            if ($record->count() > 0) {                       
                                foreach ($record as $i) {
                                    $resto_a_grabar = $resto_a_grabar * -1;
                                    $nuevo_resto = $i->resto - $resto_a_grabar; 
                                    if ($nuevo_resto > 0){            
                                        $i->update(['resto' => $nuevo_resto]);
                                        break;
                                    }else {
                                        $i->update(['resto' => 0]);
                                        $resto_a_grabar = $nuevo_resto;
                                    }   
                                }
                            }
                        }
                        $peps = Peps::create([
                            'mov_stock_id'    => 5, // Modificación Manual Indirecta
                            'producto_id'     => $i->producto_id,
                            'cantidad'        => $cantidad_para_grabar_movimiento * -1,
                            'resto'           => null,
                            'prod_modif_id'   => $productoId,
                            'cant_prod_modif' => $diferenciaDeStockParaMovimiento,
                            'user_id'         => $this->userId,
                            'comercio_id'     => $this->comercioId
                        ]); 
                    }
                }
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
        }  
    }    

    public function descontarStockProductoManual($productoId, $diferenciaDeStock)//chequeado
    {
        DB::beginTransaction();
        try {    
            $resto_a_grabar = $diferenciaDeStock;
            //primero se lo quito a la EI, si tiene resto,
            $record = Peps::where('mov_stock_id', 1)->where('producto_id', $productoId)
                    ->where('comercio_id', $this->comercioId)->first();
            if ($record->resto > 0) {                                    
                $resto_a_grabar = $record->resto + $diferenciaDeStock; 
                if ($resto_a_grabar >= 0) $record->update([ 'resto' => $resto_a_grabar ]);
                else $record->update([ 'resto' => 0 ]); 
            }
            //luego a alguna compra en donde 'cantidad' sea mayor a 'resto'
            //para descontar de su resto
            if ($resto_a_grabar < 0) { 
                $record = Peps::where('producto_id', $productoId)
                    ->where('comercio_id', $this->comercioId)
                    ->where('mov_stock_id', 2)
                    ->whereColumn('cantidad', '>', 'resto')->first(); 
                if ($record) {
                    $resto_a_grabar = $resto_a_grabar * -1;
                    $nuevo_resto = $record->resto - $resto_a_grabar; 
                    if ($nuevo_resto >= 0) $record->update(['resto' => $nuevo_resto]);
                    else {
                        $record->update(['resto' => 0]);
                        $resto_a_grabar = $nuevo_resto;
                    }   
                }
            }
            //y finalmente a las compras en donde 'cantidad' sea igual a 'resto'
            if ($resto_a_grabar < 0) { 
                $record = Peps::where('producto_id', $productoId)
                    ->where('comercio_id', $this->comercioId)
                    ->where('mov_stock_id', 2)
                    ->whereColumn('cantidad', 'resto')
                    ->orderBy('id')->get(); 
                if ($record->count() > 0) {                       
                    foreach ($record as $i) {
                        $resto_a_grabar = $resto_a_grabar * -1;
                        $nuevo_resto = $i->resto - $resto_a_grabar; 
                        if ($nuevo_resto > 0){            
                            $i->update(['resto' => $nuevo_resto]);
                            break;
                        }else {
                            $i->update(['resto' => 0]);
                            $resto_a_grabar = $nuevo_resto;
                        }   
                    }
                }
            } 
            //creo el movimiento
            $peps = Peps::create([
                'mov_stock_id'    => 4, // Modificación Manual Directa
                'producto_id'     => $productoId,
                'cantidad'        => $diferenciaDeStock,
                'user_id'         => $this->userId,
                'comercio_id'     => $this->comercioId
            ]); 
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
        }            
    }
    public function agregarStockIngredienteManual($productoId, $diferenciaDeStock)//chequeado 
    {
        DB::beginTransaction();
        try {
            $diferenciaDeStockParaMovimiento = $diferenciaDeStock;
            $diferenciaDeStock = $diferenciaDeStock * -1;
          
            $receta = Receta::where('producto_id', $productoId)->where('comercio_id', $this->comercioId)->select('id')->first();
            if($receta){  
                $principal = DetReceta::join('productos as p', 'p.id', 'det_recetas.producto_id')
                    ->where('det_recetas.comercio_id', $this->comercioId)
                    ->where('det_recetas.receta_id', $receta->id)
                    ->where('det_recetas.principal', 'si')                                    
                    ->select('det_recetas.*', 'p.merma')->get();
                if($principal->count() > 0){
                    foreach ($principal as $i) {
                        $cantidad_para_grabar_movimiento = 0;
                        //calculo merma
                        if ($i->merma > 0) $cantidad_real = $i->cantidad + (($i->cantidad * $i->merma)/100);
                        else $cantidad_real = $i->cantidad;
                  
                        $resto_a_grabar = $diferenciaDeStock * $cantidad_real; //diferencia de stock * cantidad de receta
                        $cantidad_para_grabar_movimiento = $resto_a_grabar;    
                        
                        //primero se lo agrego a una o varias ventas sin stock, si es existe alguna
                        $ventaSinStock = Peps::where('producto_id', $i->producto_id)
                            ->where('mov_stock_id', 8)
                            ->where('comercio_id', $this->comercioId)
                            ->orderBy('id')->get();
                        if ($ventaSinStock->count() > 0) {                       
                            foreach ($ventaSinStock as $i) {
                                $nuevo_resto = round(($i->resto + $resto_a_grabar),3);
                                $resto_a_grabar = $nuevo_resto; 
                                 
                                if ($nuevo_resto >= 0) $i->update(['resto' => null]);
                                else $i->update(['resto' => $nuevo_resto]);

                                if ($nuevo_resto <= 0) break; 
                            }
                        }               
                      
                        //luego se lo agrego a alguna compra en donde resto sea menor a cantidad
                        if ($resto_a_grabar > 0) {
                            $record = Peps::where('producto_id', $i->producto_id)->where('mov_stock_id', 2)
                                ->whereColumn('cantidad', '>', 'resto')->where('comercio_id', $this->comercioId)
                                ->orderBy('id', 'desc')->get();
                            if ($record->count() > 0) {                       
                                foreach ($record as $i) {
                                    $nuevo_resto = $i->resto + $resto_a_grabar; 
                                    if ($nuevo_resto <= $i->cantidad){            
                                        $i->update(['resto' => $nuevo_resto]);
                                        break;
                                    }else {
                                        $restaurarCompra = $i->cantidad - $i->resto;
                                        $resto_a_grabar = $resto_a_grabar - $restaurarCompra;
                                        $i->update(['resto' => $i->cantidad]);
                                    }   
                                }
                            }
                        }

                        //finalmente se lo agrego a la EI   
                        if ($resto_a_grabar > 0) {
                            $cantidadEI = 0;
                            $restoEI = 0;
                            $record = Peps::where('producto_id', $i->producto_id)->where('mov_stock_id', 1)
                                    ->where('comercio_id', $this->comercioId)->first();
                            if ($record) {
                                // if (!$record->cantidad) $cantidadEI = 0;
                                // else $cantidadEI = $record->cantidad;
                                if (!$record->resto) $restoEI = 0;
                                else $restoEI = $record->resto;
                                $resto_a_grabar = $restoEI + $resto_a_grabar;
                                if ($cantidadEI > 0 && $resto_a_grabar > $cantidadEI) { 
                                    $record->update([ 
                                        'cantidad' => $resto_a_grabar,
                                        'resto'    => $resto_a_grabar 
                                    ]);
                                } else $record->update([ 'resto' => $resto_a_grabar]);    
                            }  
                        } 
                        //creo el movimiento
                        $peps = Peps::create([
                            'mov_stock_id'    => 5, // Modificación Manual Indirecta
                            'producto_id'     => $i->producto_id,
                            'cantidad'        => $cantidad_para_grabar_movimiento,
                            'prod_modif_id'   => $productoId,
                            'cant_prod_modif' => $diferenciaDeStockParaMovimiento,
                            'user_id'         => $this->userId,
                            'comercio_id'     => $this->comercioId
                        ]); 
                    }
                }
            } 
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
        }        
    }

    public function agregarVentaProducto($record, $productoId, $cantidad, $detalleVentaId, $accion)
    {
        DB::beginTransaction();
        try {
            $cantidad_a_descontar = $cantidad;
            foreach ($record as $i) {
                $nuevo_resto = $i->resto - $cantidad_a_descontar; 
                if ($nuevo_resto >= 0){                         
                    $mod_peps = Peps::find($i->id)->update(['resto' => $nuevo_resto]);
                    break;
                }else {
                    $mod_peps = Peps::find($i->id)->update(['resto' => 0]);
                    $cantidad_a_descontar = $nuevo_resto * -1;
                }   
            } 
            //creo o modifico el movimiento correspondiente 
            $cantidad = $cantidad * -1;
            $record = Peps::where('det_venta_id', $detalleVentaId)
                        ->where('producto_id', $productoId)
                        ->where('mov_stock_id', $accion)->first();
            if ($record) {
                $nueva_cantidad = $record->cantidad + $cantidad;
                $record->update(['cantidad' => $nueva_cantidad]);
            } else {
                $peps = Peps::create([
                    'mov_stock_id' => $accion,
                    'det_venta_id' => $detalleVentaId,
                    'producto_id'  => $productoId,
                    'cantidad'     => $cantidad,
                    'user_id'      => $this->userId,
                    'comercio_id'  => $this->comercioId
                ]); 
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
        }
    }
    public function agregarVentaIngrediente($productoId, $cantidad, $detalleVentaId, $accion)
    {
        DB::beginTransaction();
        try {
            $receta = Receta::where('producto_id', $productoId)->where('comercio_id', $this->comercioId)->select('id')->first();
            if($receta){  
                $principal = DetReceta::join('productos as p', 'p.id', 'det_recetas.producto_id')
                    ->where('det_recetas.comercio_id', $this->comercioId)
                    ->where('det_recetas.receta_id', $receta->id)
                    ->where('det_recetas.principal', 'si')                                    
                    ->select('det_recetas.*', 'p.merma')->get();
                if($principal->count() > 0){
                    foreach ($principal as $i) {
                    //calculo merma de los ingredientes
                        if ($i->merma > 0) $cantidad_real = $i->cantidad + (($i->cantidad * $i->merma)/100);
                        else $cantidad_real = $i->cantidad;
                        $resto_a_grabar = $cantidad * $cantidad_real; //cantidad de productos vendidos * cantidad de receta
                        $cantidad_mov_venta = $resto_a_grabar * -1;
                    //primero se lo quito a la EI, si tiene resto,
                        $record = Peps::where('producto_id', $i->producto_id)->where('mov_stock_id', 1)
                                ->where('comercio_id', $this->comercioId)->first();
                        $resto_a_grabar = $record->resto - $resto_a_grabar;                    //   -1=1-2
                        if ($resto_a_grabar >= 0) $record->update([ 'resto' => $resto_a_grabar ]);
                        else $record->update([ 'resto' => 0 ]);                                
                    //luego a alguna compra en donde 'cantidad' sea mayor a 'resto'
                        if ($resto_a_grabar < 0) { 
                            $record = Peps::where('producto_id', $i->producto_id)->where('mov_stock_id', 2)
                                ->where('comercio_id', $this->comercioId)
                                ->whereColumn('cantidad', '>', 'resto')->first(); 
                            if ($record) {
                                $resto_a_grabar = $resto_a_grabar * -1;
                                $nuevo_resto = $record->resto - $resto_a_grabar; //   1 = 2-1
                                if ($nuevo_resto >= 0) $record->update(['resto' => $nuevo_resto]);
                                else {
                                    $record->update(['resto' => 0]);
                                    $resto_a_grabar = $nuevo_resto;
                                }   
                            }
                        } 
                    //y finalmente a las compras en donde 'cantidad' sea igual a 'resto'
                        if ($resto_a_grabar < 0) { // -13
                            $record = Peps::where('producto_id', $i->producto_id)->where('mov_stock_id', 2)
                                ->whereColumn('cantidad', 'resto')->where('comercio_id', $this->comercioId)
                                ->orderBy('id')->get(); 
                            if ($record->count() > 0) {                       
                                foreach ($record as $i) {
                                    $resto_a_grabar = $resto_a_grabar * -1;
                                    $nuevo_resto = $i->resto - $resto_a_grabar; 
                                    if ($nuevo_resto > 0){            
                                        $i->update(['resto' => $nuevo_resto]);
                                        break;
                                    }else {
                                        $i->update(['resto' => 0]);
                                        $resto_a_grabar = $nuevo_resto;
                                    }   
                                }
                            }
                        }
                    //creo o modifico el movimiento correspondiente
                        $record = Peps::where('det_venta_id', $detalleVentaId)
                                    ->where('producto_id', $i->producto_id)
                                    ->where('mov_stock_id', $accion)->first();
                        if ($record) {
                            $nueva_cantidad = $record->cantidad + $cantidad_mov_venta;
                            $record->update(['cantidad' => $nueva_cantidad]);
                        } else {
                            $peps = Peps::create([
                                'mov_stock_id' => $accion,
                                'det_venta_id' => $detalleVentaId,
                                'producto_id'  => $i->producto_id,
                                'cantidad'     => $cantidad_mov_venta,
                                'user_id'      => $this->userId,
                                'comercio_id'  => $this->comercioId
                            ]); 
                        }  
                    }
                }
            } 
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
        }     
    }
    public function descontarVentaSinStock($productoId, $cantidad, $detalleVentaId)
    { //DESCONTAR ventas significa AGREGAR stock
        DB::beginTransaction();
        try { 
            //primero modifico SOLO LA CANTIDAD de venta sin stock para el producto 
            //CON ESTE $detalleVentaId, SIN IMPORTAR SI TIENE RESTO O NO
            $cantidadSinStock = Peps::where('det_venta_id', $detalleVentaId)->where('producto_id', $productoId)
                ->where('mov_stock_id', 8)->where('comercio_id', $this->comercioId)->first(); 
            if ($cantidadSinStock) {  // si hay cantidad sin stock
                $nueva_cantidad = round(($cantidadSinStock->cantidad + $cantidad),3);  
                if ($nueva_cantidad < 0) $cantidadSinStock->update(['cantidad' => $nueva_cantidad]);
                else $cantidadSinStock->update(['cantidad' => null]);                
            } else {   // sino, busco en sus ingredientes, RECORDAR QUE ES SOLO CANTIDAD
                $receta = Receta::where('producto_id', $productoId)->where('comercio_id', $this->comercioId)->first();
                if ($receta) {
                    $principal = DetReceta::join('productos as p', 'p.id', 'det_recetas.producto_id')
                        ->where('det_recetas.comercio_id', $this->comercioId)
                        ->where('det_recetas.receta_id', $receta->id)
                        ->where('det_recetas.principal', 'si')                                    
                        ->select('det_recetas.*', 'p.merma')->get();
                    if($principal->count() > 0){
                        foreach ($principal as $i) {
                            $cantidad_mov_venta_con_stock = 0;
                            //calculo merma del ingrediente
                            if ($i->merma > 0) $cantidad_real = $i->cantidad + (($i->cantidad * $i->merma)/100);
                            else $cantidad_real = $i->cantidad;
                            $cantidad_a_grabar = $cantidad * $cantidad_real; //cantidad de productos vendidos * cantidad de receta
                            $cantidad_mov_venta_con_stock = $cantidad_a_grabar;
                            $cantidadSinStock = Peps::where('det_venta_id', $detalleVentaId)->where('producto_id', $i->producto_id)
                                ->where('mov_stock_id', 8)->where('comercio_id', $this->comercioId)->first();
                            if ($cantidadSinStock) {
                                $nueva_cantidad = round(($cantidadSinStock->cantidad + $cantidad_a_grabar),3);
                                $cantidad_a_grabar = $nueva_cantidad;
                            $cantidad_mov_venta_con_stock = $cantidad_a_grabar;
                                if ($nueva_cantidad < 0) $cantidadSinStock->update(['cantidad' => $nueva_cantidad]);
                                else $cantidadSinStock->update(['cantidad' => null]);
                            }

                            //actualizo el movimiento de VENTAS con stock
                            if ($cantidad_mov_venta_con_stock > 0) {                                
                                $record = Peps::where('det_venta_id', $detalleVentaId)
                                    ->where('producto_id', $i->producto_id)
                                    ->where('mov_stock_id', 3)->first();
                                if ($record) {
                                    $nueva_cantidad = $record->cantidad + $cantidad_mov_venta_con_stock;
                                    $record->update(['cantidad' => $nueva_cantidad]);
                                } else {
                                    $peps = Peps::create([
                                        'mov_stock_id' => 3,
                                        'det_venta_id' => $detalleVentaId,
                                        'producto_id'  => $i->producto_id,
                                        'cantidad'     => $cantidad_mov_venta_con_stock,
                                        'user_id'      => $this->userId,
                                        'comercio_id'  => $this->comercioId
                                    ]); 
                                } 
                            }
                        }
                    }
                }
            }
            
            //luego SOLO LOS RESTOS de ventas sin stock para el producto CON CUALQUIER $detalleVentaId
            $restosSinStock = Peps::where('mov_stock_id', 8)->where('producto_id', $productoId)
                ->where('comercio_id', $this->comercioId)->where('resto', '<', 0)->get(); 
            if ($restosSinStock->count() > 0) {  // si hay restos sin stock
                foreach ($restosSinStock as $i) {
                    $nuevo_resto = $i->resto + $cantidad; 
                    $cantidad = $nuevo_resto;   
                    if ($nuevo_resto < 0) { 
                        $i->update(['resto' => $nuevo_resto]);
                        break;
                    } else $i->update(['resto' => null, 'costo_historico' => null]);
                }  
            } else {   // sino, busco en sus ingredientes, RECORDAR QUE SON SOLO RESTOS
                $receta = Receta::where('producto_id', $productoId)->where('comercio_id', $this->comercioId)->first();
                if ($receta) {
                    $principal = DetReceta::join('productos as p', 'p.id', 'det_recetas.producto_id')
                        ->where('det_recetas.comercio_id', $this->comercioId)
                        ->where('det_recetas.receta_id', $receta->id)
                        ->where('det_recetas.principal', 'si')                                    
                        ->select('det_recetas.*', 'p.merma')->get();
                    if($principal->count() > 0){
                        foreach ($principal as $i) {
                            //calculo merma del ingrediente
                            if ($i->merma > 0) $cantidad_real = $i->cantidad + (($i->cantidad * $i->merma)/100);
                            else $cantidad_real = $i->cantidad;
                            $resto_a_grabar = $cantidad * $cantidad_real; //cantidad de productos vendidos * cantidad de receta
                       
                            //primero recorro ventasSinStock
                            $ventaSinStock = Peps::where('producto_id', $i->producto_id)
                                ->where('mov_stock_id', 8)->where('resto', '<', 0)  // == 0 => break;
                                ->where('comercio_id', $this->comercioId)->get();
                                       // -0.150 + 0.100 = -0.050 => $cantidad = -0.050
                            if ($ventaSinStock->count() > 0) {   // -0.088 + 0.050 = 0.038 => $cantidad = 0.012
                                foreach ($ventaSinStock as $j) { // -0.150 + 0.012 = -0.138 => $cantidad = -0.138 => break;
                                    $nuevo_resto = round(($j->resto + $resto_a_grabar),3);
                                    $resto_a_grabar = $nuevo_resto;
                                    if ($nuevo_resto < 0) { 
                                        $j->update(['resto' => $nuevo_resto]);
                                        break;
                                    } else $j->update(['resto' => null, 'costo_historico' => null]);
                                }
                            } 
                            //luego recorro compras
                            if ($resto_a_grabar > 0) {
                                $compras = Peps::where('producto_id', $i->producto_id)->where('mov_stock_id', 2)
                                    ->whereColumn('resto', '<', 'cantidad')->where('comercio_id', $this->comercioId)
                                    ->orderBy('id', 'desc')->get();          
                                if ($compras->count() > 0) {                   
                                    foreach ($compras as $j) {               
                                        $nuevo_resto = $j->resto + $resto_a_grabar; 
                                        $diferencia = $j->cantidad - $j->resto; 
                                        $resto_a_grabar = $resto_a_grabar - $diferencia;
                                        if ($nuevo_resto > $j->cantidad) {
                                            $j->update([ 'resto' => $j->cantidad]);
                                        } else {
                                            $j->update([ 'resto' => $nuevo_resto]);
                                            break;
                                        }
                                    }                                    
                                }
                            }
                            //y finalmente se lo agrego a la EI
                            if ($resto_a_grabar > 0) {
                                $record = Peps::where('producto_id', $i->producto_id)->where('mov_stock_id', 1)
                                        ->where('comercio_id', $this->comercioId)->first();
                                if ($record) {
                                    if (!$record->cantidad) $cantidadEI = null;
                                    else $cantidadEI = $record->cantidad;
                                    if (!$record->resto) $restoEI = 0;
                                    else $restoEI = $record->resto;
                                    $resto_a_grabar = $restoEI + $resto_a_grabar;
                                    if ($cantidadEI && $resto_a_grabar > $cantidadEI) { 
                                        $record->update([ 
                                            'cantidad' => $resto_a_grabar,
                                            'resto'    => $resto_a_grabar 
                                        ]);
                                    } else $record->update([ 'resto' => $resto_a_grabar]);    
                                }  
                            }
                        }
                        $cantidad = 0; //devuelvo cero porque se supone que se devolvieron todos los stocks
                    }
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return null;
        }
        return $cantidad;
    }
    // public function descontarVentaSinStock($productoId, $cantidad, $detalleVentaId)
    // { //DESCONTAR ventas significa AGREGAR stock
    //     DB::beginTransaction();
    //     try { 
    //         //primero modifico SOLO LA CANTIDAD de venta sin stock para el producto 
    //         //CON ESTE $detalleVentaId, SIN IMPORTAR SI TIENE RESTO O NO
    //         $cantidadSinStock = Peps::where('det_venta_id', $detalleVentaId)->where('producto_id', $productoId)
    //             ->where('mov_stock_id', 8)->where('comercio_id', $this->comercioId)->first(); 
    //         if ($cantidadSinStock) {  // si hay cantidad sin stock
    //             $nueva_cantidad = round(($cantidadSinStock->cantidad + $cantidad),3);  
    //             if ($nueva_cantidad < 0) $cantidadSinStock->update(['cantidad' => $nueva_cantidad]);
    //             else $cantidadSinStock->update(['cantidad' => null]);
    //         } else {   // sino, busco en sus ingredientes, RECORDAR QUE ES SOLO CANTIDAD
    //             $receta = Receta::where('producto_id', $productoId)->where('comercio_id', $this->comercioId)->first();
    //             if ($receta) {
    //                 $principal = DetReceta::join('productos as p', 'p.id', 'det_recetas.producto_id')
    //                     ->where('det_recetas.comercio_id', $this->comercioId)
    //                     ->where('det_recetas.receta_id', $receta->id)
    //                     ->where('det_recetas.principal', 'si')                                    
    //                     ->select('det_recetas.*', 'p.merma')->get();
    //                 if($principal->count() > 0){
    //                     foreach ($principal as $i) {
    //                         $cantidad_mov_venta_con_stock = 0;
    //                         //calculo merma del ingrediente
    //                         if ($i->merma > 0) $cantidad_real = $i->cantidad + (($i->cantidad * $i->merma)/100);
    //                         else $cantidad_real = $i->cantidad;
    //                         $cantidad_a_grabar = $cantidad * $cantidad_real; //cantidad de productos vendidos * cantidad de receta
    //                     $cantidad_mov_venta_con_stock = $cantidad_a_grabar;
    //                 //dd($cantidad_mov_venta_con_stock);
    //                         $cantidadSinStock = Peps::where('det_venta_id', $detalleVentaId)->where('producto_id', $i->producto_id)
    //                             ->where('mov_stock_id', 8)->where('comercio_id', $this->comercioId)->first();
    //                         //dd($cantidadSinStock);                      
    //                         if ($cantidadSinStock) {// -0.100 + 0.100 = 0.0 / -0.100 + 0.030 = -0.070 / -0.100 + 0.130 = 0.030
    //                             $nueva_cantidad = round(($cantidadSinStock->cantidad + $cantidad_a_grabar),3); 
    //                             //if($i->producto_id == 27) dd($nueva_cantidad ,$cantidadSinStock->cantidad , $cantidad_a_grabar);
    //                             $cantidad_a_grabar = $nueva_cantidad;
    //                         $cantidad_mov_venta_con_stock = $cantidad_a_grabar;
    //                             if ($nueva_cantidad < 0) $cantidadSinStock->update(['cantidad' => $nueva_cantidad]);
    //                             else $cantidadSinStock->update(['cantidad' => null]);
    //                         }
    //                     }
    //                 }
    //                 dd($i->producto_id,$cantidad_mov_venta_con_stock);
    //             }
    //         }
            
    //         //luego SOLO LOS RESTOS de ventas sin stock para el producto CON CUALQUIER $detalleVentaId
    //         $restosSinStock = Peps::where('mov_stock_id', 8)->where('producto_id', $productoId)
    //             ->where('comercio_id', $this->comercioId)->where('resto', '<', 0)->get(); 
    //         if ($restosSinStock->count() > 0) {  // si hay restos sin stock
    //             foreach ($restosSinStock as $i) {
    //                 $nuevo_resto = $i->resto + $cantidad; 
    //                 $cantidad = $nuevo_resto;   
    //                 if ($nuevo_resto < 0) { 
    //                     $i->update(['resto' => $nuevo_resto]);
    //                     break;
    //                 } else $i->update(['resto' => 0]);
    //             }  
    //         } else {   // sino, busco en sus ingredientes, RECORDAR QUE SON SOLO RESTOS
    //             $receta = Receta::where('producto_id', $productoId)->where('comercio_id', $this->comercioId)->first();
    //             if ($receta) {
    //                 $principal = DetReceta::join('productos as p', 'p.id', 'det_recetas.producto_id')
    //                     ->where('det_recetas.comercio_id', $this->comercioId)
    //                     ->where('det_recetas.receta_id', $receta->id)
    //                     ->where('det_recetas.principal', 'si')                                    
    //                     ->select('det_recetas.*', 'p.merma')->get();
    //                 if($principal->count() > 0){
    //                     foreach ($principal as $i) {
    //                         //calculo merma del ingrediente
    //                         if ($i->merma > 0) $cantidad_real = $i->cantidad + (($i->cantidad * $i->merma)/100);
    //                         else $cantidad_real = $i->cantidad;
    //                         $resto_a_grabar = $cantidad * $cantidad_real; //cantidad de productos vendidos * cantidad de receta
                       
    //                         //primero recorro ventasSinStock
    //                         $ventaSinStock = Peps::where('producto_id', $i->producto_id)
    //                             ->where('mov_stock_id', 8)->where('resto', '<', 0)  // == 0 => break;
    //                             ->where('comercio_id', $this->comercioId)->get();
    //                                    // -0.150 + 0.100 = -0.050 => $cantidad = -0.050
    //                         if ($ventaSinStock->count() > 0) {   // -0.088 + 0.050 = 0.038 => $cantidad = 0.012
    //                             foreach ($ventaSinStock as $j) { // -0.150 + 0.012 = -0.138 => $cantidad = -0.138 => break;
    //                                 $nuevo_resto = round(($j->resto + $resto_a_grabar),3);
    //                                 $resto_a_grabar = $nuevo_resto;
    //                                 if ($nuevo_resto < 0) { 
    //                                     $j->update(['resto' => $nuevo_resto]);
    //                                     break;
    //                                 } else $j->update(['resto' => 0]);
    //                             }
    //                         } 
    //                     //$cantidad_mov_venta_con_stock = $resto_a_grabar;
    //                     //if($i->producto_id == 27) dd($cantidad_mov_venta_con_stock);
    //                         //luego recorro compras
    //                         if ($resto_a_grabar > 0) {
    //                             $compras = Peps::where('producto_id', $i->producto_id)->where('mov_stock_id', 2)
    //                                 ->whereColumn('resto', '<', 'cantidad')->where('comercio_id', $this->comercioId)
    //                                 ->orderBy('id', 'desc')->get();           //$resto_a_grabar = 0.088
    //                             if ($compras->count() > 0) {                  //$j->cantidad = 
    //                                 foreach ($compras as $j) {                //$i->resto    = 5
    //                                     $nuevo_resto = $j->resto + $resto_a_grabar; // 5 + 8 = 13
    //                                     $diferencia = $j->cantidad - $j->resto; // 7 - 5 = 2
    //                                     $resto_a_grabar = $resto_a_grabar - $diferencia; //8 - 2 = 6
    //                                     if ($nuevo_resto > $j->cantidad) {
    //                                         $j->update([ 'resto' => $j->cantidad]);
    //                                     } else {
    //                                         $j->update([ 'resto' => $nuevo_resto]);
    //                                         break;
    //                                     }
    //                                 }                                    
    //                             }
    //                         }
    //                         //y finalmente se lo agrego a la EI
    //                         if ($resto_a_grabar > 0) {
    //                             $record = Peps::where('producto_id', $i->producto_id)->where('mov_stock_id', 1)
    //                                     ->where('comercio_id', $this->comercioId)->first();
    //                             if ($record) {
    //                                 if (!$record->cantidad) $cantidadEI = null;
    //                                 else $cantidadEI = $record->cantidad;
    //                                 if (!$record->resto) $restoEI = 0;
    //                                 else $restoEI = $record->resto;
    //                                 $resto_a_grabar = $restoEI + $resto_a_grabar;
    //                                 //$record->update([ 'resto' => $resto_a_grabar]); 
    //                                 if ($cantidadEI && $resto_a_grabar > $cantidadEI) { 
    //                                     $record->update([ 
    //                                         'cantidad' => $resto_a_grabar,
    //                                         'resto'    => $resto_a_grabar 
    //                                     ]);
    //                                 } else $record->update([ 'resto' => $resto_a_grabar]);    
    //                             }  
    //                         }
    //                         dd($i->producto_id,$cantidad_mov_venta_con_stock);
    //                         //if($i->producto_id == 27) dd($cantidad_mov_venta_con_stock);
    //                         if ($cantidad_mov_venta_con_stock > 0) {
    //                             //actualizo el movimiento de VENTAS con stock
    //                             $record = Peps::where('det_venta_id', $detalleVentaId)
    //                                 ->where('producto_id', $i->producto_id)
    //                                 ->where('mov_stock_id', 3)->first();
    //                             if ($record) {
    //                                 $nueva_cantidad = $record->cantidad + $cantidad_mov_venta_con_stock;
    //                                 $record->update(['cantidad' => $nueva_cantidad]);
    //                             } else {
    //                                 $peps = Peps::create([
    //                                     'mov_stock_id' => 3,
    //                                     'det_venta_id' => $detalleVentaId,
    //                                     'producto_id'  => $i->producto_id,
    //                                     'cantidad'     => $cantidad_mov_venta_con_stock,
    //                                     'user_id'      => $this->userId,
    //                                     'comercio_id'  => $this->comercioId
    //                                 ]); 
    //                             } 
    //                         }
    //                     }
    //                     $cantidad = 0; //devuelvo cero porque se supone que se devolvieron todos los stocks
    //                 }
    //             }
    //         }
    //         DB::commit();
    //     } catch (Exception $e) {
    //         DB::rollback();
    //         return null;
    //     }
    //     return $cantidad;
    // }
    public function agregarVentaSinStockProducto($productoId, $cantidad_a_descontar, $detalleVentaId, $costoHistorico)
    {
        DB::beginTransaction();
        try {
            //primero descuento lo que pueda si encuentro algún resto > 0
            $stock = Peps::where('producto_id', $productoId)
                ->where('comercio_id', $this->comercioId)
                ->where('resto', '>', 0)->first(); 
            if ($stock) { //modifico la compra o EI
                $cantidad_a_descontar = $stock->resto + $cantidad_a_descontar;  
                $resto_con_stock = $stock->resto * -1;  
                $stock->update(['resto' => 0]);
                //modifico o creo la venta con stock
                $venta = Peps::where('det_venta_id', $detalleVentaId)->where('producto_id', $productoId)
                    ->where('comercio_id', $this->comercioId)->where('mov_stock_id', 3)->first();
                if ($venta) { // si existe
                    $cantidad_con_stock = $venta->cantidad + $resto_con_stock; 
                    $venta->update(['cantidad' => $cantidad_con_stock]);
                } else {//registro la venta con stock con el stock que pude descontar anteriormente                    
                    Peps::create([
                        'mov_stock_id'    => 3,
                        'det_venta_id'    => $detalleVentaId,
                        'producto_id'     => $productoId,
                        'cantidad'        => $resto_con_stock,
                        'user_id'         => $this->userId,
                        'comercio_id'     => $this->comercioId
                    ]);
                }
            }
            //si no encuentro resto o resta algo, lo agrego en el movimiento
            $ventaSinStock = Peps::where('det_venta_id', $detalleVentaId)->where('producto_id', $productoId)
                ->where('comercio_id', $this->comercioId)->where('mov_stock_id', 8)->first();
            if ($ventaSinStock) {
                $cantidad = $ventaSinStock->cantidad + $cantidad_a_descontar;
                $resto = $ventaSinStock->resto + $cantidad_a_descontar;
                $ventaSinStock->update([
                    'cantidad'        => $cantidad,
                    'resto'           => $resto,
                    'costo_historico' => $costoHistorico
                ]);
            } else {
                Peps::create([
                    'mov_stock_id'    => 8,
                    'det_venta_id'    => $detalleVentaId,
                    'producto_id'     => $productoId,
                    'cantidad'        => $cantidad_a_descontar,
                    'resto'           => $cantidad_a_descontar,
                    'costo_historico' => $costoHistorico,
                    'user_id'         => $this->userId,
                    'comercio_id'     => $this->comercioId
                ]); 
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
        }
    }
    public function agregarVentaSinStockIngrediente($productoId, $cantidad, $detalleVentaId, $costoHistorico)
    {
        DB::beginTransaction();
        try {        
            $receta = Receta::where('producto_id', $productoId)->where('comercio_id', $this->comercioId)->select('id')->first();
            if($receta){ 
                $principal = DetReceta::join('productos as p', 'p.id', 'det_recetas.producto_id')
                    ->where('p.comercio_id', $this->comercioId)
                    ->where('det_recetas.receta_id', $receta->id)
                    ->where('det_recetas.principal', 'si')                                    
                    ->select('det_recetas.*', 'p.descripcion', 'p.merma')->get();
                if($principal->count() > 0){
                    $resto_a_grabar = 0;
                    foreach ($principal as $i) {
                        //calculo merma de los ingredientes
                        if ($i->merma > 0) $cantidad_real = $i->cantidad + (($i->cantidad * $i->merma)/100);
                        else $cantidad_real = $i->cantidad;
                        $resto_a_grabar = ($cantidad * $cantidad_real) * -1; //cantidad de productos vendidos * cantidad de receta c/merma
                        $cantidad_mov_venta_con_stock = $resto_a_grabar;

                        $stock = Peps::where('producto_id', $i->producto_id)->where('comercio_id', $this->comercioId)->sum('resto');
                        
                        if ($stock >= $resto_a_grabar) { //si tengo stock del ingrediente 
                            //primero se lo quito a la EI, si tiene resto,
                            $record = Peps::where('producto_id', $i->producto_id)->where('mov_stock_id', 1)
                                    ->where('comercio_id', $this->comercioId)->first();
                            $resto_a_grabar = $record->resto - $resto_a_grabar;                    //   -1=1-2
                                           
                            if ($resto_a_grabar >= 0) $record->update([ 'resto' => $resto_a_grabar ]);
                            else $record->update([ 'resto' => 0 ]); 

                            //luego a alguna compra en donde 'cantidad' sea mayor a 'resto'
                            if ($resto_a_grabar < 0) { 
                                $record = Peps::where('producto_id', $i->producto_id)->where('mov_stock_id', 2)
                                    ->where('comercio_id', $this->comercioId)
                                    ->whereColumn('cantidad', '>', 'resto')->first(); 
                                if ($record) {
                                    $resto_a_grabar = $resto_a_grabar * -1;
                                    $nuevo_resto = $record->resto - $resto_a_grabar; //   1 = 2-1
                                    if ($nuevo_resto >= 0) $record->update(['resto' => $nuevo_resto]);
                                    else {
                                        $record->update(['resto' => 0]);
                                        $resto_a_grabar = $nuevo_resto;
                                    }   
                                }
                            } 
                            //y finalmente a las compras en donde 'cantidad' sea igual a 'resto'
                            if ($resto_a_grabar < 0) { 
                                $record = Peps::where('producto_id', $i->producto_id)->where('mov_stock_id', 2)
                                    ->whereColumn('cantidad', 'resto')->where('comercio_id', $this->comercioId)
                                    ->orderBy('id')->get(); 
                                if ($record->count() > 0) {                       
                                    foreach ($record as $i) {
                                        $resto_a_grabar = $resto_a_grabar * -1;
                                        $nuevo_resto = $i->resto - $resto_a_grabar; 
                                        if ($nuevo_resto > 0){            
                                            $i->update(['resto' => $nuevo_resto]);
                                            break;
                                        }else {
                                            $i->update(['resto' => 0]);
                                            $resto_a_grabar = $nuevo_resto;
                                        }   
                                    }
                                }
                            }
                            //modifico o creo el movimiento que indica la VENTA CON STOCK
                            $record = Peps::where('det_venta_id', $detalleVentaId)->where('producto_id', $i->producto_id)
                                ->where('comercio_id', $this->comercioId)->where('mov_stock_id', 3)->first();                            
                            if ($record) { 
                                $cantidad_con_stock = $record->cantidad - $cantidad_mov_venta_con_stock;
                                $record->update(['cantidad' => $cantidad_con_stock]);
                            } else {
                                $cantidad_mov_venta_con_stock = $cantidad_mov_venta_con_stock * -1;
                                $peps = Peps::create([
                                    'mov_stock_id' => 3,
                                    'det_venta_id' => $detalleVentaId,
                                    'producto_id'  => $i->producto_id,
                                    'cantidad'     => $cantidad_mov_venta_con_stock,
                                    'user_id'      => $this->userId,
                                    'comercio_id'  => $this->comercioId
                                ]); 
                            } 
                        } else {  //si no tengo stock del ingrediente
                            $cantidad_mov_venta_con_stock = 0;
                            if ($stock > 0) { //descuento lo que pueda del resto existente
                                $record = Peps::where('producto_id', $i->producto_id)->where('resto', '>', 0)
                                    ->where('comercio_id', $this->comercioId)->orderBy('id')->get();
                                foreach ($record as $j) {                        
                                    $diferencia = $resto_a_grabar - $j->resto;              
                                    $resto_a_grabar = $diferencia;                     
                                    $cantidad_mov_venta_con_stock +=  $j->resto;
                                    $j->update(['resto' => 0]);   
                                   
                                }   
                                //modifico o creo el movimiento que indica la VENTA CON STOCK
                                $record = Peps::where('det_venta_id', $detalleVentaId)->where('producto_id', $i->producto_id)
                                    ->where('comercio_id', $this->comercioId)->where('mov_stock_id', 3)->first();                            
                                if ($record) {
                                    $cantidad_con_stockd->update(['cantidad' => $cantidad_con_stock]);
                                } else {
                                    $cantidad_mov_venta_ = $record->cantidad - $cantidad_mov_venta_con_stock;
                                    $recorcon_stock = $cantidad_mov_venta_con_stock * -1;
                                    $peps = Peps::create([
                                        'mov_stock_id' => 3,
                                        'det_venta_id' => $detalleVentaId,
                                        'producto_id'  => $i->producto_id,
                                        'cantidad'     => $cantidad_mov_venta_con_stock,
                                        'user_id'      => $this->userId,
                                        'comercio_id'  => $this->comercioId
                                    ]); 
                                }   
                            }           
                            //registro el movimiento que indica la VENTA SIN STOCK
                            $record = Peps::where('det_venta_id', $detalleVentaId)->where('producto_id', $i->producto_id)
                                ->where('comercio_id', $this->comercioId)->where('mov_stock_id', 8)->first();
                            if ($record) { 
                                $cantidad_peps = $record->cantidad - $resto_a_grabar;
                                $resto = $record->resto - $resto_a_grabar;
                                $record->update([
                                    'cantidad'        => $cantidad_peps,
                                    'resto'           => $resto,
                                    'costo_historico' => $costoHistorico
                                ]);
                            } else {  
                                $resto_a_grabar = $resto_a_grabar * -1;                                       
                                Peps::create([ //creo el movimiento con la cantidad que reste descontar
                                    'mov_stock_id'    => 8,
                                    'det_venta_id'    => $detalleVentaId,
                                    'producto_id'     => $i->producto_id,
                                    'cantidad'        => $resto_a_grabar,
                                    'resto'           => $resto_a_grabar,
                                    'costo_historico' => $costoHistorico,
                                    'user_id'         => $this->userId,
                                    'comercio_id'     => $this->comercioId
                                ]);
                            }
                        }  
                    }
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
        }
    }
    public function descontarVentaProducto($productoId, $cantidad, $detalleVentaId)
    {   //al descontar una venta, debo AGREGAR STOCK
        DB::beginTransaction();
        try { 
            //primero DESCUENTO del detalleVentaiD
            $detalleDeVenta = Peps::where('producto_id', $productoId)
                ->where('comercio_id', $this->comercioId)
                ->where('det_venta_id', $detalleVentaId)
                ->where('mov_stock_id', 3)
                ->select('id', 'cantidad')->first();
            if ($detalleDeVenta) {                                 
                $nueva_cantidad = $detalleDeVenta->cantidad + $cantidad;  
                if ($nueva_cantidad < 0) $detalleDeVenta->update(['cantidad' => $nueva_cantidad]);
                else $detalleDeVenta->delete();
            } 
            //luego AGREGO STOCK a alguna compra con resto < cantidad (compras ordenadas desc)  
            //SE AGREGA STOCK A COMPRAS CON MERMA INCLUIDA
            $compras = Peps::where('producto_id', $productoId)
                ->where('comercio_id', $this->comercioId)
                ->whereColumn('resto', '<', 'cantidad')
                ->where('mov_stock_id', 2)
                ->select('id', 'cantidad','resto')->orderBy('id', 'desc')->get();
            if ($compras->count() > 0) {
                foreach ($compras as $i) {
                    $nuevo_resto = $i->resto + $cantidad;
                    if ($nuevo_resto <= $i->cantidad) {
                        $i->update(['resto' => $nuevo_resto]);
                        $cantidad = 0;
                    } else {
                        $i->update(['resto' => $i->cantidad]);
                        $cantidad = $nuevo_resto - $i->cantidad;
                    }
                }
            }
            //si no hay compras o están igualadas en cantidad y resto, se lo agrego a la EI
            if ($cantidad > 0) {                
                $exIni = Peps::where('producto_id', $productoId)
                    ->where('comercio_id', $this->comercioId)
                    ->where('mov_stock_id', 1)
                    ->select('id', 'cantidad','resto')->first();
                if ($exIni) {
                    $nuevo_resto = $exIni->resto + $cantidad;
                    if ($nuevo_resto <= $exIni->cantidad) {
                        $exIni->update(['resto' => $nuevo_resto]);
                    } else {
                        $exIni->update([
                            'cantidad' => $nuevo_resto, 
                            'resto' => $nuevo_resto
                        ]);
                    }
                }
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
        } 
              
    }
    public function verificarStockTrait($cantidad, $productoId)
    {
        //agrego merma del producto
        $producto = Producto::find($productoId);
        if ($producto->merma > 0) $cantidad = $cantidad + (($cantidad * $producto->merma)/100);
        //verifico si posee algún resto que no sea nulo, en ese caso significa que NO ES NULO
        $peps = Peps::where('producto_id', $productoId)->where('comercio_id', $this->comercioId)
                    ->where('resto', '<>', null)->get();  
        if ($peps->count() > 0) { //si NO ES NULO
            $peps = Peps::where('producto_id', $productoId)->where('comercio_id', $this->comercioId)
                        ->sum('resto');
            if ($peps >= $cantidad) {
                return [6];  //stock disponible => verSalsaGuarnicion()  } else return [4];            
            } else {
                if ($producto->tiene_receta == 'si') return [5]; //stock no disponible SIN opción
                else return [4]; //stock no disponible CON opción
            }
        } else {     // si el stock ES NULO, veo si tiene receta
            if($producto->tiene_receta == 'si'){ //si tiene receta, verifico stock de sus ingredientes
                $receta = Receta::where('producto_id', $productoId)->where('comercio_id', $this->comercioId)->select('id')->first();
                if($receta){                            
                    $principal = DetReceta::join('productos as p', 'p.id', 'det_recetas.producto_id')
                        ->where('det_recetas.comercio_id', $this->comercioId)
                        ->where('det_recetas.receta_id', $receta->id)
                        ->where('det_recetas.principal', 'si')                                    
                        ->select('det_recetas.*','p.descripcion', 'p.merma')->get();
                    $array=[];
                    if($principal->count() > 0){
                        foreach ($principal as $i) {
                            $stock_local = 0;
                            $stock = Peps::where('producto_id', $i->producto_id)->where('comercio_id', $this->comercioId)->sum('resto'); 
                            $stock_local = $stock;
                            //agrego merma de los ingredientes
                            if ($i->merma > 0) $cantidad_real = $i->cantidad + (($i->cantidad * $i->merma)/100);
                            else $cantidad_real = $i->cantidad; 
                            $cantidad_real = $cantidad * $cantidad_real; //cantidad de productos vendidos * cantidad de receta c/merma

                            //verifico stock                          
                            if($stock_local < $cantidad_real){
                                 $array[] = [
                                    "stock"          => $stock, 
                                    "unidadDeMedida" => $i->unidad_de_medida,
                                    "descripcion"    => $i->descripcion,
                                    "productoId"     => $productoId
                                ];                                            
                            } 
                        }
                        if(!empty($array)){
                            return [2,$array,$productoId]; //stock receta no disponible                                               
                        } else return [6]; //stock disponible => verSalsaGuarnicion()
                    }else return [1]; //receta sin principal
                } else return [3];  //receta sin detalle
            } else return [4];  //stock no disponible CON opción
        } 
        return [6]; //stock disponible => verSalsaGuarnicion()      
    }    
    public function verificarStockRecetaTrait($cantidad, $productoId)
    {
        $producto = Producto::find($productoId); 

        $receta = Receta::where('producto_id', $productoId)->where('comercio_id', $this->comercioId)->select('id')->first();
        if($receta){                            
            $principal = DetReceta::join('productos as p', 'p.id', 'det_recetas.producto_id')
                ->where('det_recetas.comercio_id', $this->comercioId)
                ->where('det_recetas.receta_id', $receta->id)
                ->where('det_recetas.principal', 'si')                                    
                ->select('det_recetas.*','p.descripcion', 'p.merma')->get();
            $array=[];
            if($principal->count() > 0){
                foreach ($principal as $i) {
                    $stock_local = 0;
                    $stock = Peps::where('producto_id', $i->producto_id)->where('comercio_id', $this->comercioId)->sum('resto'); 
                    $stock_local = $stock;
                    //agrego merma de los ingredientes
                    if ($i->merma > 0) $cantidad_real = $i->cantidad + (($i->cantidad * $i->merma)/100);
                    else $cantidad_real = $i->cantidad; 
                    $cantidad_real = $cantidad * $cantidad_real; //cantidad de productos vendidos * cantidad de receta c/merma

                    //verifico stock                          
                    if($stock_local < $cantidad_real){
                            $array[] = [
                            "stock"          => $stock, 
                            "unidadDeMedida" => $i->unidad_de_medida,
                            "descripcion"    => $i->descripcion,
                            "productoId"     => $productoId
                        ];                                            
                    } 
                }
                if(!empty($array)){
                    return [2,$array,$productoId]; //stock receta no disponible                                               
                } else return [6]; //stock disponible 
            }else return [1]; //receta sin principal
        } else return [3];  //receta sin detalle
    }  
}

