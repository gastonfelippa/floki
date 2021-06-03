<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Ctacte;
use App\Models\CajaUsuario;
use App\Models\Detfactura;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\Vianda;
use App\Models\User;
use Carbon\Carbon;
use DB;

class ViandasController extends Component
{

    public $comercioId, $fecha, $check, $producto, $dia, $repartidor = 'Elegir', $estado_entrega = '0';
    public $numero, $cliente_id, $importe, $factura_id, $producto_id, $cantidad, $precio, $nro_arqueo;

    public function render()
    {
         //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');

        //vemos si tenemos una caja habilitada con nuestro user_id
        // $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', auth()->user()->id)
        //     ->where('caja_usuarios.estado', '1')->select('caja_usuarios.*')->get();
        // $this->caja_abierta = $caja_abierta->count();
        // if($caja_abierta->count() > 0){
        //     $this->nro_arqueo = $caja_abierta[0]->id;  //este es el nro_arqueo del cajero, pero puede cambiar por el del delivery
        //     $this->fecha_inicio = $caja_abierta[0]->created_at;  
        // }

        $productos = Producto::select()->where('comercio_id', $this->comercioId)->orderBy('descripcion', 'asc')->get();
        $repartidores = User::join('model_has_roles as mhr', 'mhr.model_id', 'users.id')
            ->join('roles as r', 'r.id', 'mhr.role_id')
            ->where('r.alias', 'Repartidor')
            ->where('r.comercio_id', $this->comercioId)
            ->select('users.*')->orderBy('apellido')->get();
        
        $diaDeLaSemana = '';
        if($this->fecha != '') $diaDeLaSemana = $this->fecha;
        else $diaDeLaSemana = date('w');
        switch ($diaDeLaSemana) {
            case '1': $this->dia = 'lunes'; break;
            case '2': $this->dia = 'martes'; break;
            case '3': $this->dia = 'miercoles'; break;
            case '4': $this->dia = 'jueves'; break;
            case '5': $this->dia = 'viernes'; break;
            case '6': $this->dia = 'sabado'; break;
            case '0': $this->dia = 'domingo'; break;
            default:
        }
        
        $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
            ->join('productos as p', 'p.id', 'viandas.producto_id')
            ->where('c.vianda', '1')
            ->where('viandas.c_'. $this->dia .'_m', '<>', '')
            ->where('c.comercio_id', $this->comercioId)
            ->select('viandas.c_'. $this->dia .'_m as cantidad','viandas.h_'. $this->dia .'_m as hora', 'viandas.comentarios',
            'c.id as cliente_id', 'c.apellido', 'c.nombre', 'p.descripcion', 'p.precio_venta', 
            DB::RAW("'' as importe"))->orderBy('h_'. $this->dia .'_m')->get(); 
        foreach ($info as $i){
            $i->importe = $i->cantidad * $i->precio_venta;
        }
        return view('livewire.viandas.component', [
            'info' => $info,
            'productos' => $productos,
            'repartidores' => $repartidores,
        ]);
    }
    
    protected $listeners = [       
        'grabar'=>'grabar',         
        'cambiarFecha'=>'cambiarFecha',         
        'createFactFromModal' => 'createFactFromModal',
        'factura_contado' => 'factura_contado',         
        'factura_ctacte' => 'factura_ctacte'         
    ];

