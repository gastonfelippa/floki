<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Banco;
use App\Models\CajaUsuario;
use App\Models\Categoria;
use App\Models\Cheque;
use App\Models\Cliente;
use App\Models\Comercio;
use App\Models\Ctacte;
use App\Models\Detfactura;
use App\Models\DetMetodoPago;
use App\Models\Factura;
use App\Models\Localidad;
use App\Models\Producto;
use App\Models\Stock;
use App\Models\StockEnConsignacion;
use App\Models\Subproducto;
use App\Models\User;
use Carbon\Carbon;
use DB;

class FacturaController extends Component
{
	//properties
    public $cantidad = 1, $precio, $estado='ABIERTO', $inicio_factura, $mostrar_datos;
    public $cliente="Elegir", $consignatario="Elegir", $empleado="Elegir", $producto="Elegir", $subproducto="Elegir", $salonId;
    public $clientes, $consignatarios, $empleados, $productos, $subproductos, $bancos = "Elegir", $esConsFinal;
    public $selected_id = 0, $numFactura, $action = 1;
    public $facturas,  $total, $importe, $totalAgrabar, $delivery = 0, $importeCompPago, $entrega = 0, $saldo;  
    public $grabar_encabezado = true, $modificar, $codigo, $barcode = null;
    public $arqueoGralId, $factura_id, $categorias, $articulos =null, $saldoCtaCte, $saldoACobrar;
    public $dirCliente, $apeNomCli, $apeNomRep, $clienteId = null;
    public $comentario, $nro_arqueo, $fecha_inicio, $caja_abierta, $estado_entrega = '0';
    public $f_de_pago = null, $nro_comp_pago = null, $comentarioPago = '', $mercadopago = null;
    public $estadoAqueoGral, $forzar_arqueo = 0, $ultima_factura = 0;
    public $contador_filas, $imp_por_hoja, $imp_duplicado, $lista = '1';
    public $comercioId, $modComandas, $modConsignaciones, $modDelivery, $facturaPendiente, $comercioTipo;
    public $mostrar_sp = 0, $tiene_sp, $es_producto = 1, $controlar_stock = 'no';
    public $costo;
	
	public function render()
	{
        // $this->facturaAfip();
 
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        $this->modConsignaciones = session('modConsignaciones');
        $this->modComandas = session('modComandas');
        $this->modDelivery = session('modDelivery');
        $this->facturaPendiente = session('facturaPendiente');
        $this->comercioTipo = session('tipoComercio');

        //averiguo el id del Cons Final
        $this->esConsFinal = Cliente::where('comercio_id', $this->comercioId)
            ->where('nombre', 'FINAL')->select('id')->first();
        $this->esConsFinal = $this->esConsFinal->id;
        
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
                ->where('user_id', auth()->user()->id)->get();
            //si hay alguna, la dejo terminar, pero al finalizar vuelvo al home   
            if($record) $this->ultima_factura = 1;  
            else $this->forzar_arqueo = 1;
        }

        //vemos si tenemos una caja habilitada con nuestro user_id
        $caja_abierta = CajaUsuario::where('caja_usuario_id', auth()->user()->id)
            ->where('estado', '1')->select('*')->get();
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
        if ($this->subproducto == 'Elegir'){
            $this->subproductos = Subproducto::where('producto_id', $this->producto)
                ->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        }else{
            $this->subproductos = Subproducto::where('id', $this->subproducto)
                ->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        }
        $this->clientes = Cliente::where('comercio_id', $this->comercioId)->orderBy('apellido')->get();
        $this->consignatarios = Cliente::where('comercio_id', $this->comercioId)
            ->where('consignatario', '1')->orderBy('apellido')->get();
        $this->categorias = Categoria::where('mostrar_al_vender', 'si')
            ->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        $this->bancos = Banco::where('comercio_id', $this->comercioId)
            ->orderBy('descripcion')->get();
        

        //muestro solo los repartidores que tienen caja abierta
        $this->empleados = User::join('model_has_roles as mhr', 'mhr.model_id', 'users.id')
            ->join('roles as r', 'r.id', 'mhr.role_id')
            ->join('caja_usuarios as cu', 'cu.caja_usuario_id', 'users.id')
            ->where('r.alias', 'Repartidor')
            ->where('r.comercio_id', $this->comercioId)
            ->where('cu.estado', '1')
            ->select('users.id', 'users.name', 'users.apellido')->get();
      
        //capturo el id del repartidor Salón 
        $salon = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('users.name', '...')
            ->where('users.apellido', 'Salón')
            ->where('uc.comercio_id', $this->comercioId)
            ->select('users.id')->get();
        $this->salonId = $salon[0]->id;

        if($this->selected_id == 0){
            if(strlen($this->barcode) > 0) $this->buscarProducto($this->barcode); 
            else $this->precio = '';        

            $dProducto = Producto::find($this->producto);
            if($dProducto != null){
                if($this->lista != '1') $this->precio = $dProducto->precio_venta_l2; 
                else $this->precio = $dProducto->precio_venta_l1; 
            }else $this->precio = '';
        }
    
