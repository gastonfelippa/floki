<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ArqueoGral;
use App\Models\CajaUsuario;
use App\Models\Cliente;
use App\Models\Comercio;
use App\Models\Ctacte;
use App\Models\Detfactura;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\Vianda;
use App\Models\ViandasContado;
use App\Models\User;
use Carbon\Carbon;
use DB;

class ViandasController extends Component
{

    public $comercioId, $arqueoGralId, $estadoAqueoGral ,$forzar_arqueo, $action='1';
    public $fecha = null, $check, $producto, $dia, $repartidor = 'Elegir', $cantidad_a_preparar, $cantidad_grabadas;
    public $numero, $cliente_id, $importe, $factura_id, $producto_id, $cantidad, $precio, $nro_arqueo;
    public $caja_abierta, $mostrar_facturas = true;
    public $estado, $estado_pago, $estado_entrega = '3';
    public $f_de_pago = null, $nro_comp_pago = null, $comentarioPago = '', $mercadopago = null;
    public $total_factura;

    public function render()
    {
         //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        session(['facturaPendiente' => null]);  
        //necesario para controlar que no se grabe más de una vez la misma vianda
        $this->arqueoGralId = session('idArqueoGral');
        $this->estadoAqueoGral = session('estadoArqueoGral');      
        if($this->estadoAqueoGral == 'pendiente') $this->forzar_arqueo = 1;
   
        //vemos si tenemos una caja habilitada con nuestro comercioId
        //este dato lo utizamos a la hora de Ver lista Facturas
        //además de ver si hay que hacer el arqueo gral.
        $caja_abierta = CajaUsuario::join('usuario_comercio as uc', 'uc.usuario_id', 'caja_usuarios.caja_usuario_id')
            ->where('uc.comercio_id', $this->comercioId)
            ->where('caja_usuarios.estado', '1')->select('caja_usuarios.*')->get();
        $this->caja_abierta = $caja_abierta->count();
        if($caja_abierta->count()){    
            //busca si hay que hacer el arqueo gral.
            if($this->arqueoGralId == 0){     //debe hacer el arqueo gral.
                return view('arqueodecaja');
            }
        }
        
        $productos = Producto::select()->where('comercio_id', $this->comercioId)->orderBy('descripcion', 'asc')->get();

        //muestro todas las Cajas habilitadas
        $repartidores = User::join('model_has_roles as mhr', 'mhr.model_id', 'users.id')
            ->join('roles as r', 'r.id', 'mhr.role_id')
            ->join('caja_usuarios as cu', 'cu.caja_usuario_id', 'users.id')
            ->where('r.comercio_id', $this->comercioId)
            ->where('cu.estado', '1')
            ->select('users.*')->orderBy('apellido')->get();

        if($this->repartidor != 'Elegir') $this->emit('mostrar_vista_facturas');

        $diaDeLaSemana = '';
        if($this->fecha != null) $diaDeLaSemana = $this->fecha;
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
        //trae info para las vistas de Cocina y Comentarios
        $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
            ->join('productos as p', 'p.id', 'viandas.producto_id')
            ->where('c.vianda', '1')
            ->where('viandas.c_'. $this->dia .'_m', '<>', '')
            ->where('c.comercio_id', $this->comercioId)
            ->select('viandas.c_'. $this->dia .'_m as cantidad','viandas.h_'. $this->dia .'_m as hora', 'viandas.comentarios',
            'c.id as cliente_id', 'c.apellido', 'c.nombre', 'p.descripcion', 'p.precio_venta_l1', 
            DB::RAW("'' as importe"))->orderBy('h_'. $this->dia .'_m')->get(); 
        $this->cantidad_a_preparar = 0;
        foreach ($info as $i){
            $i->importe = $i->cantidad * $i->precio_venta_l1;
            $this->cantidad_a_preparar = $this->cantidad_a_preparar + $i->cantidad;
        }
        //dd($info);
        //treae info para la vista Facturas
        $info2 = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
            ->join('productos as p', 'p.id', 'viandas.producto_id')
            ->where('c.vianda', '1')
            ->where('viandas.c_'. $this->dia .'_m', '<>', '')
            ->where('c.comercio_id', $this->comercioId)
            ->select('viandas.c_'. $this->dia .'_m as cantidad','viandas.h_'. $this->dia .'_m as hora', 'viandas.comentarios',
            'c.id as cliente_id', 'c.apellido', 'c.nombre', 'p.id', 'p.descripcion', 'p.precio_venta_l1', 
            DB::RAW("'' as importe"), DB::RAW("'' as habilitar_facturas"), DB::RAW("'' as estado"))->orderBy('c.apellido')->get(); 
      
            $this->cantidad_grabadas = 0;
            foreach ($info2 as $i){
            $i->importe = $i->cantidad * $i->precio_venta_l1;      //calculo el importe de cada factura
            //verifico si no se hizo esta factura en el Arqueo Gral actual para no repetir la acción
            $record = ViandasContado::where('cliente_id', $i->cliente_id) 
                ->where('arqueo_gral_id', $this->arqueoGralId)->get();   
            if($record->count()){
                $i->habilitar_facturas = 0; 
                $i->estado = $record[0]->estado;
                $this->cantidad_grabadas = $this->cantidad_grabadas + $i->cantidad;   
            }                       
            else $i->habilitar_facturas = 1;
        }
        return view('livewire.viandas.component', [
            'info'         => $info,
            'info2'        => $info2,
            'productos'    => $productos,
            'repartidores' => $repartidores
        ]);
    }    
    protected $listeners = [       
        'grabar'              =>'grabar',         
        'cambiarFecha'        =>'cambiarFecha',         
        'createFactFromModal' => 'createFactFromModal',
        'factura_contado'     => 'factura_contado',         
        'factura_ctacte'      => 'factura_ctacte',
        'elegirFormaDePago'   => 'elegirFormaDePago',
        'enviarDatosPago'     => 'enviarDatosPago',    
        'doAction'            => 'doAction'
    ];
    public function doAction($action)
	{
        $this->action = $action;
    }
    public function resetInput()
    {
        $this->repartidor     = 'Elegir';
        $this->f_de_pago      = null;
        $this->nro_comp_pago  = null;
        $this->comentarioPago = '';
        $this->mercadopago    = null;
        $this->forzar_arqueo  = 0;
        $this->action         = 1;
    }
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
                         'p.precio_venta_l1', DB::RAW("'' as importe"))->get(); 
            foreach ($info as $i){
                $i->importe=$i->cantidad * $i->precio_venta_l1;
            } 
            //busco el nro_arqueo del repartidor
            $nroArqueo = CajaUsuario::where('caja_usuarios.caja_usuario_id', $this->repartidor)
                ->where('caja_usuarios.estado', '1')->get();
            $this->nro_arqueo = $nroArqueo[0]->id;                     
   
            foreach($info as $i){                
                if (in_array($i->cliente_id, $data)) {
                    $primerFactura = Factura::where('comercio_id', $this->comercioId)->get();
                    if(!$primerFactura->count()){
                        $numFactura = 1;
                    }else{
                        $encabezado = Factura::select('facturas.numero')
                                    ->where('comercio_id', $this->comercioId)
                                    ->orderBy('facturas.numero', 'desc')->get();                             
                        $numFactura = $encabezado[0]->numero + 1;
                    }        
                    $factura = Factura::create([
                        'numero'         => $numFactura,
                        'cliente_id'     => $i->cliente_id,
                        'repartidor_id'  => $this->repartidor,
                        'user_id'        => auth()->user()->id,
                        'importe'        => $i->importe,
                        'estado'         => 'ctacte',
                        'estado_pago'    => '0',
                        'estado_entrega' => $this->estado_entrega,
                        'comercio_id'    => $this->comercioId,
                        'arqueo_id'      => $this->nro_arqueo   //nro. de arqueo de caja de quien cobra la factura
                    ]);
                    Detfactura::create([         //creamos un nuevo detalle
                        'factura_id'  => $factura->id,
                        'producto_id' => $i->producto_id,
                        'cantidad'    => $i->cantidad,
                        'precio'      => $i->precio_venta_l1,
                        'comercio_id' => $this->comercioId
                    ]);	
                    Ctacte::create([
                        'cliente_id' => $i->cliente_id,
                        'factura_id' => $factura->id,
                        'estado'     => '1'
                    ]);                    
                    ViandasContado::create([             //guardamos la factura cobrada 
                        'factura_id'     => $factura->id,   //para no repetir la acción en el mismo Arqueo Gral
                        'cliente_id'     => $i->cliente_id,
                        'arqueo_gral_id' => $this->arqueoGralId,
                        'estado'         => 'Cta cte'
                    ]);
                    $record = Cliente::find($i->cliente_id);//marca que el cliente tiene un saldo en ctacte
                    $record->update([ 'saldo' => '1' ]);               
                }
            }
            DB::commit();
            $this->emit('facturasCreateCtaCte');
        }catch (\Exception $e){
            DB::rollback();    
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! Las Facturas no se grabaron...');
        }
        $this->resetInput();
        return;          
    }   
    public function cambiarFecha($data)  //esta función inhabilita la vista 'Ver Lista Facturas' 
    {                                    //cuando estamos fuera del Arqueo Gral activo
        $dia_consulta = Carbon::parse($data)->format('Y-m-d');
        $this->fecha = date('w',strtotime($data));

        //averiguamos la hora de apertura del comercio
        $horaApertura = Comercio::select('hora_apertura')
            ->where('id', $this->comercioId)->first();
        $hora_apertura = $horaApertura->hora_apertura;
    
        //obtenemos la fecha de creación del Arqueo Gral actual
        $idArqueoGral = ArqueoGral::where('estado', '1')
            ->where('comercio_id', $this->comercioId)->get();
        if($idArqueoGral->count()){
            $date = Carbon::parse($idArqueoGral[0]->created_at);
            $dia_arqueo = Carbon::parse($date)->format('Y-m-d');          

            //obtenemos la fecha de consulta elegida para compararla con la del Arqueo anterior
            //$dia_consulta = Carbon::parse($fecha_consulta)->format('Y-m-d');

            if($dia_consulta > $dia_arqueo) {
                $now = $dia_consulta . ' 23:59:59';       //si es mayor, tomamos el final del día
            }else{
                $now = $dia_consulta . ' 00:00:00';       //si es menor, tomamos el inicio del día
            }        
            $diff = $date->diffInDays($now);              //obtenemos la diferencia en días
            $hora_actual = Carbon::now()->format('H:i');
            //si estamos en el mismo día 'o' es el día siguiente 
            //y 'no' es más tarde que la hora de apertura del comercio
            //permitimos grabar viandas
            if($diff == 0 || $diff == 1 && $hora_actual <= $hora_apertura){
                $this->mostrar_facturas = true; 
            }else{    //sino, no permitimos grabar viandas
                $this->mostrar_facturas = false;
            }
        }
        $this->emit('mostrar_viandas');

    }       
    public function createFactFromModal($infoMod)
    {
        $data = json_decode($infoMod);

        //busco el nro_arqueo del repartidor
        $nroArqueo = CajaUsuario::where('caja_usuarios.caja_usuario_id', $this->repartidor)
            ->where('caja_usuarios.estado', '1')->get();
        $this->nro_arqueo = $nroArqueo[0]->id; 

        DB::begintransaction();                 
        try{
            $preVta = Producto::select('precio_venta_l1')->where('id', $data->producto_id)->get();
            $importe = 0;
            $importe = $data->cantidad * $preVta[0]->precio_venta_l1;            
           
            $primerFactura = Factura::select('*')->where('comercio_id', $this->comercioId)->get();                       
            if(!$primerFactura->count()){
                $numFactura = 1;
            }else{
                $ultimaFactura = Factura::select('numero')
                                    ->where('comercio_id', $this->comercioId)
                                    ->withTrashed()
                                    ->orderBy('numero', 'desc')->get();                             
                $numFactura = $ultimaFactura[0]->numero + 1;
            }   

            $factura = Factura::create([
                'numero'         => $numFactura,
                'cliente_id'     => $data->cliente_id,
                'repartidor_id'  => $this->repartidor,
                'user_id'        => auth()->user()->id,
                'importe'        => $importe,
                'estado'         => 'pendiente',
                'estado_pago'    => '0',
                'estado_entrega' => $this->estado_entrega,
                'comercio_id'    => $this->comercioId,
                'arqueo_id'      => $this->nro_arqueo   //nro. de arqueo de caja de quien cobra la factura
            ]);
            Detfactura::create([         //creamos un nuevo detalle
                'factura_id'  => $factura->id,
                'producto_id' => $data->producto_id,
                'cantidad'    => $data->cantidad,
                'precio'      => $preVta[0]->precio_venta_l1,
                'comercio_id' => $this->comercioId
            ]);	
            // Ctacte::create([
            //     'cliente_id' => $data->cliente_id,
            //     'factura_id' => $factura->id
            // ]);
            ViandasContado::create([             //guardamos la factura cobrada 
                'factura_id'     => $factura->id,   //para no repetir la acción en el mismo Arqueo Gral
                'cliente_id'     => $data->cliente_id,
                'arqueo_gral_id' => $this->arqueoGralId,
                'estado'         => 'Pendiente'
            ]); 
            DB::commit();
            $this->emit('facturaPendiente'); 
        }catch (Exception $e){
            DB::rollback();      
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');           
        }         
    } 
    public function factura_contado()
    {
        $this->estado = 'contado';    
        $this->estado_pago = '1';       
        //busco solo los repartidores con caja habilitada y lo comparo con la variable $this->repartidor
        //si coinciden, debo enviar la factura como 'pendiente' y 'entregada' al Arqueo Caja Repartidor 
        //si no coincide es porque es alguna Caja que no es repartidor
        //en este caso grabamos la factura como 'contado' y 'pagada' al Arqueo de Caja del Usuario seleccionado
        //con los datos de las variables inicializadas anteriormente     
        $repartidores = User::join('model_has_roles as mhr', 'mhr.model_id', 'users.id')
            ->join('roles as r', 'r.id', 'mhr.role_id')
            ->join('caja_usuarios as cu', 'cu.caja_usuario_id', 'users.id')
            ->where('r.alias', 'Repartidor')
            ->where('r.comercio_id', $this->comercioId)
            ->where('cu.estado', '1')
            ->select('users.id')->orderBy('apellido')->get();
     
        foreach($repartidores as $r){
            if($r->id == $this->repartidor){
                $this->estado = 'pendiente';    
                $this->estado_pago = '0';    
            }
        }
         
        //busco el nro_arqueo del repartidor
        $nroArqueo = CajaUsuario::where('caja_usuarios.caja_usuario_id', $this->repartidor)
            ->where('caja_usuarios.estado', '1')->get();
        $this->nro_arqueo = $nroArqueo[0]->id;

        DB::begintransaction();
        try{            
            $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                ->join('productos as p', 'p.id', 'viandas.producto_id')
                ->where('c.vianda', '1')
                ->where('viandas.c_'. $this->dia .'_m', '<>', '')
                ->where('c.comercio_id', $this->comercioId)
                ->select('viandas.c_'. $this->dia .'_m as cantidad', 'c.id as cliente_id', 'p.id as producto_id',
                         'p.precio_venta_l1', DB::RAW("'' as importe"))->get();
            foreach($info as $i){
                if ($i->cliente_id == $this->cliente_id) {
                    $i->importe=$i->cantidad * $i->precio_venta_l1;
                    $primerFactura = Factura::select('*')->where('comercio_id', $this->comercioId)->get();                       
                    if(!$primerFactura->count()){
                         $numFactura = 1;
                    }else{
                        $encabezado = Factura::select('facturas.numero')
                                        ->where('comercio_id', $this->comercioId)
                                        ->orderBy('facturas.numero', 'desc')->get();                             
                        $numFactura = $encabezado[0]->numero + 1;
                    }                                    
                    $factura = Factura::create([
                        'numero'         => $numFactura,
                        'cliente_id'     => $i->cliente_id,
                        'repartidor_id'  => $this->repartidor,
                        'importe'        => $i->importe,
                        'estado'         => $this->estado,
                        'estado_pago'    => $this->estado_pago,
                        'estado_entrega' => $this->estado_entrega,
                        'user_id'        => auth()->user()->id,
                        'comercio_id'    => $this->comercioId,
                        'arqueo_id'      => $this->nro_arqueo,   //nro. de arqueo de caja de quien cobra la factura                        
                        'forma_de_pago'  => $this->f_de_pago,
                        'nro_comp_pago'  => $this->nro_comp_pago,  //nro ticket tarjeta o nro transferencia
                        'mercadopago'    => $this->mercadopago,
                        'comentario'     => $this->comentarioPago
                    ]);
                    Detfactura::create([         //creamos un nuevo detalle
                        'factura_id'  => $factura->id,
                        'producto_id' => $i->producto_id,
                        'cantidad'    => $i->cantidad,
                        'precio'      => $i->precio_venta_l1,
                        'comercio_id' => $this->comercioId
                    ]);	
                    ViandasContado::create([             //guardamos la factura cobrada 
                        'factura_id'     => $factura->id,   //para no repetir la acción en el mismo Arqueo Gral
                        'cliente_id'     => $i->cliente_id,
                        'arqueo_gral_id' => $this->arqueoGralId,
                        'estado'         => 'Cobrada'
                    ]);	
                }
            }      
            DB::commit();
            $this->emit('facturaCobrada');
        }catch (\Exception $e){
            DB::rollback();    
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! La Factura no se grabó...');
        }
        $this->resetInput();
        return;
    }
    public function factura_ctacte($cliId)
    {
        //busco el nro_arqueo del repartidor
        $nroArqueo = CajaUsuario::where('caja_usuarios.caja_usuario_id', $this->repartidor)
            ->where('caja_usuarios.estado', '1')->get();
        $this->nro_arqueo = $nroArqueo[0]->id;

        DB::begintransaction();    
        try{ 
            $info = Vianda::join('clientes as c', 'c.id', 'viandas.cliente_id')
                ->join('productos as p', 'p.id', 'viandas.producto_id')
                ->where('c.vianda', '1')
                ->where('viandas.c_'. $this->dia .'_m', '<>', '')
                ->where('c.comercio_id', $this->comercioId)
                ->select('viandas.c_'. $this->dia .'_m as cantidad', 'c.id as cliente_id', 'p.id as producto_id',
                         'p.precio_venta_l1', DB::RAW("'' as importe"))->get();

            foreach($info as $i){
                if ($i->cliente_id == $cliId) {
                    $i->importe=$i->cantidad * $i->precio_venta_l1;
                    $primerFactura = Factura::where('comercio_id', $this->comercioId)->get();                       
                    if(!$primerFactura->count()){
                         $numFactura = 1;
                    }else{
                        $encabezado = Factura::select('facturas.numero')
                                        ->where('comercio_id', $this->comercioId)
                                        ->orderBy('facturas.numero', 'desc')->get();                             
                        $numFactura = $encabezado[0]->numero + 1;
                    }                
                    $factura = Factura::create([
                        'numero'         => $numFactura,
                        'cliente_id'     => $i->cliente_id,
                        'repartidor_id'  => $this->repartidor,
                        'importe'        => $i->importe,
                        'estado'         => 'ctacte',
                        'estado_pago'    => '0',
                        'estado_entrega' => $this->estado_entrega,
                        'user_id'        => auth()->user()->id,
                        'comercio_id'    => $this->comercioId,
                        'arqueo_id'      => $this->nro_arqueo   //nro. de arqueo de caja de quien cobra la factura
                    ]);
                    Detfactura::create([         //creamos un nuevo detalle
                        'factura_id'  => $factura->id,
                        'producto_id' => $i->producto_id,
                        'cantidad'    => $i->cantidad,
                        'precio'      => $i->precio_venta_l1,
                        'comercio_id' => $this->comercioId
                    ]);	
                    Ctacte::create([
                        'cliente_id' => $i->cliente_id,
                        'factura_id' => $factura->id,
                        'estado'     => '1'
                    ]);
                    ViandasContado::create([             //guardamos la factura cobrada 
                        'factura_id'     => $factura->id,   //para no repetir la acción en el mismo Arqueo Gral
                        'cliente_id'     => $i->cliente_id,
                        'arqueo_gral_id' => $this->arqueoGralId,
                        'estado'         => 'Cta cte'
                    ]);
                }
            }
            DB::commit();
            $this->emit('facturaCtaCte');     
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! La Factura no se grabó...');
        }
        $this->resetInput();
        return;
    } 
    public function elegirFormaDePago($idCli, $total)
    {
        $this->cliente_id = $idCli;
        $this->total_factura = $total;
        $this->f_de_pago = '1';        
        $this->doAction(2);
    }
    public function enviarDatosPago($tipo,$nro)
    {
        $this->f_de_pago = $tipo;
        $this->nro_comp_pago = $nro;
    }
}
