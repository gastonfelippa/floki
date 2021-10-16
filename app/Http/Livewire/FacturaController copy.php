<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\CajaUsuario;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Comercio;
use App\Models\Ctacte;
use App\Models\Detfactura;
use App\Models\Detfactura_sp;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\StockEnConsignacion;
use App\Models\Subproducto;
use App\Models\User;
use Carbon\Carbon;
use DB;

class FacturaController extends Component
{
	//properties
    public $cantidad = 1, $precio, $estado='ABIERTO', $inicio_factura, $mostrar_datos;
    public $cliente="Elegir", $consignatario="Elegir", $empleado="Elegir", $producto="Elegir", $salon =null;
    public $clientes, $consignatarios, $empleados, $productos;
    public $selected_id = null, $search, $numFactura, $action = 1;
    public $facturas,  $total, $importe, $totalAgrabar, $delivery = 0;  
    public $grabar_encabezado = true, $modificar, $codigo, $barcode;
    public $arqueoGralId, $factura_id, $categorias, $articulos =null, $saldoCtaCte, $saldoACobrar;
    public $dirCliente, $apeNomCli, $apeNomRep, $clienteId;
    public $comentario, $nro_arqueo, $fecha_inicio, $caja_abierta, $estado_entrega = '0';
    public $f_de_pago = null, $nro_comp_pago = null, $comentarioPago = '', $mercadopago = null;
    public $estadoAqueoGral, $forzar_arqueo = 0, $ultima_factura = 0;
    public $contador_filas, $imp_por_hoja, $imp_duplicado, $lista = '1';
    public $comercioId, $modConsignaciones, $modDelivery;
    public $mostrar_sp = 0, $tiene_sp, $producto_sp = 0;
	