    public function grabar($data)
    { 
        $data = json_decode($data);     

        DB::begintransaction();                 //iniciar transacción para grabar
        try{
            $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                ->join('productos as p', 'p.id', 'viandas.producto_id')
                ->where('c.vianda', '1')
                ->where('viandas.c_'. $this->dia .'_m', '<>', '')
                ->where('c.comercio_id', $this->comercioId)
                ->select('viandas.c_'. $this->dia .'_m as cantidad', 'c.id as cliente_id', 'p.id as producto_id',
                         'p.precio_venta', DB::RAW("'' as importe"))->get(); 
            foreach ($info as $i){
                $i->importe=$i->cantidad * $i->precio_venta;
            }
      // if($this->empleado == 'Elegir'){   ///modificar el nro_arqueo
                    //     $this->empleado = null;  
                    // }else{            //si es delivery, cambiamos el nro_arqueo
                    //     $this->estado_entrega = 1;    
                    $nroArqueo = CajaUsuario::where('caja_usuarios.caja_usuario_id', $this->repartidor)
                    ->where('caja_usuarios.estado', '1')->get();
                if($nroArqueo->count() > 0){
                    $this->nro_arqueo = $nroArqueo[0]->id;  //este es el nro_arqueo del repartidor
                }  
           // } 
           
            foreach($info as $i){
                if (in_array($i->cliente_id, $data)) {

                    $primerFactura = Factura::select('*')->where('comercio_id', $this->comercioId)->get();                       
                    if($primerFactura->count() == 0){
                        $numFactura = 1;
                    }else{
                        $encabezado = Factura::select('facturas.numero')
                                    ->where('comercio_id', $this->comercioId)
                                    ->orderBy('facturas.numero', 'desc')->get();                             
                        $numFactura = $encabezado[0]->numero + 1;
                    }  
                            
                    $factura = Factura::create([
                        'numero' => $numFactura,
                        'cliente_id' => $i->cliente_id,
                        'repartidor_id' => $this->repartidor,
                        'user_id' => auth()->user()->id,
                        'importe' => $i->importe,
                        'estado' => 'ctacte',
                        'estado_pago'    => '0',
                        'estado_entrega' => $this->estado_entrega,
                        'comercio_id' => $this->comercioId,
                        'arqueo_id'      => $this->nro_arqueo   //nro. de arqueo de caja de quien cobra la factura
                    ]);
                    Detfactura::create([         //creamos un nuevo detalle
                        'factura_id' => $factura->id,
                        'producto_id' => $i->producto_id,
                        'cantidad' => $i->cantidad,
                        'precio' => $i->precio_venta,
                        'comercio_id' => $this->comercioId
                    ]);	
                    Ctacte::create([
                        'cliente_id' => $i->cliente_id,
                        'factura_id' => $factura->id
                    ]);                
                }
            }
            DB::commit();
            session()->flash('message', 'Facturas de Viandas creadas exitosamente!!');
            return;
        }catch (\Exception $e){
            DB::rollback();    
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! Las Facturas no se grabaron...');
            return;          
        }
    }    
        
    public function cambiarFecha($data)
    {
        if($data != '') $this->fecha = date('w',strtotime($data));
    }
       
    public function createFactFromModal($infoMod)
    {
        $data = json_decode($infoMod);

        DB::begintransaction();                 //iniciar transacción para grabar
        try{
            $preVta = Producto::select('precio_venta')->where('id', $data->producto_id)->get();
            $importe = 0;
            $importe = $data->cantidad * $preVta[0]->precio_venta;            
           
            $primerFactura = Factura::select('*')->where('comercio_id', $this->comercioId)->get();                       
            if($primerFactura->count() == 0){
                $numFactura = 1;
            }else{
                $ultimaFactura = Factura::select('facturas.numero')
                                    ->where('comercio_id', $this->comercioId)
                                    ->orderBy('facturas.numero', 'desc')->get();                             
                $numFactura = $ultimaFactura[0]->numero + 1;
            }   

            $factura = Factura::create([
                'numero' => $numFactura,
                'cliente_id' => $data->cliente_id,
                'importe' => $importe,
                'estado' => 'ctacte',
                'user_id' => auth()->user()->id,
                'comercio_id' => $this->comercioId
            ]);
            Detfactura::create([         //creamos un nuevo detalle
                'factura_id' => $factura->id,
                'producto_id' => $data->producto_id,
                'cantidad' => $data->cantidad,
                'precio' => $preVta[0]->precio_venta,
                'comercio_id' => $this->comercioId
            ]);	
            Ctacte::create([
                'cliente_id' => $data->cliente_id,
                'factura_id' => $factura->id
            ]);                    
         
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();      
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');           
        } 
        session()->flash('message', 'Facturas de Viandas creadas exitosamente!!');
    } 

