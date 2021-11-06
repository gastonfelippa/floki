<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\CajaUsuario;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Comanda;
use App\Models\Ctacte;
use App\Models\Detcomanda;
use App\Models\Detfactura;
use App\Models\Empleado;
use App\Models\Factura;
use App\Models\Guarnicion;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\Salsa;
use App\Models\User;
use Carbon\Carbon;
use DB;

class FacturaBarController extends Component
{
	//properties
    public $cantidad = 1, $precio, $estado='ABIERTO', $inicio_factura, $mostrar_datos;
    public $cliente="Elegir", $empleado="Elegir", $producto="Elegir", $mesa="Elegir", $mozo="Elegir", $salon =null;
    public $clientes, $empleados, $productos, $mesas, $mozos;
    public $selected_id = null, $search, $numFactura, $action = 1;
    public $facturas,  $total, $importe, $totalAgrabar, $delivery = 0;  
    public $grabar_encabezado = true, $modificar, $codigo, $barcode;
    public $comercioId, $arqueoGralId, $factura_id, $categorias, $articulos =null, $saldoCtaCte, $saldoACobrar;
    public $dirCliente, $apeNomCli, $apeNomRep, $clienteId;
    public $comentario, $nro_arqueo, $fecha_inicio, $caja_abierta, $estado_entrega = '0';
    public $f_de_pago = null, $nro_comp_pago = null, $comentarioPago = '', $mercadopago = null;
    public $estadoAqueoGral, $forzar_arqueo = 0, $ultima_factura = 0;
    public $salsas, $guarniciones, $salsa = 0, $guarnicion = 0, $texto_base = null, $comentario_comanda='null';
    public $comanda_id, $inicio_comanda, $sector_comanda, $texto_comanda, $cantidad_comanda;
    public $tabFactura = 'factura', $unirComandas = 'no', $estadoComanda, $tab = 'factura';
    public $mesaId, $mozoId;
	