	public function render()
	{
        // $this->facturaAfip();
 
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        $this->modConsignaciones = session('modConsignaciones');
        $this->modDelivery = session('modDelivery');

        $comercio = Comercio::where('id', $this->comercioId)->get();
        if($comercio->count())
        {
            $this->imp_por_hoja  = $comercio[0]->imp_por_hoja;
            $this->imp_duplicado = $comercio[0]->imp_duplicado;
        }

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
        if($caja_abierta->count()){
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
        $this->consignatarios = Cliente::where('comercio_id', $this->comercioId)
            ->where('consignatario', '1')->orderBy('apellido')->get();
        $this->categorias = Categoria::where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
       
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
        if($dProducto != null){
            if($this->lista != '1') $this->precio = $dProducto->precio_venta_l2; 
            else $this->precio = $dProducto->precio_venta_l1; 
        }else $this->precio = '';

        $encabezado = Factura::select('*')->where('comercio_id', $this->comercioId)->withTrashed()->get(); 
        //si es la primera factura, le asigno el nro: 1
        if($encabezado->count() == 0){
            $this->numFactura = 1;
            $this->inicio_factura = true;       
        }else{  //sino, busco si hay alguna factura abierta
            $encabezado = Factura::where('facturas.estado','like','abierta')->where('facturas.comercio_id', $this->comercioId)
                ->select('facturas.*', 'facturas.numero as nroFact')->get();
                //verifico si es delivery para recuperar los datos de Cli/Rep
            if($encabezado->count() > 0 && $encabezado[0]->cliente_id <> null){                
                $encabezado = Factura::join('clientes as c','c.id','facturas.cliente_id')
                    ->join('users as u','u.id','facturas.repartidor_id')
                    ->where('facturas.estado','like','abierta')
                    ->where('facturas.comercio_id', $this->comercioId)
                    ->select('facturas.*', 'facturas.numero as nroFact','c.nombre as nomCli', 'c.apellido as apeCli','c.calle',
                    'c.numero', 'u.name as nomRep', 'u.apellido as apeRep')->get();
                $this->numFactura = $encabezado[0]->nroFact;
                $this->clienteId = $encabezado[0]->cliente_id;
                $this->factura_id = $encabezado[0]->id;
                $this->inicio_factura = false;
                $this->delivery = 1;
                $this->lista = $encabezado[0]->lista;
                $this->estado_entrega = $encabezado[0]->estado_entrega;
                $this->dirCliente = $encabezado[0]->calle . ' ' . $encabezado[0]->numero;
                $this->verSaldo($encabezado[0]->cliente_id);
                $this->mostrar_datos = 0;
            }elseif($encabezado->count() > 0) {
                $this->inicio_factura = false;
                $this->lista = $encabezado[0]->lista;
                $this->numFactura = $encabezado[0]->nroFact;
                $this->factura_id = $encabezado[0]->id;
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
        $this->contador_filas = 0;
        foreach ($info as $i){
            $this->contador_filas ++;
            $i->importe=$i->cantidad * $i->precio;
            $this->total += $i->importe;
        }
        $this->verificar_impresion();

		return view('livewire.facturas.component', [
            'info'        => $info,
            'encabezado'  => $encabezado
		]);
    }
    public function verificar_impresion()
    {
        if($this->contador_filas > 10 && $this->imp_por_hoja != '1') $this->emit('limite_superado');
        elseif($this->contador_filas > 20 && $this->imp_por_hoja == '1') $this->emit('limite_20');
    }
    public function doAction($action)
    {
        $this->action = $action;
    }
    public function resetInput()
    {
        $this->cantidad       = 1;
        $this->barcode        = '';
        $this->precio         = '';
        $this->producto       = 'Elegir';
        $this->producto_sp    = 0;
        $this->selected_id    = null;
        $this->action         = 1;
        $this->search         = '';
        $this->estado_entrega = 0;
        $this->salon          = null;
        $this->f_de_pago      = null;
        $this->nro_comp_pago  = null;
        $this->mercadopago    = null;
        $this->comentarioPago = '';
        $this->forzar_arqueo  = 0;
        $this->mostrar_sp     = 0;
    }    
    public function resetInputTodos()
    {
        $this->cantidad       = 1;
        $this->barcode        = '';
        $this->precio         = '';        
        $this->cliente        = 'Elegir';
        $this->consignatario  = 'Elegir';
        $this->dirCliente     = null;
        $this->empleado       = 'Elegir';
        $this->producto       = 'Elegir';
        $this->producto_sp    = 0;
        $this->articulos      = '';
        $this->delivery       = 0;
        $this->selected_id    = null;
        $this->action         = 1;
        $this->search         = '';
        $this->inicio_factura = true;
        $this->estado_entrega = '0';
        $this->salon          = null;
        $this->factura_id     = null;
        $this->lista          = '1';
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
        'StoreOrUpdate'     => 'StoreOrUpdate'
    ];
    public function usarLista($numero)
    {
        $this->lista = $numero;
        $texto = "";
        if($this->modConsignaciones == '1') $texto = "La Factura descontará el Stock Local";
        if($numero == '3'){
            $texto="La Factura descontará el Stock del Consignatario";
            $this->emit('listaNro','Consignatarios', $texto);
        }else $this->emit('listaNro', $this->lista, $texto);
    }
    public function verSaldo($id)
    { 
        $this->saldoCtaCte = 0;          
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
        $this->mostrar_sp = 0;
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
        $this->producto    = $record->producto_id;
        $this->precio      = $record->precio;
        $this->cantidad    = $record->cantidad;
    }
    public function StoreOrUpdateButton($articuloId)
    {
        if($articuloId != 0) $this->producto = $articuloId;
        else{
            $this->validate([
                'producto' => 'not_in:Elegir'
            ]); 
        }
        $this->tiene_sp = Subproducto::where('producto_id', $this->producto)->get();
        if($this->tiene_sp->count()){
            $this->mostrar_sp = 1;
        }else $this->verificar_stock(0);
    }
    public function StoreOrUpdateButtonSp($articuloSp_Id)
    {
        if($articuloSp_Id != 0) $this->producto_sp = $articuloSp_Id;
        else{
            $this->validate([
                'producto' => 'not_in:Elegir'
            ]); 
        }
        $this->verificar_stock(1);
    }
    public function verificar_stock($sp)
    {
        $producto = Producto::find($this->producto);
        if($sp == 1){
            $stock_sp = Subproducto::find($this->producto_sp);
            $stock_local = $stock_sp->stock;  
        }else $stock_local = $producto->stock;
        
        if($stock_local == null) $stock_local = 0;
        if($this->lista == '1'){
            if($stock_local >= $this->cantidad){
                $this->precio = $producto->precio_venta_l1;
                $this->StoreOrUpdate();
            }else $this->emit('stock_no_disponible', 'local', $stock_local); $this->resetInput(); 
        }elseif($this->lista == '2'){ 
            if($stock_local >= $this->cantidad){
                $this->precio = $producto->precio_venta_l2;
                $this->StoreOrUpdate();
            }else $this->emit('stock_no_disponible', 'local', $stock_local); $this->resetInput();
        }else{
            $cliente = null;
            if($this->inicio_factura) $cliente = $this->consignatario;
            else $cliente = $this->clienteId;
            if($sp == 0){
                $stock_consignacion = StockEnConsignacion::where('producto_id', $this->producto)
                    ->where('cliente_id', $cliente)
                    ->get()->sum('cantidad');    
            }else{
                $stock_consignacion = StockEnConsignacion::where('producto_id', $this->producto_sp)
                    ->where('cliente_id', $cliente)
                    ->get()->sum('cantidad');
            }
            if($stock_consignacion == null) $stock_consignacion = 0;
            if($stock_consignacion >= $this->cantidad){
                $this->precio = $producto->precio_venta_l2; 
                $this->StoreOrUpdate();
            }else $this->emit('stock_no_disponible', 'en consignación', $stock_consignacion); $this->resetInput();
        }
    }
    public function StoreOrUpdate()
    {
        $this->validate([
            'producto' => 'not_in:Elegir'
        ]);            
        $this->validate([
            'cantidad' => 'required|numeric|min:0|not_in:0',
            'producto' => 'required',
            'precio'   => 'required'
        ]);
        $this->totalAgrabar = $this->total + ($this->cantidad * $this->precio); 

        DB::begintransaction();                         //iniciar transacción para grabar
        try{
            //busca el id del cliente porque al ser inicio de factura este dato está en memoria 
            //y no está grabado todavía            
            $cliente = null;      
            if($this->inicio_factura){
                if($this->lista != '3') $cliente = $this->cliente;
                else $cliente = $this->consignatario;
            }else $cliente = $this->clienteId; 
            //CONTROL DE STOCK: si se factura con L1 o L2, se descuenta el stock local
            //si se factura con L3, se descuenta el stock del consignatario.                         
            if($this->selected_id > 0) {       //modifica
                $record = DetFactura::find($this->selected_id);//primero modificamos el detalle
                $cantidad_detalle = $record->cantidad;         
                $record->update([                        
                    'producto_id' => $this->producto,
                    'cantidad'    => $this->cantidad,
                    'precio'      => $this->precio
                ]);
                if($this->lista != '3'){                      //si se descuenta solo del stock local
                    $record = Producto::find($this->producto);   
                    $stockAnterior = $record['stock']; 
                    if($cantidad_detalle > $this->cantidad){         //agrego stock local
                        $stock_a_agregar = $cantidad_detalle - $this->cantidad;
                        $stockNuevo = $stockAnterior + $stock_a_agregar;
                    }elseif($cantidad_detalle < $this->cantidad){    //resto stock local
                        $stock_a_descontar = $this->cantidad - $cantidad_detalle;
                        $stockNuevo = $stockAnterior - $stock_a_descontar; 
                    }
                    $record->update(['stock' => $stockNuevo]); 
                }else{                                  //si se descuenta del stock del consignatario
                    $this->cantidad = -1 * abs($this->cantidad);  //invierte el signo  
                    $existe = StockEnConsignacion::select('id')  //solo modifico la cantidad
                        ->where('factura_id', $this->factura_id)
                        ->where('comercio_id', $this->comercioId)
                        ->where('producto_id', $this->producto)->get();
                    if($existe->count()){
                        $record = StockEnConsignacion::find($existe[0]->id);
                        $record->update(['cantidad' => $this->cantidad]); 
                    }
                }                     
            }else {                             //crea
                if($this->lista != '3'){
                    if($this->cliente == 'Elegir') $this->cliente = null; else $this->delivery = 1;
                }else{
                    if($this->consignatario == 'Elegir') $this->cliente = null; 
                    else{
                        $this->cliente = $this->consignatario;
                        $this->delivery = 1;
                    } 
                }  

                if($this->empleado == 'Elegir') $this->empleado = null;  
                else{            //si es delivery, cambiamos el nro_arqueo
                    $this->estado_entrega = 1;    
                    $nroArqueo = CajaUsuario::where('caja_usuarios.caja_usuario_id', $this->empleado)
                        ->where('caja_usuarios.estado', '1')->get();
                    if($nroArqueo->count()) $this->nro_arqueo = $nroArqueo[0]->id;  //este es el nro_arqueo del delivery
                }  
                if($this->inicio_factura) {
                    $factura = Factura::create([
                        'numero'         => $this->numFactura,
                        'cliente_id'     => $this->cliente,
                        'importe'        => $this->totalAgrabar,
                        'estado'         => 'abierta',
                        'estado_pago'    => '0',
                        'estado_entrega' => $this->estado_entrega,
                        'lista'          => $this->lista,
                        'repartidor_id'  => $this->empleado,
                        'mozo_id'        => null,
                        'mesa_id'        => null,
                        'user_id'        => auth()->user()->id, //id de quien confecciona la factura
                        'comercio_id'    => $this->comercioId,
                        'arqueo_id'      => $this->nro_arqueo   //nro. de arqueo de caja de quien cobra la factura
                    ]);
                    $this->inicio_factura = false;
                    $this->factura_id = $factura->id;
                } 
                // crea detalle
                $existe = Detfactura::select('id')         //buscamos si el producto ya está cargado
                    ->where('factura_id', $this->factura_id)
                    ->where('comercio_id', $this->comercioId)
                    ->where('producto_id', $this->producto)->get();
                if ($existe->count()){                    //actualizamos solo la cantidad
                    $edit_cantidad = Detfactura::find($existe[0]->id); 
                    $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
                    $edit_cantidad->update(['cantidad' => $nueva_cantidad]);
                    if($this->producto_sp != 0){
                        $existe_sp = Detfactura_sp::select('id')  //buscamos si el subproducto ya está cargado
                            ->where('factura_id', $this->factura_id)
                            ->where('comercio_id', $this->comercioId)
                            ->where('producto_sp_id', $this->producto_sp)->get();
                        if ($existe_sp->count()){                 //actualizamos solo la cantidad
                            $edit_cantidad = Detfactura_sp::find($existe_sp[0]->id); 
                            $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
                            $edit_cantidad->update(['cantidad' => $nueva_cantidad]);
                        }else{
                            $add_item_sp = Detfactura_sp::create([         
                                'factura_id'     => $this->factura_id,
                                'producto_id'    => $this->producto,
                                'producto_sp_id' => $this->producto_sp,
                                'cantidad'       => $this->cantidad,
                                'comercio_id'    => $this->comercioId
                            ]); 
                        }
                    }
                }else{                                    //creamos un nuevo detalle
                    if($this->contador_filas == 10 && $this->imp_por_hoja != '1') $this->emit('limite_10');
                    elseif($this->contador_filas == 20 && $this->imp_por_hoja == '1') $this->emit('limite_20');
                    else{
                        $add_item = Detfactura::create([         
                            'factura_id'  => $this->factura_id,
                            'producto_id' => $this->producto,
                            'cantidad'    => $this->cantidad,
                            'precio'      => $this->precio,
                            'comercio_id' => $this->comercioId
                        ]); 
                        if($this->producto_sp != 0){
                            $add_item = Detfactura_sp::create([         
                                'factura_id'     => $this->factura_id,
                                'producto_id'    => $this->producto,
                                'producto_sp_id' => $this->producto_sp,
                                'cantidad'       => $this->cantidad,
                                'comercio_id'    => $this->comercioId
                            ]);   
                        }                     
                    }
                }
                $record = Factura::find($this->factura_id);  //actualizamos el encabezado
                $record->update(['importe' => $this->totalAgrabar]);
                
                //modifico stock
                if($this->lista != '3'){                    
                    $record = Producto::find($this->producto);
                    $stockAnterior = $record['stock'];
                    $stockNuevo = $stockAnterior - $this->cantidad;  
                    $record->update(['stock' => $stockNuevo]); 
                    if($this->producto_sp != 0){
                        $record_sp = Subproducto::find($this->producto_sp);
                        $stockAnterior_sp = $record_sp['stock'];
                        $stockNuevo_sp = $stockAnterior_sp - $this->cantidad;  
                        $record_sp->update(['stock' => $stockNuevo_sp]); 
                    }
                }else{
                    $this->cantidad = -1 * abs($this->cantidad);  //invierte el signo  
                    $existe = StockEnConsignacion::select('id')  //buscamos si el producto ya está cargado
                        ->where('factura_id', $this->factura_id)
                        ->where('comercio_id', $this->comercioId)
                        ->where('producto_id', $this->producto)->get();
                    if($existe->count()){
                        $edit_cantidad = StockEnConsignacion::find($existe[0]->id); 
                        $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
                        $edit_cantidad->update(['cantidad' => $nueva_cantidad]);
                    }else{   
                        $inv_en_consig = StockEnConsignacion::create([
                            'cliente_id'  => $cliente,
                            'factura_id'  => $this->factura_id,
                            'producto_id' => $this->producto,
                            'cantidad'    => $this->cantidad,
                            'comercio_id' => $this->comercioId
                        ]);
                    }
                }
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
    // public function StoreOrUpdate()
    // {
    //     $this->validate([
    //         'producto' => 'not_in:Elegir'
    //     ]);            
    //     $this->validate([
    //         'cantidad' => 'required|numeric|min:0|not_in:0',
    //         'producto' => 'required',
    //         'precio'   => 'required'
    //     ]);
    //     $this->totalAgrabar = $this->total + ($this->cantidad * $this->precio); 

    //     DB::begintransaction();                         //iniciar transacción para grabar
    //     try{
    //         //busca el id del cliente porque al ser inicio de factura este dato está en memoria 
    //         //y no está grabado todavía            
    //         $cliente = null;      
    //         if($this->inicio_factura) $cliente = $this->cliente;
    //         else $cliente = $this->clienteId; 
    //         //CONTROL DE STOCK: si se factura con L1 o L2, se descuenta el stock local
    //         //si se factura con L3, se descuenta el stock del consignatario.                         
    //         if($this->selected_id > 0) {       //valida si se quiere modificar o crear
    //             $record = DetFactura::find($this->selected_id);//primero modificamos el detalle
    //             $cantidad_detalle = $record->cantidad;         
    //             $record->update([                        
    //                 'producto_id' => $this->producto,
    //                 'cantidad'    => $this->cantidad,
    //                 'precio'      => $this->precio
    //             ]);
    //             if($this->lista != '3'){                      //si se descuenta solo del stock local
    //                 $record = Producto::find($this->producto);   
    //                 $stockAnterior = $record['stock']; 
    //                 if($cantidad_detalle > $this->cantidad){         //agrego stock local
    //                     $stock_a_agregar = $cantidad_detalle - $this->cantidad;
    //                     $stockNuevo = $stockAnterior + $stock_a_agregar;
    //                 }elseif($cantidad_detalle < $this->cantidad){    //resto stock local
    //                     $stock_a_descontar = $this->cantidad - $cantidad_detalle;
    //                     $stockNuevo = $stockAnterior - $stock_a_descontar; 
    //                 }
    //                 $record->update(['stock' => $stockNuevo]); 
    //             }else{                                  //si se descuenta del stock del consignatario
    //                 $this->cantidad = -1 * abs($this->cantidad);  //invierte el signo  
    //                 $existe = StockEnConsignacion::select('id')  //solo modifico la cantidad
    //                     ->where('factura_id', $this->factura_id)
    //                     ->where('comercio_id', $this->comercioId)
    //                     ->where('producto_id', $this->producto)->get();
    //                 if($existe->count()){
    //                     $record = StockEnConsignacion::find($existe[0]->id);
    //                     $record->update(['cantidad' => $this->cantidad]); 
    //                 }
    //             }                     
    //         }else {
    //             if($this->cliente == 'Elegir') $this->cliente = null; else $this->delivery = 1;
    //             if($this->empleado == 'Elegir') $this->empleado = null;  
    //             else{            //si es delivery, cambiamos el nro_arqueo
    //                 $this->estado_entrega = 1;    
    //                 $nroArqueo = CajaUsuario::where('caja_usuarios.caja_usuario_id', $this->empleado)
    //                     ->where('caja_usuarios.estado', '1')->get();
    //                 if($nroArqueo->count()) $this->nro_arqueo = $nroArqueo[0]->id;  //este es el nro_arqueo del delivery
    //             }  
    //             if($this->inicio_factura) {
    //                 $factura = Factura::create([
    //                     'numero'         => $this->numFactura,
    //                     'cliente_id'     => $this->cliente,
    //                     'importe'        => $this->totalAgrabar,
    //                     'estado'         => 'abierta',
    //                     'estado_pago'    => '0',
    //                     'estado_entrega' => $this->estado_entrega,
    //                     'lista'          => $this->lista,
    //                     'repartidor_id'  => $this->empleado,
    //                     'mozo_id'        => null,
    //                     'mesa_id'        => null,
    //                     'user_id'        => auth()->user()->id, //id de quien confecciona la factura
    //                     'comercio_id'    => $this->comercioId,
    //                     'arqueo_id'      => $this->nro_arqueo   //nro. de arqueo de caja de quien cobra la factura
    //                 ]);
    //                 $this->inicio_factura = false;
    //                 $this->factura_id = $factura->id;
    //             }  
    //             $existe = Detfactura::select('id')         //buscamos si el producto ya está cargado
    //                 ->where('factura_id', $this->factura_id)
    //                 ->where('comercio_id', $this->comercioId)
    //                 ->where('producto_id', $this->producto)->get();
    //             if ($existe->count()){                    //actualizamos solo la cantidad
    //                 $edit_cantidad = Detfactura::find($existe[0]->id); 
    //                 $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
    //                 $edit_cantidad->update(['cantidad' => $nueva_cantidad]);
    //             }else{                                    //creamos un nuevo detalle
    //                 if($this->contador_filas == 10 && $this->imp_por_hoja != '1') $this->emit('limite_10');
    //                 elseif($this->contador_filas == 20 && $this->imp_por_hoja == '1') $this->emit('limite_20');
    //                 else{
    //                     $add_item = Detfactura::create([         
    //                         'factura_id'  => $this->factura_id,
    //                         'producto_id' => $this->producto,
    //                         'cantidad'    => $this->cantidad,
    //                         'precio'      => $this->precio,
    //                         'comercio_id' => $this->comercioId
    //                     ]);                       
    //                 }
    //             }
    //             $record = Factura::find($this->factura_id);  //actualizamos el encabezado
    //             $record->update(['importe' => $this->totalAgrabar]);

    //             if($this->lista != '3'){
    //                 $record = Producto::find($this->producto);
    //                 $stockAnterior = $record['stock'];
    //                 $stockNuevo = $stockAnterior - $this->cantidad;  
    //                 $record->update(['stock' => $stockNuevo]);        
    //             }else{
    //                 $this->cantidad = -1 * abs($this->cantidad);  //invierte el signo  
    //                 $existe = StockEnConsignacion::select('id')  //buscamos si el producto ya está cargado
    //                     ->where('factura_id', $this->factura_id)
    //                     ->where('comercio_id', $this->comercioId)
    //                     ->where('producto_id', $this->producto)->get();
    //                 if($existe->count()){
    //                     $edit_cantidad = StockEnConsignacion::find($existe[0]->id); 
    //                     $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
    //                     $edit_cantidad->update(['cantidad' => $nueva_cantidad]);
    //                 }else{   
    //                     $inv_en_consig = StockEnConsignacion::create([
    //                         'cliente_id'  => $cliente,
    //                         'factura_id'  => $this->factura_id,
    //                         'producto_id' => $this->producto,
    //                         'cantidad'    => $this->cantidad,
    //                         'comercio_id' => $this->comercioId
    //                     ]);
    //                 }
    //             }
    //         }
    //         DB::commit();
    //         if($this->selected_id > 0) session()->flash('message', 'Registro Actualizado');       
    //         else session()->flash('message', 'Registro Agregado');  
    //     }catch (Exception $e){
    //         DB::rollback();
    //         session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
    //     }     
    //     $this->resetInput(); 
    //     return;        
    // }    
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
        }catch (Exception $e){
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
            DB::commit();               
            $this->emit('facturaCtaCte');
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInputTodos();
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
        $dataRep = User::find($repartidor);

        if($this->inicio_factura) {
            $this->mostrar_datos = 1;
            $this->apeNomCli = $dataCli->apellido . ' ' . $dataCli->nombre;
            $this->dirCliente = $dataCli->calle . ' ' . $dataCli->numero;
            $this->verSaldo($dataCli->id);
            $this->apeNomRep = $dataRep->apellido . ' ' . $dataRep->name;
            if($this->lista != '3') $this->cliente = $info->cliente_id;
            else $this->consignatario = $info->cliente_id;
            
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
                $detFactura = Detfactura::find($id);
                $producto_id = $detFactura->producto_id;
                $cantidad = $detFactura->cantidad;
                $detFactura->delete(); //elimina el item del detalle

                if($this->lista != '3'){    //si está en uso el stock local
                    $record = Producto::find($producto_id);        //suma stock local
                    $stockAnterior = $record['stock'];
                    $stockNuevo = $stockAnterior + $cantidad;  
                    $record->update([                       
                        'stock' => $stockNuevo
                    ]);
                }else{                       //si está en uso el stock en consignación
                    $record = StockEnConsignacion::find($producto_id)        //suma stock local
                        ->where('factura_id', $this->factura_id)
                        ->where('comercio_id', $this->comercioId);
                    $record->delete();
                }
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