        if($this->facturaPendiente){   //si se trata de una factura pendiente, la mostramos
             $encabezado = Factura::find($this->facturaPendiente);
            //verifico si es delivery para recuperar los datos de Cli/Rep
            if($encabezado->count()){                
                $encabezado = Factura::join('clientes as c','c.id','facturas.cliente_id')
                    ->join('users as u','u.id','facturas.repartidor_id')
                    ->where('facturas.id', $this->facturaPendiente)
                    ->where('facturas.comercio_id', $this->comercioId)
                    ->select('facturas.*', 'facturas.numero as nroFact','c.nombre as nomCli', 'c.apellido as apeCli','c.calle',
                    'c.numero', 'c.localidad_id', 'u.name as nomRep', 'u.apellido as apeRep', DB::RAW("'' as localidad"))->get();
                $loc = Localidad::find($encabezado[0]->localidad_id);
                $encabezado[0]->localidad = $loc->descripcion;
                $this->numFactura = $encabezado[0]->nroFact;
                $this->clienteId = $encabezado[0]->cliente_id;
                $this->factura_id = $encabezado[0]->id;
                $this->inicio_factura = false;
                $this->delivery = 1;
                $this->lista = $encabezado[0]->lista;
                $this->estado_entrega = $encabezado[0]->estado_entrega;
                $this->dirCliente = $encabezado[0]->calle . ' ' . $encabezado[0]->numero . ' - ' . $encabezado[0]->localidad;
                $this->verSaldo($encabezado[0]->cliente_id);
                $this->mostrar_datos = 0;
           }
        }else{
            $encabezado = Factura::select('*')->where('comercio_id', $this->comercioId)->withTrashed()->get(); 
            //si es la primera factura, le asigno el nro: 1
            if($encabezado->count() == 0){
                $this->numFactura = 1;
                $this->inicio_factura = true;       
            }else{  //sino, busco si hay alguna factura abierta
                $encabezado = Factura::where('facturas.estado','like','abierta')
                    ->where('facturas.comercio_id', $this->comercioId)
                    ->select('facturas.*', 'facturas.numero as nroFact')->get();
                    //verifico si es delivery para recuperar los datos de Cli/Rep
                if($encabezado->count() > 0 && $encabezado[0]->repartidor_id <> null){ 
                    $encabezado = Factura::join('clientes as c','c.id','facturas.cliente_id')
                        ->join('users as u','u.id','facturas.repartidor_id')
                        ->where('facturas.estado','like','abierta')
                        ->where('facturas.comercio_id', $this->comercioId)
                        ->select('facturas.*', 'facturas.numero as nroFact','c.nombre as nomCli', 'c.apellido as apeCli','c.calle',
                        'c.numero', 'c.localidad_id', 'u.name as nomRep', 'u.apellido as apeRep', DB::RAW("'' as localidad"))->get();
                    $loc = Localidad::find($encabezado[0]->localidad_id);
                    $encabezado[0]->localidad = $loc->descripcion;
                    $this->numFactura = $encabezado[0]->nroFact;
                    $this->clienteId = $encabezado[0]->cliente_id;
                    $this->factura_id = $encabezado[0]->id;
                    $this->inicio_factura = false;
                    $this->delivery = 1;
                    $this->lista = $encabezado[0]->lista;
                    $this->estado_entrega = $encabezado[0]->estado_entrega;
                    $this->dirCliente = $encabezado[0]->calle . ' ' . $encabezado[0]->numero . ' - ' . $encabezado[0]->localidad;
                    $this->verSaldo($encabezado[0]->cliente_id);
                    $this->mostrar_datos = 0;
                }elseif($encabezado->count() > 0 && $encabezado[0]->cliente_id <> null){ 
                    $encabezado = Factura::join('clientes as c','c.id','facturas.cliente_id')
                        ->where('facturas.estado','like','abierta')
                        ->where('facturas.comercio_id', $this->comercioId)
                        ->select('facturas.*', 'facturas.numero as nroFact','c.nombre as nomCli', 'c.apellido as apeCli','c.calle',
                        'c.numero', 'c.localidad_id', DB::RAW("'' as localidad"))->get();
                    $loc = Localidad::find($encabezado[0]->localidad_id);
                    $encabezado[0]->localidad = $loc->descripcion;
                    $this->numFactura = $encabezado[0]->nroFact;
                    $this->clienteId = $encabezado[0]->cliente_id;
                    $this->factura_id = $encabezado[0]->id;
                    $this->inicio_factura = false;
                    $this->delivery = 1;
                    $this->lista = $encabezado[0]->lista;
                    $this->estado_entrega = $encabezado[0]->estado_entrega;
                    $this->dirCliente = $encabezado[0]->calle . ' ' . $encabezado[0]->numero . ' - ' . $encabezado[0]->localidad;
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
        }  
        $info = Detfactura::select('*')->where('comercio_id', $this->comercioId)->get();
        if($info->count()){ 
            $info = Detfactura::join('facturas as r','r.id','detfacturas.factura_id')
                ->select('detfacturas.*', DB::RAW("'' as p_id"), 
                    DB::RAW("'' as codigo"), DB::RAW("'' as producto"), DB::RAW("'' as es_producto"))
                ->where('detfacturas.factura_id', $this->factura_id)
                ->where('detfacturas.comercio_id', $this->comercioId)
                ->orderBy('detfacturas.id')->get();  
            
            $this->total = 0;
            $this->contador_filas = 0;
            foreach ($info as $i){
                $this->contador_filas ++;
                $i->importe=$i->cantidad * $i->precio;
                $this->total += $i->importe;
                if($i->producto_id){
                    $producto = Producto::find($i->producto_id);
                    $i->p_id        = $producto->id;
                    $i->codigo      = $producto->codigo;
                    $i->producto    = $producto->descripcion;
                    $i->es_producto = 1;
                }else{
                    $subproducto = Subproducto::find($i->subproducto_id);
                    $i->p_id        = $subproducto->id;
                    $i->codigo      = $subproducto->id;
                    $i->producto    = $subproducto->descripcion;
                    $i->es_producto = 0;             
                }
            }
        } 
        $infoMediosDePago = DetMetodoPago::where('factura_id', $this->factura_id)
            ->where('comercio_id', $this->comercioId)
            ->select('*', DB::RAW("'' as medio_pago"))->orderBy('id')->get(); 
        $this->entrega = 0;
        if($infoMediosDePago->count()){
            foreach($infoMediosDePago as $i){
                $this->entrega += $i->importe;
                switch ($i->medio_de_pago) {
                    case '1':
                        $i->medio_pago = 'Efectivo';
                        break;
                    case '2':
                        $i->medio_pago = 'Tarj. de Débito';
                        break;
                    case '3':
                        $i->medio_pago = 'Tarj. de Crédito';
                        break;
                    case '4':
                        $i->medio_pago = 'Transferencia Bancaria';
                        break;
                    case '5':
                        $i->medio_pago = 'Cheque N°';
                        break;
                    default:
                        $i->medio_pago = '';
                }
            }
        }  
        $this->saldo = $this->total - $this->entrega;
        
        
        $this->verificar_impresion();

		return view('livewire.facturas.component', [
            'info'             => $info,
            'encabezado'       => $encabezado,
            'infoMediosDePago' => $infoMediosDePago
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
        if($this->action == 1) $this->resetInput();
    }
    public function resetMediosDePago()
    {
        $this->f_de_pago       = null;
        $this->nro_comp_pago   = null;
        $this->importeCompPago = null;
        $this->mercadopago     = null;
        $this->comentarioPago  = '';
        $this->entrega         = 0;
        $this->saldo           = 0;
    }    
    public function resetInput()
    {
        $this->action          = 1;
        $this->barcode         = null;
        $this->cantidad        = 1;
        $this->clienteId       = null;
        $this->comentarioPago  = '';
        $this->controlar_stock = 'no';
        $this->es_producto     = 1;
        $this->estado_entrega  = '0';
        $this->f_de_pago       = null;
        $this->forzar_arqueo   = 0;
        $this->mercadopago     = null;
        $this->mostrar_sp      = 0;
        $this->nro_comp_pago   = null;
        $this->precio          = '';
        $this->producto        = 'Elegir';
        $this->selected_id     = 0;
        $this->subproducto     = 'Elegir';
        $this->importeCompPago = null;
        $this->entrega          = 0;
        $this->saldo            = 0;
        $this->costo            = null;
    }    
    public function resetInputTodos()
    {
        $this->action           = 1;
        $this->articulos        = '';
        $this->barcode          = null;
        $this->cantidad         = 1;
        $this->cliente          = 'Elegir';
        $this->clienteId        = null;
        $this->controlar_stock  = 'no';
        $this->consignatario    = 'Elegir';
        $this->delivery         = 0;
        $this->dirCliente       = null;
        $this->empleado         = 'Elegir';
        $this->es_producto      = 1;
        $this->estado_entrega   = '0';
        $this->factura_id       = null;
        $this->facturaPendiente = null;
        $this->inicio_factura   = true;
        $this->lista            = '1';
        $this->mostrar_sp       = 0;
        $this->precio           = '';        
        $this->producto         = 'Elegir';
        $this->selected_id      = 0;
        $this->subproducto      = 'Elegir';
        $this->importeCompPago  = null;
        $this->entrega          = 0;
        $this->saldo            = 0;
    }
    protected $listeners = [
        'modCliRep'         => 'modCliRep',
        'deleteRow'         => 'destroy',
        'cobrar_factura'    => 'cobrar_factura' ,
        'factura_ctacte'    => 'factura_ctacte',      
        'anularFactura'     => 'anularFactura',
        'enviarDatosPago'   => 'enviarDatosPago',
        'dejar_pendiente'   => 'dejar_pendiente', 
        'StoreOrUpdate'     => 'StoreOrUpdate',
        'ocultar_sp'        => 'ocultar_sp',
        'agregarBanco'      => 'agregarBanco',
        'enviarDatosCheque' => 'agregarCheque',
        'guardarCliente'    =>'guardarCliente'
        // 'elegirFormaDePago' => 'elegirFormaDePago',
    ];
    public function ocultar_sp()
    {
        $this->mostrar_sp = 0;
        $this->articulos = null;
    }
    public function usarLista($numero)
    {
        $this->lista = $numero;
        $texto = ""; 
        if($this->comercioTipo == 11) $texto = "La Factura descontará el Stock Local";
        if($numero == '3'){
            $texto="La Factura descontará el Stock del Consignatario";
            $this->emit('listaNro','Consignatarios', $texto);
        }else $this->emit('listaNro', $this->lista, $texto);
    }
    public function verSaldo($id)
    { 
        $this->saldoCtaCte = 0;          
        $info = Ctacte::join('clientes as c', 'c.id', 'cta_cte.cliente_id')
            ->where('c.id', $id)
            ->where('c.comercio_id', $this->comercioId)
            ->select('cta_cte.cliente_id', DB::RAW("'' as importe"))->get(); 

        foreach($info as $i) {
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

        //$afip = new Afip(array('CUIT' => $CUIT));

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
    public function buscarPorCodigo() //codigo de remitos
    {
        if($this->barcode != null){
            $this->mostrar_sp = 0;
            $this->articulos = null;
            $articulos = Producto::where('codigo', $this->barcode)
                            ->where('comercio_id', $this->comercioId)->first();
            if ($articulos) $this->producto = $articulos->id;
            else session()->flash('msg-error', 'El Código no existe...');
        }
    }   
	public function buscarArticulo($id)
	{
        $this->resetInput();
		$this->articulos = Producto::where('comercio_id', $this->comercioId)
                                ->where('categoria_id', $id)->orderBy('descripcion')->get();
	}    
    public function buscarProducto($id)  //codigo original facturas
    {
        $pvta = Producto::select()->where('comercio_id', $this->comercioId)->where('codigo', $id)->get();
        if ($pvta->count()){
            $this->producto = $pvta[0]->id;
        }else{
            $this->producto = "Elegir";
            session()->flash('msg-error', 'El Código no existe...');
        } 
    }
    public function edit($id, $es_producto)
    {
        $this->selected_id = $id;
        $this->es_producto = $es_producto;
        $record = DetFactura::find($id);
        if($this->es_producto == 1) $this->producto = $record->producto_id;
        else $this->subproducto = $record->subproducto_id;
        $this->precio   = $record->precio;
        $this->cantidad = $record->cantidad;
    }
    public function StoreOrUpdateButton($articuloId)
    {
        if($articuloId != 0){                  //si cargo desde los botones
            if($this->es_producto == 1){        //si cargué un producto, verifico si tiene subproductos
                $this->tiene_sp = Subproducto::where('producto_id', $articuloId)->get();
                if($this->tiene_sp->count()){   //si tiene, los muestro
                    $this->mostrar_sp = 1;
                    $this->es_producto = 0; 
                }   
                else $this->verificar_stock($articuloId); //sino valido stock producto
            }else $this->verificar_stock($articuloId); //si cargué un subproducto, valido su stock
        }else{                                 //si cargo desde el form
            if($this->es_producto == 1){       //si es un producto, verifico si tiene subproductos
                $record = Subproducto::where('producto_id', $this->producto)->get();
                if($record->count()){          //si tiene, los muestro en el select
                    $this->es_producto = 0;
                    //return;
                }else $this->verificar_stock($this->producto); //si no tiene subproductos,valido stock producto
            }else $this->verificar_stock($this->subproducto); //si cargué un subproducto, valido su stock
        }
    }
    public function verificar_stock($id)
    {
        //consulto si se controla el stock del producto o subproducto cargado
        if($this->es_producto == 1) $producto = Producto::where('id', $id)->first();
        else{
            $producto = Subproducto::join('productos as p', 'p.id', 'subproductos.producto_id')
                ->where('subproductos.id', $id)
                ->select('p.precio_venta_l1', 'p.precio_venta_l2', 'p.controlar_stock')->first();
        }
        $this->controlar_stock = $producto->controlar_stock;

        if($this->inicio_factura && $this->consignatario == 'Elegir' && $this->lista == '3') $this->emit('cargar_consignatario');
        else{
            if($this->controlar_stock == 'si'){     //si se controla el stock 
                if($this->selected_id > 0){                 //si modificamos item
                    $record = Detfactura::find($this->selected_id);
                    $cantidad_detalle = $record->cantidad;
                    if($this->lista != '3'){
                        if($this->es_producto == 1) $stock = Stock::where('producto_id', $id)->first();
                        else $stock = Stock::where('subproducto_id', $id)->first();   
                        $stock_local = $stock->stock_actual; 
                        $stock_local_nuevo = $stock_local + $cantidad_detalle; 
                        if($stock_local_nuevo == null) $stock_local_nuevo = 0;
                    }
                    if($this->lista == '1'){              //verifico stock local
                        if($stock_local_nuevo >= $this->cantidad){
                            $this->precio = $producto->precio_venta_l1;
                            $this->costo = $producto->precio_costo;
                            $this->StoreOrUpdate($id);
                        }else $this->emit('stock_no_disponible', 'local', $stock_local); $this->resetInput(); 
                    }elseif($this->lista == '2'){         //verifico stock local 
                        if($stock_local_nuevo >= $this->cantidad){
                            $this->precio = $producto->precio_venta_l2;
                            $this->costo = $producto->precio_costo;
                            $this->StoreOrUpdate($id);
                        }else $this->emit('stock_no_disponible', 'local', $stock_local); $this->resetInput();
                    }else{                                //verifico stock consignatario
                        if($this->es_producto == 1){
                            $stock_consignacion = StockEnConsignacion::where('producto_id', $id)
                                ->where('cliente_id', $this->clienteId)->get()->sum('cantidad');  
                        }else{
                            $stock_consignacion = StockEnConsignacion::where('subproducto_id', $id)
                                ->where('cliente_id', $this->clienteId)->get()->sum('cantidad');
                        }
                        $stock_consignacion_nuevo = $stock_consignacion + $cantidad_detalle;

                        if($stock_consignacion_nuevo == null) $stock_consignacion_nuevo = 0;
                        if($stock_consignacion_nuevo >= $this->cantidad){
                            $this->precio = $producto->precio_venta_l2;
                            $this->costo = $producto->precio_costo; 
                            $this->StoreOrUpdate($id);
                        }else{
                            $this->emit('stock_no_disponible', 'en consignación', $stock_consignacion);
                            $this->resetInput();
                        }  
                    }
                }else{                                     //si creamos item 
                    if($this->es_producto == 1){           //si es un producto
                        $existe = Detfactura::select('id', 'cantidad') //buscamos si ya está cargado
                            ->where('factura_id', $this->factura_id)
                            ->where('comercio_id', $this->comercioId)
                            ->where('producto_id', $id)->get();  
                    }else{                                 //sino
                        $existe = Detfactura::select('id', 'cantidad') //buscamos si el subproducto ya está cargado
                            ->where('factura_id', $this->factura_id)
                            ->where('comercio_id', $this->comercioId)
                            ->where('subproducto_id', $id)->get();
                    }
                    if($existe->count()) $cantidad_detalle = $existe[0]->cantidad;
                    else $cantidad_detalle = 0;

                    if($this->lista != '3'){
                        if($this->es_producto == 1) $stock = Stock::where('producto_id', $id)->first();
                        else $stock = Stock::where('subproducto_id', $id)->first();
                        if($stock != null) $stock_local = $stock->stock_actual;
                        else return;
                    }
                    if($this->lista == '1'){
                        if($stock_local >= $this->cantidad){
                            $this->precio = $producto->precio_venta_l1;
                            $this->costo = $producto->precio_costo;
                            $this->StoreOrUpdate($id);
                        }else $this->emit('stock_no_disponible', 'local', $stock_local); $this->resetInput(); 
                    }elseif($this->lista == '2'){ 
                        if($stock_local >= $this->cantidad){
                            $this->precio = $producto->precio_venta_l2;
                            $this->costo = $producto->precio_costo;
                            $this->StoreOrUpdate($id);
                        }else $this->emit('stock_no_disponible', 'local', $stock_local); $this->resetInput();
                    }else{
                        $cliente = null;
                        if($this->inicio_factura) $cliente = $this->consignatario;
                        else $cliente = $this->clienteId;
                        if($this->es_producto == 1){
                            $stock_consignacion = StockEnConsignacion::where('producto_id', $id)
                                ->where('cliente_id', $cliente)
                                ->get()->sum('cantidad');
                        }else{
                            $stock_consignacion = StockEnConsignacion::where('subproducto_id', $id)
                                ->where('cliente_id', $cliente)
                                ->get()->sum('cantidad');
                        }                    
                        if($stock_consignacion >= $this->cantidad){
                            $this->precio = $producto->precio_venta_l2;
                            $this->costo = $producto->precio_costo; 
                            $this->StoreOrUpdate($id);
                        }else{
                            $this->emit('stock_no_disponible', 'en consignación', $stock_consignacion);
                            $this->resetInput();
                        }  
                    }
                }
            }else{       //si no se controla stock
                if($this->lista == '1') $this->precio = $producto->precio_venta_l1;
                elseif($this->lista == '2') $this->precio = $producto->precio_venta_l2;
                else $this->precio = $producto->precio_venta_l2;
                $this->costo = $producto->precio_costo;
                $this->StoreOrUpdate($id);
            }
        }
    }
    public function StoreOrUpdate($id)
    {       
        $this->validate(['precio' => 'required']);
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
                $record->update(['cantidad' => $this->cantidad]);
                
                if($this->controlar_stock == 'si'){
                    //si se controla el stock del producto o subproducto, lo modifico
                    if($this->lista != '3'){        //si se descuenta solo del stock local    
                        if($this->es_producto == 1) $record = Stock::where('producto_id', $id)->first();  
                        else $record = Stock::where('subproducto_id', $id)->first(); 
                        $stockActual = $record['stock_actual'] + $cantidad_detalle;
                        $stockNuevo = $stockActual - $this->cantidad;  
                        $record->update(['stock_actual' => $stockNuevo]);   
                    }else{                          //stock consignatario
                        $this->cantidad = -1 * abs($this->cantidad);  //invierte el signo  
                        if($this->es_producto == 1){
                            $existe = StockEnConsignacion::select('id')  //solo modifico la cantidad
                                ->where('factura_id', $this->factura_id)
                                ->where('comercio_id', $this->comercioId)
                                ->where('producto_id', $id)->get();
                        }else{
                            $existe = StockEnConsignacion::select('id')  //solo modifico la cantidad
                                ->where('factura_id', $this->factura_id)
                                ->where('comercio_id', $this->comercioId)
                                ->where('subproducto_id', $id)->get();
                        }
                        if($existe->count()){
                            $record = StockEnConsignacion::find($existe[0]->id);
                            $record->update(['cantidad' => $this->cantidad]); 
                        }
                    }
                }
            }else {                             //crea
                //inicializa variables
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
                //crea la factura
                if($this->inicio_factura) {
                    $this->totalAgrabar = $this->total + ($this->cantidad * $this->precio);
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
                if($this->es_producto == 1){         //si es un producto
                    $existe = Detfactura::select('id')       //buscamos si ya está cargado
                        ->where('factura_id', $this->factura_id)
                        ->where('comercio_id', $this->comercioId)
                        ->where('producto_id', $id)->get();  
                }else{                                 //sino
                    $existe = Detfactura::select('id') //buscamos si el subproducto ya está cargado
                        ->where('factura_id', $this->factura_id)
                        ->where('comercio_id', $this->comercioId)
                        ->where('subproducto_id', $id)->get();
                }
                if ($existe->count()){                    //actualizamos solo la cantidad
                    $edit_cantidad = Detfactura::find($existe[0]->id); 
                    $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
                    $edit_cantidad->update(['cantidad' => $nueva_cantidad]);
                }else{                                    //creamos un nuevo detalle
                    if($this->contador_filas == 10 && $this->imp_por_hoja != '1') $this->emit('limite_10');
                    elseif($this->contador_filas == 20 && $this->imp_por_hoja == '1') $this->emit('limite_20');
                    else{
                        if($this->es_producto == 1){
                            $add_item = Detfactura::create([         //creamos un nuevo detalle
                                'factura_id'  => $this->factura_id,
                                'producto_id' => $id,
                                'cantidad'    => $this->cantidad,
                                'precio'      => $this->precio,
                                'costo'       => $this->costo,
                                'comercio_id' => $this->comercioId
                            ]);    
                        }else{ 
                            $add_item = Detfactura::create([         //creamos un nuevo detalle
                                'factura_id'  => $this->factura_id,
                                'subproducto_id' => $id,
                                'cantidad'    => $this->cantidad,
                                'precio'      => $this->precio,
                                'costo'       => $this->costo,
                                'comercio_id' => $this->comercioId
                            ]);      
                        }                     
                    }
                }  
                if($this->controlar_stock == 'si'){
                    //si se controla el stock del producto o subproducto, lo modifico
                    if($this->lista != '3'){        //stock local    
                        if($this->es_producto == 1) $record = Stock::where('producto_id', $id)->first(); 
                        else $record = Stock::where('subproducto_id', $id)->first();  
                        $stockAnterior = $record['stock_actual'];
                        $stockNuevo = $stockAnterior - $this->cantidad;  
                        $record->update(['stock_actual' => $stockNuevo]);   
                    }else{                          //stock consignatario
                        $this->cantidad = -1 * abs($this->cantidad);  //invierte el signo 
                        if($this->es_producto == 1){             //modifico stock en consignación
                            $existe = StockEnConsignacion::select('id')  
                                ->where('factura_id', $this->factura_id)
                                ->where('comercio_id', $this->comercioId)
                                ->where('producto_id', $id)->get();
                        }else{
                            $existe = StockEnConsignacion::select('id')  
                                ->where('factura_id', $this->factura_id)
                                ->where('comercio_id', $this->comercioId)
                                ->where('subproducto_id', $id)->get();   
                        }
                        if ($existe->count()){                                //si el producto ya está cargado
                            $edit_cantidad = StockEnConsignacion::find($existe[0]->id); 
                            $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
                            $edit_cantidad->update(['cantidad' => $nueva_cantidad]);//actualizamos solo la cantidad
                        }else{
                            if($this->es_producto == 1){
                                $inv_en_consig = StockEnConsignacion::create([   //sino creamos uno nuevo
                                    'cliente_id'  => $cliente,
                                    'factura_id'  => $this->factura_id,
                                    'producto_id' => $id,
                                    'cantidad'    => $this->cantidad,
                                    'comercio_id' => $this->comercioId
                                ]);
                            }else{
                                $inv_en_consig = StockEnConsignacion::create([    //sino creamos uno nuevo
                                    'cliente_id'     => $cliente,
                                    'factura_id'     => $this->factura_id,
                                    'subproducto_id' => $id,
                                    'cantidad'       => $this->cantidad,
                                    'comercio_id'    => $this->comercioId
                                ]);  
                            }
                        } 
                    }
                }
            }
            //actualizo el total de la factura
            if(!$this->inicio_factura) {
                $detalle = DetFactura::where('factura_id', $this->factura_id)
                    ->select('cantidad', 'precio')->get();
                $importe = 0;    
                if($detalle->count()){
                    foreach ($detalle as $i) $importe += $i->cantidad * $i->precio; 
                }
                $this->totalAgrabar = $importe;
                $record = Factura::find($this->factura_id);  
                $record->update(['importe' => $this->totalAgrabar]);
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
    public function agregarBanco($data)
    {
        $info = json_decode($data);
        DB::begintransaction();                 
        try{
            $add_item = Banco::create([         
                'descripcion' => mb_strtoupper($info->banco),
                'sucursal'    => ucwords($info->sucursal),
                'comercio_id' => $this->comercioId
            ]);
            $this->bancos = $add_item->id;
            DB::commit();
            $this->emit('bancoCreado');  
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }     
        $this->f_de_pago = '1'; 
        return;  
    }    
    public function agregarCheque($data)
    {
        $info = json_decode($data);
        if($info->importe > $this->saldo){
            $this->emit('importeMayorQueSaldo');
            return;
        }else{
            DB::begintransaction();                 
            try{
                $add_item = Cheque::create([         
                    'cliente_id'       => $this->clienteId,
                    'banco_id'         => $info->banco,
                    'numero'           => $info->numero,
                    'fecha_de_emision' => Carbon::parse($info->fechaDeEmision)->format('Y,m,d') . ' 00:00:00',
                    'fecha_de_pago'    => Carbon::parse($info->fechaDePago)->format('Y,m,d') . ' 00:00:00',
                    'importe'          => $info->importe,
                    'estado'           => 'en_caja',
                    'cuit_titular'     => $info->cuitTitular,
                    'comercio_id'      => $this->comercioId
                ]);
                $record = DetMetodoPago::create([ 
                    'factura_id'    => $this->factura_id,
                    'medio_de_pago' => '5',
                    'num_comp_pago' => $info->numero, 
                    'importe'       => $info->importe,
                    'arqueo_id'     => $this->nro_arqueo,
                    'comercio_id'   => $this->comercioId
                ]);
                if($info->terminarFactura == 1){    //si se cancela la factura
                    $record = Factura::find($this->factura_id);
                    $record->update([
                        'estado'        => 'contado',      //indica el estado de la factura
                        'estado_pago'   => '1',            //0 ctacte, 1 pagado, 2 entrega
                        'importe'       => $this->total,
                        'comentario'    => $this->comentarioPago
                    ]);
                    $this->emit('facturaCobrada');
                }else $this->emit('cobroRegistrado'); 

                DB::commit();  
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
            }     
            session(['facturaPendiente' => null]);
            if($info->terminarFactura == 1) $this->resetInputTodos();
            else $this->resetMediosDePago(); 
        }
    }  
    public function cobrar_factura($formaDePago, $nroCompPago, $importe, $terminarFactura)
    {
        if($importe > $this->saldo){
            $this->emit('importeMayorQueSaldo');
            return;
        }else{
            $this->f_de_pago = $formaDePago;
            if($nroCompPago <> '') $this->nro_comp_pago = $nroCompPago;
            else $this->nro_comp_pago = null;            
            $this->importeCompPago = $importe;

            DB::begintransaction();                        
            try{
                $record = DetMetodoPago::create([ 
                    'factura_id'    => $this->factura_id,
                    'medio_de_pago' => $this->f_de_pago,
                    'num_comp_pago' => $this->nro_comp_pago, 
                    'importe'       => $this->importeCompPago,
                    'arqueo_id'     => $this->nro_arqueo, //nro. de arqueo de caja de quien cobra la factura
                    'comercio_id'   => $this->comercioId
                ]);
                if($terminarFactura == 1){    //si se cancela la factura
                    $factura = Factura::find($this->factura_id);
                    $factura->update([
                        'estado'        => 'contado',      //indica el estado de la factura
                        'estado_pago'   => '1',            //0 ctacte, 1 pagado, 2 entrega
                        'importe'       => $this->total,
                        'comentario'    => $this->comentarioPago
                    ]);
                    $this->emit('facturaCobrada');
                }else $this->emit('cobroRegistrado'); 
                
                DB::commit();
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
            }  
            session(['facturaPendiente' => null]);
            if($terminarFactura == 1) $this->resetInputTodos();
            else $this->resetMediosDePago();
        }
    }
    public function factura_ctacte()
    {
        DB::begintransaction();                        
        try{ 
            $record = Factura::find($this->factura_id);
            $record->update([
                'cliente_id'  => $this->clienteId,
                'estado'      => 'ctacte',
                'estado_pago' => '0',
                'importe'     => $this->total
            ]);
            Ctacte::create([
                'cliente_id' => $this->clienteId,
                'factura_id' => $this->factura_id,
                'estado'     => '1'
            ]);
            $record = Cliente::find($this->clienteId); //marca que el cliente tiene un saldo en ctacte
            $record->update(['saldo' => '1']);

            DB::commit();               
            $this->emit('facturaCtaCte');
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        session(['facturaPendiente' => null]); 
        $this->resetInputTodos();
    }        
    public function dejar_pendiente()
    {
        DB::begintransaction();                         //iniciar transacción para grabar
        try{
            $record = Factura::find($this->factura_id);
            $record->update([
                'estado' => 'pendiente',
                'importe' => $this->total
            ]);
            DB::commit();
            session()->flash('message', 'Factura Pendiente');
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }  
        session(['facturaPendiente' => null]); 
        $this->resetInputTodos();
    }
    public function guardarCliente($clienteId)
    {
        if($clienteId){
            $this->clienteId = $clienteId;
            $record = Factura::find($this->factura_id);
            $record->update(['cliente_id'=> $clienteId]);
            $this->emit('cliente_agregado');
        }        
    }
    public function modCliRep($data)
    {       
        $info = json_decode($data);
        $this->clienteId = $info->cliente_id;
        $repartidor='';
        //si el repartidor es el Salon, el nro_arqueo debe ser el de la caja que está facturando
        if($info->empleado_id == "Salon"){
            $repartidor = $this->salonId; 
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
        $loc     = Localidad::find($dataCli->localidad_id);
        $dataRep = User::find($repartidor);

        if($this->inicio_factura) {
            $this->mostrar_datos = 1;
            $this->apeNomCli = $dataCli->apellido . ' ' . $dataCli->nombre;
            $this->dirCliente = $dataCli->calle . ' ' . $dataCli->numero . ' - ' . $loc->descripcion;
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
            $this->cliente = $info->cliente_id;    ////ojo/////
        }
        session()->flash('message', 'Encabezado Modificado...');
    }
    public function destroy($id, $es_producto) 
    {
        if ($id) {
            $this->es_producto = $es_producto;
            DB::begintransaction();
            try{
                $detFactura = Detfactura::find($id);
                if($this->es_producto == 1) $producto_id = $detFactura->producto_id;
                else $producto_id = $detFactura->subproducto_id;
                $cantidad = $detFactura->cantidad;
                $detFactura->delete(); //elimina el item del detalle

                //actualizo el total de la factura
                $detalle = DetFactura::where('factura_id', $this->factura_id)
                    ->select('cantidad', 'precio')->get();
                $importe = 0;    
                if($detalle->count()){
                    foreach ($detalle as $i) $importe += $i->cantidad * $i->precio;
                }
                $this->totalAgrabar = $importe;
                $record = Factura::find($this->factura_id);  
                $record->update(['importe' => $this->totalAgrabar]);

                //consulto si se controla stock del producto o subproducto a eliminar
                if($this->es_producto == 1){
                    $record = Producto::where('id', $producto_id)->first();
                }else{
                    $record = Subproducto::where('id', $producto_id)->first();  
                    $producto = $record['producto_id'];
                    $record = Producto::where('id', $producto)->first();
                }
                $controlar_stock = $record->controlar_stock;

                if($controlar_stock == 'si'){  //si se controla el stock 
                    //actualizo stock
                    if($this->lista != '3'){    //si está en uso el stock local
                        if($this->es_producto == 1) $record = Stock::where('producto_id', $producto_id)->first();   
                        else $record = Stock::where('subproducto_id', $producto_id)->first();  
                        $stockAnterior = $record['stock_actual'];
                        $stockNuevo = $stockAnterior + $cantidad;  
                        $record->update(['stock_actual' => $stockNuevo]);   
                    }else{                       //si está en uso el stock en consignación
                        if($this->es_producto == 1){            //elimino item del producto
                            $existe = StockEnConsignacion::select('id')  
                                ->where('factura_id', $this->factura_id)
                                ->where('comercio_id', $this->comercioId)
                                ->where('producto_id', $producto_id)->get();
                        }else{                                  //elimino item del subproducto
                            $existe = StockEnConsignacion::select('id')  
                                ->where('factura_id', $this->factura_id)
                                ->where('comercio_id', $this->comercioId)
                                ->where('subproducto_id', $producto_id)->get();   
                        }
                        $record = StockEnConsignacion::find($existe[0]->id)->delete(); 
                    }
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
                $factura->update([ 'estado' => 'anulado']);

                $factura = Factura::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Facturas',
                    'estado'          => '0',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $comentario,
                    'comercio_id'     => $this->comercioId
                ]);
                //actualizo stock
                $record = Detfactura::where('factura_id', $id)->get();
                foreach ($record as $i){
                    //consulto si se controla stock del producto o subproducto a eliminar
                    if($i->producto_id != null){
                        $producto = Producto::where('id', $i->producto_id)->first();
                    }else{
                        $subproducto = Subproducto::where('id', $i->subproducto_id)->first();  
                        $producto_id = $subproducto['producto_id'];
                        $producto = Producto::where('id', $producto_id)->first();
                    }
                    $controlar_stock = $producto->controlar_stock;
                    
                    if($controlar_stock == 'si'){  //si se controla el stock 
                        if($this->lista != '3'){    //si está en uso el stock local
                            if($i->producto_id != null) $record = Stock::where('producto_id', $i->producto_id)->first(); 
                            else $record = Stock::where('subproducto_id', $i->subproducto_id)->first();  
                            $stockAnterior = $record['stock_actual'];
                            $stockNuevo = $stockAnterior + $i->cantidad;  
                            $record->update(['stock_actual' => $stockNuevo]);   
                        }else{                       //si está en uso el stock en consignación
                            $record = StockEnConsignacion::where('factura_id', $id)->get();
                            foreach ($record as $i) $i->delete();
                        }
                    }
                }
                session()->flash('msg-ok', 'Registro Anulado con éxito!!');
                DB::commit();               
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se anuló...');
            }
            $this->resetInputTodos();
            return;
        }
    }
    // public function elegirFormaDePago()
    // {
    //     if($this->clienteId != ''){
    //         $cli = Cliente::where('id', $this->clienteId)->get();
    //         $this->nomCli = $cli[0]->apellido . ' ' . $cli[0]->nombre;
    //     }
    //     $this->f_de_pago = '1';        
    //     $this->doAction(2);
    // }
    public function enviarDatosPago($tipo,$nro,$importe)
    {
        $this->f_de_pago = $tipo;
        $this->nro_comp_pago = $nro;
        $this->importeCompPago = $importe;
    }
    public function grabarImpresion()
    {
        DB::begintransaction();
        try{
            $factura = Factura::find($this->factura_id)->update(['impresion' => 1]);   
            DB::commit();              
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! La impresión no se grabó...');
        }
        return;
    }
}