    public function factura_contado($cliId)
    {        
        $repartidor = User::where('id', $this->repartidor)->first();
        if($repartidor['name'] == '...') $estado = 'contado'; else $estado = 'pendiente';

        DB::begintransaction();
        try{
            $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                ->join('productos as p', 'p.id', 'viandas.producto_id')
                ->where('c.vianda', '1')
                ->where('viandas.c_'. $this->dia .'_m', '<>', '')
                ->where('c.comercio_id', $this->comercioId)
                ->select('viandas.c_'. $this->dia .'_m as cantidad', 'c.id as cliente_id', 'p.id as producto_id',
                         'p.precio_venta', DB::RAW("'' as importe"))->get();
            foreach($info as $i){
                if ($i->cliente_id == $cliId) {
                    $i->importe=$i->cantidad * $i->precio_venta;
                    $primerFactura = Factura::select('*')->where('comercio_id', $this->comercioId)->get();                       
                    if($primerFactura->count() == 0){
                         $numFactura = 1;
                    }else{
                        $encabezado = Factura::select('facturas.numero')
                                        ->where('comercio_id', $this->comercioId)
                                        ->orderBy('facturas.numero', 'desc')->get();                             
                        $numFactura = $encabezado[0]->numero + 1;
                    }                                    
                    $factura = Factura::create([
                        'numero' => $numFactura,
                        'cliente_id' => $i->cliente_id,
                        'repartidor_id' => $this->repartidor,
                        'importe' => $i->importe,
                        'estado' => $estado,
                        'estado_pago' => '1',
                        'user_id' => auth()->user()->id,
                        'comercio_id' => $this->comercioId
                    ]);
                    Detfactura::create([         //creamos un nuevo detalle
                        'factura_id' => $factura->id,
                        'producto_id' => $i->producto_id,
                        'cantidad' => $i->cantidad,
                        'precio' => $i->precio_venta,
                        'comercio_id' => $this->comercioId
                    ]);	
                }
            }      
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();    
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! La Factura no se creó...');
        }
        return;
    }

    public function factura_ctacte($cliId)
    {
        DB::begintransaction();    
        try{ 
            $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                ->join('productos as p', 'p.id', 'viandas.producto_id')
                ->where('c.vianda', '1')
                ->where('viandas.c_'. $this->dia .'_m', '<>', '')
                ->where('c.comercio_id', $this->comercioId)
                ->select('viandas.c_'. $this->dia .'_m as cantidad', 'c.id as cliente_id', 'p.id as producto_id',
                         'p.precio_venta', DB::RAW("'' as importe"))->get();

            foreach($info as $i){
                if ($i->cliente_id == $cliId) {
                    $i->importe=$i->cantidad * $i->precio_venta;
                    $primerFactura = Factura::select('*')->where('comercio_id', $this->comercioId)->get();                       
                    if($primerFactura->count() == 0){
                         $numFactura = 1;
                    }else{
                        $encabezado = Factura::select('facturas.numero')
                                        ->where('comercio_id', $this->comercioId)
                                        ->orderBy('facturas.numero', 'desc')->get();                             
                        $numFactura = $encabezado[0]->numero + 1;
                    }                
                    $factura = Factura::create([
                        'numero' => $numFactura,
                        'cliente_id' => $i->cliente_id,
                        'repartidor_id' => $this->repartidor,
                        'importe' => $i->importe,
                        'estado' => 'ctacte',
                        'estado_pago' => '0',
                        'user_id' => auth()->user()->id,
                        'comercio_id' => $this->comercioId
                    ]);
                    Detfactura::create([         //creamos un nuevo detalle
                        'factura_id' => $factura->id,
                        'producto_id' => $i->producto_id,
                        'cantidad' => $i->cantidad,
                        'precio' => $i->precio_venta,
                        'comercio_id' => $this->comercioId
                    ]);	
                    Ctacte::create([
                        'cliente_id' => $cliId,
                        'factura_id' => $factura->id
                    ]);
                }
            }
            DB::commit();     
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! La Factura no se grabó...');
        }
        return;
    }
    
    public function contar_viandas($data)
    {
        $data = json_decode($data);
        $this->cViandas = $data->cantidad;
    }
}