	public function render()
	{
        // $this->facturaAfip();
 
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        session(['facturaPendiente' => null]);  
        $this->mesaId = session("idMesa");
        $this->mozo = session("idMozo");
        $mesa = Mesa::where('id', $this->mesaId)->get();
        if($mesa[0]->estado == 'Disponible') $this->mesa = $mesa[0]->descripcion;
        
//dd($this->mesaId, $this->mozoId);
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
        if($caja_abierta->count() > 0){
            $this->nro_arqueo = $caja_abierta[0]->id;  //este es el nro_arqueo del cajero, pero puede cambiar por el del delivery
            $this->fecha_inicio = $caja_abierta[0]->created_at;
            //busca si hay que hacer el arqueo gral.
            $this->arqueoGralId = session('idArqueoGral');
            if($this->arqueoGralId == 0){  //debe hacer el arqueo gral.
                return view('arqueodecaja');
            }
        }        

        $this->productos = Producto::where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        $this->clientes = Cliente::where('comercio_id', $this->comercioId)->orderBy('apellido')->get();
        $this->categorias = Categoria::where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        $this->salsas = Salsa::where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        $this->guarniciones = Guarnicion::where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        $this->mesas = Mesa::where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
    //dd($this->mesas);
        //muestro solo los mozos
        $this->mozos = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('uc.comercio_id', $this->comercioId)->select('users.*')->orderBy('apellido')->get();
   // dd($this->mozos);
        //muestro solo los repartidores que tienen caja abierta
        $this->empleados = User::join('model_has_roles as mhr', 'mhr.model_id', 'users.id')
            ->join('roles as r', 'r.id', 'mhr.role_id')
            ->join('caja_usuarios as cu', 'cu.caja_usuario_id', 'users.id')
            ->where('r.alias', 'Repartidor')
            ->where('r.comercio_id', $this->comercioId)
            ->where('cu.estado', '1')
            ->select('users.id', 'users.name', 'users.apellido')->get();
      
        //capturo el id del repartidor Salón 
        $this->salon = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('users.name', '...')
            ->where('users.apellido', 'Salón')
            ->where('uc.comercio_id', $this->comercioId)
            ->select('users.id')->get();

        if(strlen($this->barcode) > 0) $this->buscarProducto($this->barcode); 
        else $this->precio = '';        

        $dProducto = Producto::find($this->producto);
        if($dProducto != null) $this->precio = $dProducto->precio_venta_l1; 
        else $this->precio = '';
        
        $encabezado = Factura::select('*')->where('comercio_id', $this->comercioId)->withTrashed()->get(); 
        //si es la primera factura, le asigno el nro: 1
        if($encabezado->count() == 0){
            $this->numFactura = 1;
            $this->inicio_factura = true;       
        }else{  //sino, busco si hay alguna factura abierta
            $encabezado = Factura::join('mesas as m', 'm.id', 'facturas.mesa_id')
                ->where('facturas.estado','like','abierta')->where('facturas.comercio_id', $this->comercioId)
                ->select('facturas.*', 'facturas.numero as nroFact', 'm.descripcion')->get();
                //verifico si es delivery para recuperar los datos de Cli/Rep
            if($encabezado->count() > 0 && $encabezado[0]->cliente_id <> null){                
                $encabezado = Factura::join('clientes as c','c.id','facturas.cliente_id')
                    ->join('users as u','u.id','facturas.repartidor_id')
                    ->join('mesas as m','m.id','facturas.mesa_id')
                    ->where('facturas.estado','like','abierta')
                    ->where('facturas.comercio_id', $this->comercioId)
                    ->select('facturas.*', 'facturas.numero as nroFact','c.nombre as nomCli', 'c.apellido as apeCli','c.calle',
                             'c.numero', 'u.name as nomRep', 'u.apellido as apeRep', 'm.descripcion')->get();
                $this->numFactura = $encabezado[0]->nroFact;
                $this->clienteId = $encabezado[0]->cliente_id;
                $this->factura_id = $encabezado[0]->id;
                $this->mesa = $encabezado[0]->descripcion;
                $this->mozo = $encabezado[0]->mozo_id;
                $this->inicio_factura = false;
                $this->delivery = 1;
                $this->estado_entrega = $encabezado[0]->estado_entrega;
                $this->dirCliente = $encabezado[0]->calle . ' ' . $encabezado[0]->numero;
                $this->verSaldo($encabezado[0]->cliente_id);
                $this->mostrar_datos = 0;
            }elseif($encabezado->count() > 0) {
                $this->inicio_factura = false;
                $this->numFactura = $encabezado[0]->nroFact;
                $this->factura_id = $encabezado[0]->id;
                $this->mesa = $encabezado[0]->descripcion;
                $this->mozo = $encabezado[0]->mozo_id;
                $this->delivery = 0;          //dice si la factura es delivery
                $this->mostrar_datos = 0;     //muestra datos del modal, no de la BD       
            }else {                           //si no hay una factura abierta le sumo 1 a la última
                $this->inicio_factura = true;
                $encabezado = Factura::select('numero')
                    ->where('comercio_id', $this->comercioId)
                    ->withTrashed()
                    ->orderBy('numero', 'desc')->get();                             
                $this->numFactura = $encabezado[0]->numero + 1;
                $this->delivery = 0;
            }
        }
        $info = Detfactura::select('*')->where('comercio_id', $this->comercioId)->get();
        if($info->count() > 0){
            $info = Detfactura::join('facturas as f','f.id','detfacturas.factura_id')
                ->join('productos as p','p.id','detfacturas.producto_id')
                ->select('detfacturas.*', 'p.descripcion as producto', DB::RAW("'' as importe"))
                ->where('detfacturas.factura_id', $this->factura_id)
                ->where('detfacturas.comercio_id', $this->comercioId)
                ->where('f.estado', 'like', 'abierta')
                ->orderBy('detfacturas.id', 'asc')->get();  
        }    
        $this->total = 0;
        foreach ($info as $i){
            $i->importe=$i->cantidad * $i->precio;
            $this->total += $i->importe;
        }

    //COMANDAS/////

        $estadoCom = Comanda::select('*')
            ->where('factura_id', $this->factura_id)
            ->where('estado', 'cargado')->get();
        //si no hay una comanda con estado 'cargado', inicializo una
        if($estadoCom->count()){
            $this->comanda_id     = $estadoCom[0]->id;
            $this->estadoComanda  = $estadoCom[0]->estado;
            $this->inicio_comanda = false;
        }else $this->inicio_comanda = true; 

        $infoComanda = Detcomanda::join('comandas as c','c.id','detcomandas.comanda_id')
            ->join('facturas as f','f.id','c.factura_id')
            ->where('c.factura_id', $this->factura_id)
            ->where('c.estado', 'cargado')
            ->where('f.estado', 'like', 'abierta')
            ->orWhere('c.factura_id', $this->factura_id)
            ->where('c.estado', 'cargado')
            ->where('f.estado', 'like', 'pendiente')
            ->select('detcomandas.*')
            ->orderBy('detcomandas.descripcion')->get(); 

    ///////////////    

		return view('livewire.facturas.component-bar', [
            'info'        => $info,
            'encabezado'  => $encabezado,
            'infoComanda' => $infoComanda
		]);
    }
    public function doAction($action)
    {
        $this->action = $action;
    }
    public function resetInput()
    {
        $this->cantidad           = 1;
        $this->barcode            = '';
        $this->precio             = '';
        $this->producto           = 'Elegir';
        $this->selected_id        = null;
        $this->action             = 1;
        $this->search             = '';
        $this->estado_entrega     = 0;
        $this->salon              = null;
        $this->f_de_pago          = null;
        $this->nro_comp_pago      = null;
        $this->mercadopago        = null;
        $this->comentarioPago     = '';
        $this->forzar_arqueo      = 0;
        $this->salsa              = 0;
        $this->guarnicion         = 0;
        $this->texto_base         = null;
        $this->comentario_comanda = null;
    }    
    public function resetInputTodos()
    {
        $this->cantidad       = 1;
        $this->barcode        = '';
        $this->precio         = '';        
        $this->cliente        = 'Elegir';
        $this->dirCliente     = null;
        $this->empleado       = 'Elegir';
        $this->producto       = 'Elegir';
        $this->articulos      = '';
        $this->delivery       = 0;
        $this->selected_id    = null;
        $this->action         = 1;
        $this->search         = '';
        $this->inicio_factura = true;
        $this->estado_entrega = '0';
        $this->salon          = null;
        $this->factura_id     = null;
        $this->comanda_id     = null;
        $this->tab            = 'factura';
    }
    protected $listeners = [
        'modCliRep'         => 'modCliRep',
        'deleteRow'         => 'destroy',
        'factura_contado'   => 'factura_contado',
        'factura_ctacte'    => 'factura_ctacte',      
        'anularFactura'     => 'anularFactura',
        'elegirFormaDePago' => 'elegirFormaDePago',
        'enviarDatosPago'   => 'enviarDatosPago',
        'dejar_pendiente'   => 'dejar_pendiente', 
        'StoreOrUpdate'     => 'StoreOrUpdate',
        'unirComandas'      => 'unirComandas',
        'abrirMesa'         => 'abrirMesa'
    ];
    public function abrirMesa($data)
    {
        $info = json_decode($data);
        $this->mesa = $info->mesa_id;
        $this->mozo = $info->mozo_id;
        //dd($this->mesa,$this->mozo);
    }
    public function verSaldo($id)
    {            
        $info2 = Ctacte::join('clientes as c', 'c.id', 'cta_cte.cliente_id')
            ->where('c.id', $id)
            ->where('c.comercio_id', $this->comercioId)
            ->select('cta_cte.cliente_id', DB::RAW("'' as importe"))->get(); 

        foreach($info2 as $i) {
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
    public function facturaAfip()
    {
        include './../resources/src/Afip.php';
        /**
         * CUIT vinculado al certificado
         **/
        $CUIT = 20175835165; 

        $afip = new Afip(array('CUIT' => $CUIT));

        $data = array(
            'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
            'PtoVta' 	=> 1,  // Punto de venta
            'CbteTipo' 	=> 6,  // Tipo de comprobante (Factura B)(ver tipos disponibles) 
            'Concepto' 	=> 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
            'DocTipo' 	=> 99, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
            'DocNro' 	=> 0,  // Número de documento del comprador (0 consumidor final)
            'CbteDesde' 	=> 1,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
            'CbteHasta' 	=> 1,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
            'CbteFch' 	=> intval(date('Ymd')), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
            'ImpTotal' 	=> 121, // Importe total del comprobante
            'ImpTotConc' 	=> 0,   // Importe neto no gravado
            'ImpNeto' 	=> 100, // Importe neto gravado
            'ImpOpEx' 	=> 0,   // Importe exento de IVA
            'ImpIVA' 	=> 21,  //Importe total de IVA
            'ImpTrib' 	=> 0,   //Importe total de tributos
            'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
            'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)  
            'Iva' 		=> array( // (Opcional) Alícuotas asociadas al comprobante
                array(
                    'Id' 		=> 5, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
                    'BaseImp' 	=> 100, // Base imponible
                    'Importe' 	=> 21 // Importe 
                )
            ), 
        );
        
        $res = $afip->ElectronicBilling->CreateVoucher($data);
        echo $res['CAE']; //CAE asignado el comprobante
        echo $res['CAEFchVto']; //Fecha de vencimiento del CAE (yyyy-mm-dd)
    }    
	public function buscarArticulo($id)
	{
		$this->articulos = Producto::where('comercio_id', $this->comercioId)
                                ->where('categoria_id', $id)->orderBy('descripcion', 'asc')->get();
	}    
    public function buscarProducto($id)
    {
        $pvta = Producto::select()->where('comercio_id', $this->comercioId)->where('codigo', $id)->get();
        
        if ($pvta->count() > 0){
            $this->producto = $pvta[0]->id;
        }else{
            $this->producto = "Elegir";
            session()->flash('msg-error', 'El Código no existe...');
        } 
    }
    public function edit($id)
    {
        $record = DetFactura::find($id);
        $this->selected_id = $id;
        $this->producto = $record->producto_id;
        $this->precio = $record->precio;
        $this->cantidad = $record->cantidad;
    }
    public function verSalsaGuarn($id)
    {
        if($id != 'mantener_id') $this->producto = $id;
        $record = Producto::join('texto_base_comandas as tbc', 'tbc.id', 'productos.texto_base_comanda_id')
            ->where('productos.id', $this->producto)
            ->select('productos.guarnicion', 'productos.salsa', 'productos.sectorcomanda_id', 'tbc.descripcion')
            ->get();
        if($record[0]->sectorcomanda_id == 1){    
            $this->guarnicion = $record[0]->guarnicion;
            $this->salsa      = $record[0]->salsa;
            $this->texto_base = $record[0]->descripcion;  
            $this->emit('modal_comanda');
        } 
    }
    public function enviarComanda()
    {
        $record = Comanda::find($this->comanda_id);
        if($this->unirComandas == 'si'){
            $record->update(['estado'  => 'en espera']); //solo cambiamos el estado y respetamos la hora de envío histórica
        }else{
            $record->update([
                'estado'  => 'en espera',
                'sent_at' => Carbon::now()      //cambiamos estado y fecha/hora enviado
            ]);
        }
        $this->tab = 'factura';
        $this->emit('enviarComanda');
    }
    public function verEstadoComandas()
    {
        $estadoCom = Comanda::select('*') //primero verifico si hay alguna comanda en espera para esta mesa
            ->where('factura_id', $this->factura_id)
            ->where('estado', 'en espera')
            ->orderBy('sent_at')->get();
        if($estadoCom->count()){          // si hay, pregunto si se quiere agregar el nuevo item 
            foreach($estadoCom as $e){
                $this->comanda_id = $estadoCom[0]->id;
                $this->inicio_comanda = false;
                if($this->unirComandas == 'no') $this->emit('comandaEnEspera');
            }
        }
    }
    public function unirComandas($si_no)
    {
        $this->unirComandas = $si_no;
        if($this->unirComandas == 'si'){
            $record = Comanda::find($this->comanda_id);
            $record->update([
                'estado'  => 'cargado'
            ]);
        }
    }
    public function StoreOrUpdate($texto_comanda, $comanda)
    {
        $this->texto_comanda = $texto_comanda;
        if($this->producto != '0'){
            $producto = Producto::where('id', $this->producto)->get();
            //$this->producto = $id;
            $this->precio = $producto[0]->precio_venta_l1;
            $this->cantidad = 1;
            $this->sector_comanda = $producto[0]->sectorcomanda_id;
        }else {
            $this->validate([
                'producto' => 'not_in:Elegir'
            ]);            
            $this->validate([
                'cantidad' => 'required',
                'producto' => 'required',
                'precio' => 'required'
            ]);
        }
        $this->totalAgrabar = $this->total + ($this->cantidad * $this->precio); 
        //////COMANDAS
        if($comanda == 1 && $this->estadoComanda != 'cargado') $this->verEstadoComandas();     
        //////
        DB::begintransaction();                         //iniciar transacción para grabar
        try{  
            if($this->selected_id > 0) {                //valida si se quiere modificar o crear
                $record = DetFactura::find($this->selected_id);  
                $record->update([                       //actualizamos el registro
                    'producto_id' => $this->producto,
                    'cantidad'    => $this->cantidad,
                    'precio'      => $this->precio
                ]); 
            }else {
                if($this->cliente == 'Elegir') $this->cliente = null; else $this->delivery = 1;
                if($this->empleado == 'Elegir'){
                    $this->empleado = null;  
                }else{            //si es delivery, cambiamos el nro_arqueo
                    $this->estado_entrega = 1;    
                    $nroArqueo = CajaUsuario::where('caja_usuarios.caja_usuario_id', $this->empleado)
                        ->where('caja_usuarios.estado', '1')->get();
                    if($nroArqueo->count()){
                        $this->nro_arqueo = $nroArqueo[0]->id;  //este es el nro_arqueo del delivery
                    }  
                }  
                if($this->inicio_factura) {
                    $factura = Factura::create([
                        'numero'         => $this->numFactura,
                        'cliente_id'     => $this->cliente,
                        'importe'        => $this->totalAgrabar,
                        'estado'         => 'abierta',
                        'estado_pago'    => '0',
                        'estado_entrega' => $this->estado_entrega,
                        'repartidor_id'  => $this->empleado,
                        'mozo_id'        => $this->mozo,
                        'mesa_id'        => $this->mesa,
                        'user_id'        => auth()->user()->id, //id de quien confecciona la factura
                        'comercio_id'    => $this->comercioId,
                        'arqueo_id'      => $this->nro_arqueo   //nro. de arqueo de caja de quien cobra la factura
                    ]);

                    $mesa = Mesa::find($this->mesaId);
                    $mesa->update(['estado' => 'Ocupada']);

                    $this->inicio_factura = false;
                    $this->factura_id = $factura->id;
                }  
                $existe = Detfactura::select('id')              //buscamos si el producto ya está cargado
                    ->where('factura_id', $this->factura_id)
                    ->where('comercio_id', $this->comercioId)
                    ->where('producto_id', $this->producto)->get();
                if ($existe->count()){
                    $edit_cantidad = Detfactura::find($existe[0]->id); 
                    $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
                    $edit_cantidad->update([                //actualizamos solo la cantidad                                      
                        'cantidad' => $nueva_cantidad
                    ]);
                }else{
                    $add_item = Detfactura::create([         //creamos un nuevo detalle
                        'factura_id' => $this->factura_id,
                        'producto_id' => $this->producto,
                        'cantidad' => $this->cantidad,
                        'precio' => $this->precio,
                        'comercio_id' => $this->comercioId
                    ]);	
                }
                $record = Factura::find($this->factura_id);  //actualizamos el encabezado
                $record->update(['importe' => $this->totalAgrabar]); 

                //COMANDA////
                if($comanda == 1){
                  //  $this->verEstadoComandas();
//                     if($this->unirComandas == 'si'){

// /////////////////////////////////////////////////////////////////////////////////////////////////////////
//                     }
                    if($this->inicio_comanda) {
                        $comanda = Comanda::create([
                            'factura_id'       => $this->factura_id,
                            'estado'           => 'cargado',
                            'sectorcomanda_id' => $this->sector_comanda
                        ]);
                        $this->inicio_comanda = false;
                        $this->comanda_id = $comanda->id;
                    }  
                    $existe = Detcomanda::select('id')              //buscamos si el producto ya está cargado
                        ->where('comanda_id', $this->comanda_id)
                        ->where('producto_id', $this->producto)
                        ->where('descripcion', $this->texto_comanda)
                        ->where('comercio_id', $this->comercioId)->get();
                    if ($existe->count()){
                        $edit_cantidad = Detcomanda::find($existe[0]->id); 
                        $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
                        $edit_cantidad->update([                //actualizamos solo la cantidad                                      
                            'cantidad' => $nueva_cantidad
                        ]);
                    }else{
                        $add_item = Detcomanda::create([         //creamos un nuevo detalle
                            'comanda_id'  => $this->comanda_id,
                            'producto_id' => $this->producto,
                            'cantidad'    => $this->cantidad,
                            'descripcion' => $this->texto_comanda,
                            'comercio_id' => $this->comercioId
                        ]);	
                    } 
                    $this->tab = 'comanda';                   
                }


                /////////////
            }
            DB::commit();
            if($this->selected_id > 0) session()->flash('message', 'Registro Actualizado');       
            else session()->flash('message', 'Registro Agregado');  
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }     
        $this->resetInput(); 
        return;          
    }    
    public function factura_contado()
    {
        DB::begintransaction();                         //iniciar transacción para grabar
        try{
            $record = Factura::find($this->factura_id);
            $record->update([
                'estado'        => 'contado',
                'estado_pago'   => '1',
                'importe'       => $this->total,
                'forma_de_pago' => $this->f_de_pago,
                'nro_comp_pago' => $this->nro_comp_pago,  //nro ticket tarjeta o nro transferencia
                'mercadopago'   => $this->mercadopago,
                'comentario'    => $this->comentarioPago
            ]);
            DB::commit();
            $this->emit('facturaCobrada');
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }              
        $this->resetInputTodos();
    }
    public function factura_ctacte($cliId)
    {
        $info = json_decode($cliId);
        $this->clienteId = $info->cliente_id;
        DB::begintransaction();                         //iniciar transacción para grabar
        try{ 
            $record = Factura::find($this->factura_id);
            $record->update([
                'cliente_id' => $this->clienteId,
                'estado' => 'ctacte',
                'estado_pago' => '0',
                'importe' => $this->total
            ]);
            Ctacte::create([
                'cliente_id' => $this->clienteId,
                'factura_id' => $this->factura_id
            ]);
            $record = Cliente::find($this->clienteId); //marca que el cliente tiene un saldo en ctacte
            $record->update([
                'saldo' => '1'
            ]);
            $this->emit('facturaCtaCte');
            DB::commit();               
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInputTodos();
        return;
    }        
    public function dejar_pendiente()
    {
        $record = Factura::find($this->factura_id);
        $record->update([
            'estado' => 'pendiente',
            'importe' => $this->total
        ]);              
        session()->flash('message', 'Factura Pendiente'); 
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
       // dd($repartidor);
        $dataRep = User::find($repartidor);

        if($this->inicio_factura) {
            $this->mostrar_datos = 1;
            $this->apeNomCli = $dataCli->apellido . ' ' . $dataCli->nombre;
            $this->dirCliente = $dataCli->calle . ' ' . $dataCli->numero;
            $this->verSaldo($dataCli->id);
            $this->apeNomRep = $dataRep->apellido . ' ' . $dataRep->name;
            $this->cliente = $info->cliente_id;
            $this->empleado = $repartidor;
        }else {
            if($this->estado_entrega == 0) $this->estado_entrega = '1';
            $record = Factura::find($info->factura_id);
            $record->update([
                'cliente_id'     => $info->cliente_id,
                'repartidor_id'  => $repartidor,
                'estado_entrega' => $this->estado_entrega,
                'arqueo_id'      => $this->nro_arqueo
            ]);
            $this->delivery = 1;
            $this->mostrar_datos = 0;
        }
        session()->flash('message', 'Encabezado Modificado...');
    }
    public function destroy($id) //elimina item
    {
        if ($id) {
            DB::begintransaction();
            try{
                $detFactura = Detfactura::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla' => 'Detalle de Facturas',
                    'estado' => '0',
                    'user_delete_id' => auth()->user()->id,
                    'comentario' => $this->comentario,
                    'comercio_id' => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Registro eliminado con éxito!!');
                DB::commit();               
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
            }
            $this->resetInput();
            return;
        }
    }
    public function anularFactura($id, $comentario)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $factura = Factura::find($id);
                $factura->update([                    
                    'estado' => 'anulado'
                    ]);
                $factura = Factura::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Facturas',
                    'estado'          => '0',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $comentario,
                    'comercio_id'     => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Registro Anulado con éxito!!');
                DB::commit();               
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se anuló...');
            }
            $this->resetInput();
            return;
        }
    }
    public function elegirFormaDePago()
    {
        if($this->clienteId != ''){
            $cli = Cliente::where('id', $this->clienteId)->get();
            $this->nomCli = $cli[0]->apellido . ' ' . $cli[0]->nombre;
        }
        $this->f_de_pago = '1';        
        $this->doAction(2);
    }
    public function enviarDatosPago($tipo,$nro)
    {
        $this->f_de_pago = $tipo;
        $this->nro_comp_pago = $nro;
    }
}