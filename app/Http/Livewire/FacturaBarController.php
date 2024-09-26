<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Traits\GenericTrait;
use App\Models\Auditoria;
use App\Models\Banco;
use App\Models\CajaUsuario;
use App\Models\Categoria;
use App\Models\Cheque;
use App\Models\Cliente;
use App\Models\Comanda;
use App\Models\Comercio;
use App\Models\Ctacte;
use App\Models\Detcomanda;
use App\Models\Detfactura;
use App\Models\DetMetodoPago;
use App\Models\DetReceta;
use App\Models\Factura;
use App\Models\Guarnicion;
use App\Models\Localidad;
use App\Models\Mesa;
use App\Models\Peps;
use App\Models\Producto;
use App\Models\Receta;
use App\Models\Reserva;
use App\Models\Rubro;
use App\Models\Salsa;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Log;

class FacturaBarController extends Component
{
    use GenericTrait;
	//properties
    public $stock, $cantidad = 1, $precio, $costo, $estado='ABIERTO', $inicio_factura, $mostrar_datos, $facturaPendiente;
    public $cliente="Elegir", $empleado="Elegir", $producto="Elegir", $mesa="Elegir", $mozo="Elegir", $salon =null;
    public $clientes, $empleados, $productos, $mesas, $mozos, $rubros, $bancos = "Elegir", $esConsFinal;
    public $selected_id = 0, $detalleFactId = 0, $agregar_quitar_producto = 1, $numFactura, $action = 1;
    public $facturas, $fecha, $total, $importe, $totalAgrabar, $delivery = 0, $entrega = 0, $saldo;  
    public $grabar_encabezado = true, $modificar, $codigo, $barcode;
    public $comercioId, $arqueoGralId, $factura_id = null, $categorias = null, $articulos =null, $saldoCtaCte, $saldoACobrar;
    public $dirCliente, $apeNomCli, $apeNomRep, $clienteId;
    public $impresion = 0, $comentario, $nro_arqueo, $fecha_inicio, $caja_abierta = 0, $estado_entrega = '0';
    public $f_de_pago = null, $nro_comp_pago = null, $comentarioPago = '', $mercadopago = null;
    public $estadoArqueoGral, $forzar_arqueo = 0, $ultima_factura = 0;
    public $salsas, $guarniciones, $salsa = 0, $guarnicion = 0, $texto_base = null, $comentario_comanda='null';
    public $comanda_id = null, $inicio_comanda, $sector_comanda, $texto_comanda, $cantidad_comanda;
    public $unir_comandas = 'no', $estadoComanda, $tab = 'factura', $infoComandaEnEspera;
    public $modDelivery, $modComandas, $modConsignaciones, $mesaId, $mozoId, $mesaDesc, $mozoDesc, $permisos = 1;
    public $mostrar_sp = 0, $tiene_sp, $es_producto = 1, $controlar_stock = 'no', $tiene_receta = false;
    public $camarero = null, $categoria_id, $rubro_id, $search;
    public $info = [], $infoMediosDePago = [], $encabezado, $infoComanda = [];
    public $lista, $importeCompPago, $nomCli, $permitir_carga_sin_stock = 'no', $venta_sin_stock_general, $se_imprime_en_comanda;
	
    public function mount()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        $this->modDelivery = session('modDelivery');
        $this->modComandas = session('modComandas');
        $this->modConsignaciones = session('modConsignaciones');
        $this->facturaPendiente = session('facturaPendiente');
        $this->estadoArqueoGral = session('estadoArqueoGral');

        //verifico para luego preguntar o no al vender sin stock
        $comercio = Comercio::where('id', $this->comercioId)->select('venta_sin_stock')->first();
        $this->venta_sin_stock_general = $comercio->venta_sin_stock;

        //averiguo el id del Cons Final
        $this->esConsFinal = Cliente::where('comercio_id', $this->comercioId)
            ->where('nombre', 'FINAL')->select('id')->first();
        $this->esConsFinal = $this->esConsFinal->id;

        $this->clientes = Cliente::where('comercio_id', $this->comercioId)->orderBy('apellido')->get();
              
        

        $this->rubros = Rubro::where('comercio_id', $this->comercioId)
                            ->where('mostrar', 'like', 'si')
                            ->select('id', 'descripcion')->orderBy('descripcion')->get();
        $this->salsas = Salsa::where('comercio_id', $this->comercioId)->select('id', 'descripcion')->orderBy('descripcion')->get();
        $this->guarniciones = Guarnicion::where('comercio_id', $this->comercioId)->select('id', 'descripcion')->orderBy('descripcion')->get();
        $this->mesas = Mesa::where('comercio_id', $this->comercioId)->select('id', 'descripcion')->orderBy('descripcion')->get();
        $this->bancos = Banco::where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();

        //muestro solo los mozos
        $this->mozos = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('uc.comercio_id', $this->comercioId)->select('users.*')->orderBy('apellido')->get();
  
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

    }

	public function render()
    {
        // $this->facturaAfip();

        $this->verificarCajaHabilitada();
  
        $this->abrirMesa();

        $this->verArqueoGral();    

        $this->verFacturaPendiente(); 

        if($this->factura_id) $this->verDetalleFactura();   
        
        $this->categorias = Categoria::join('rubros as r', 'r.id', 'categorias.rubro_id')
            ->where('categorias.rubro_id', $this->rubro_id)
            ->where('categorias.tipo_id', 2)
            ->where('r.comercio_id', $this->comercioId)
            ->orWhere('categorias.rubro_id', $this->rubro_id)
            ->where('categorias.tipo_id', 3)
            ->where('r.comercio_id', $this->comercioId)
            ->select('categorias.id', 'categorias.descripcion')->orderBy('categorias.descripcion')->get();

        if(strlen($this->search) > 0) {
            $this->articulos = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
                ->join('rubros as r', 'r.id', 'c.rubro_id')
                ->where('r.comercio_id', $this->comercioId)
                ->where('c.tipo_id', 'not like', 1)
                ->where('c.tipo_id', 'not like', 4)
                ->where('productos.descripcion', 'like', '%' . $this->search .'%')
                ->where('productos.estado', 'Disponible')
                ->select('productos.id', 'productos.descripcion')
                ->orderBy('productos.descripcion')->get();
            // $this->articulos = Producto::where('comercio_id', $this->comercioId)
            //     ->where('descripcion', 'like', '%' . $this->search .'%')
            //     ->where('tipo', 'not like', 'Art. Compra')
            //     ->where('tipo', 'not like', 'Art. Elaborado')
            //     ->where('estado', 'Disponible')
            //     ->select('id', 'descripcion')
            //     ->orderBy('descripcion')->get();
        }else{
            $this->articulos = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
                ->join('rubros as r', 'r.id', 'c.rubro_id')
                ->where('productos.categoria_id', $this->categoria_id)
                ->where('r.comercio_id', $this->comercioId)
                ->where('c.tipo_id', 'not like', 1)
                ->where('c.tipo_id', 'not like', 4)
                ->where('productos.estado', 'Disponible')
                ->select('productos.id', 'productos.descripcion')
                ->orderBy('productos.descripcion')->get();
            // $this->articulos = Producto::where('comercio_id', $this->comercioId)
            //     ->where('categoria_id', $this->categoria_id)
            //     ->where('tipo', 'not like', 'Art. Compra')
            //     ->where('tipo', 'not like', 'Art. Elaborado')
            //     ->where('estado', 'Disponible')
            //     ->select('id', 'descripcion')
            //     ->orderBy('descripcion')->get();
        }       


        //COMANDAS/////
        if($this->factura_id) $this->buscarComanda();
        

     ///////////////    

		return view('livewire.facturas.component-bar', [
            'info'             => $this->info,
            'infoMediosDePago' => $this->infoMediosDePago
		]);
    } 
    public function verificarCajaHabilitada()
    {
        //vemos si tenemos una caja habilitada con nuestro user_id
        $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', auth()->user()->id)
            ->where('caja_usuarios.estado', '1')->select('caja_usuarios.*')->get();
        if($caja_abierta->count() > 0){
            $this->caja_abierta = $caja_abierta->count();
            $this->nro_arqueo = $caja_abierta[0]->id;  //este es el nro_arqueo del cajero, pero puede cambiar por el del delivery
            $this->fecha_inicio = $caja_abierta[0]->created_at;
            //busca si hay que hacer el arqueo gral.
            $this->arqueoGralId = session('idArqueoGral');
            if($this->arqueoGralId == 0){  //debe hacer el arqueo gral.
                return view('arqueodecaja');
            }
        }
    }  
    public function abrirMesa()
    {
        $this->mesaId = session("idMesa");
        if($this->mesaId == 'd' || $this->mesaId == 'D'){  //si es delivery
            $this->delivery = 1;
            $this->mesaId = null;
            $this->inicio_factura = true;
        }else{
            $buscar_mesa = Mesa::find($this->mesaId);
            $this->mesaDesc = $buscar_mesa->descripcion;
            if ($buscar_mesa->estado != 'Ocupada' && $buscar_mesa->estado != 'C/factura') { //si es una mesa nueva
                //if(session()->has('idMozo')){             //si es una mesa nueva
                //$this->mozoDesc = session("idMozo");       //si queremos que muestre el id del mozo en la factura
                $mozo = session('idMozo');
                //dd($mozo);
                $idMozo = $mozo['lista'];
                $buscar_mozo = User::find($idMozo); //si queremos que muestre el nombre del mozo en la factura
                $this->mozoDesc = $buscar_mozo->apellido . ' ' . $buscar_mozo->name;
                $this->mozoId = $buscar_mozo->id;
            }else{
                $buscarMozo = Factura::where('comercio_id', $this->comercioId)
                    ->where('arqueo_id', $this->nro_arqueo)
                    ->where('mesa_id', $this->mesaId)
                    ->where('estado', 'abierta')
                    ->select('id', 'mozo_id')->get();
                if($buscarMozo->count() > 0){
                    $this->factura_id = $buscarMozo[0]->id; 
                    //$this->mozoDesc  = $buscarMozo[0]->mozo_id;    //si queremos que muestre el id del mozo en la factura
                    $buscar_mozo = User::find($buscarMozo[0]->mozo_id); //si queremos que muestre el nombre del mozo en la factura
                    $this->mozoDesc = $buscar_mozo->apellido . ' ' . $buscar_mozo->name;
                    $this->mozoId = $buscar_mozo->id;
                } else $this->permisos = 0;
                
            }
        }  
        if(strlen($this->mozoDesc) > 15) $this->mozoDesc = substr($this->mozoDesc,0,9) . " ...";
    }
    public function verFacturaPendiente()
    {
        if($this->facturaPendiente){   //si se trata de una factura pendiente, la mostramos              
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
            $this->nro_arqueo = $encabezado[0]->arqueo_id;  //me aseguro de que el nro_arqueo sea de quien cobra la factura
        }else {
            $encabezado = Factura::select('*')->where('comercio_id', $this->comercioId)->withTrashed()->get(); 
            //busco si hay alguna factura abierta para la mesa en cuestión
            if($encabezado->count()){
                if($this->factura_id){  //si hay alguna factura abierta debo ver si es de salón o delivery
                    $encabezado = Factura::find($this->factura_id);
                    if ($encabezado) {
                        if($encabezado->mesa_id != null){  //al tener mesa_id significa que no es delivery
                            $encabezado = Factura::join('mesas as m', 'm.id', 'facturas.mesa_id')
                                ->where('facturas.id', $this->factura_id)
                                ->select('facturas.*', 'facturas.numero as nroFact', 'm.descripcion')->get();
                            $this->inicio_factura = false;
                            $this->numFactura = $encabezado[0]->nroFact;
                            $this->fecha = $encabezado[0]->created_at;
                            $this->clienteId = $encabezado[0]->cliente_id;   ///AGREGADO//////
                            $this->delivery = 0;          //dice si la factura es delivery
                            $this->mostrar_datos = 0;     //muestra datos del modal, no de la BD  
                        }else{      //busco factura delivery
                            $encabezado = Factura::join('clientes as c','c.id','facturas.cliente_id')
                                ->join('users as u','u.id','facturas.repartidor_id')
                                ->where('facturas.id', $this->factura_id)
                                ->select('facturas.*', 'facturas.numero as nroFact','c.nombre as nomCli', 'c.apellido as apeCli','c.calle',
                                        'c.numero', 'u.name as nomRep', 'u.apellido as apeRep')->get();
                            $this->numFactura = $encabezado[0]->nroFact;
                            $this->fecha = $encabezado[0]->created_at;
                            $this->clienteId = $encabezado[0]->cliente_id;
                            $this->mesaDesc = $encabezado[0]->descripcion;
                            $this->inicio_factura = false;
                            $this->delivery = 1;
                            $this->estado_entrega = $encabezado[0]->estado_entrega;
                            $this->dirCliente = $encabezado[0]->calle . ' ' . $encabezado[0]->numero;
                            $this->verSaldo($encabezado[0]->cliente_id);
                            $this->mostrar_datos = 0;
                        }
                    }
                }else {             //si no hay una factura abierta para esa mesa, le sumo 1 a la última
                    $this->inicio_factura = true;
                    $encabezado = Factura::select('numero')->where('comercio_id', $this->comercioId)
                        ->withTrashed()->orderBy('numero', 'desc')->get();                             
                    $this->numFactura = $encabezado[0]->numero + 1;
                    $this->fecha = intval(date('Ymd'));
                    if($this->delivery == 1) $this->mesaId = null;
                    else $this->delivery = 0;
                }
            }else{  //si es la primera factura, le asigno el nro: 1
                $this->numFactura = 1;
                $this->inicio_factura = true;
                if($this->delivery == 1) $this->mesaId = null;
                else $this->delivery = 0;
            }
        }
        $this->encabezado = $encabezado;
    }
    public function verArqueoGral()
    {
        //BUSCO EL ESTADO DEL ARQUEO GENERAL    
        if($this->estadoArqueoGral == 'pendiente'){
            if($this->facturaPendiente) $this->factura_id = $this->facturaPendiente;
            //BUSCO SI HAY ALGUNA FACTURA ABIERTA PARA LA CAJA DEL USUARIO LOGUEADO
            $record = Factura::where('estado', 'abierta')
                ->where('comercio_id', $this->comercioId)
                ->orWhere('estado', 'pendiente')
                ->where('comercio_id', $this->comercioId)
                ->where('user_id', auth()->user()->id)->get();
            //SI HAY UNA O VARIAS, LO REDIRIJO A 'MESAS' PARA QUE LAS TERMINE
            $ultima = 0;
            if($record){
                foreach ($record as $i) {
                    if($i->id == $this->factura_id){
                        $ultima = 1;
                        break;
                    }                     
                }
            }

            //si hay alguna, la dejo terminar, pero al finalizar vuelvo al home   
            if($ultima == 1) $this->ultima_factura = 1;
            else $this->forzar_arqueo = 1;
        }
    }
    public function verDetalleFactura()
    {
        $this->info = Detfactura::join('facturas as f','f.id','detfacturas.factura_id')
            ->join('productos as p','p.id','detfacturas.producto_id')
            ->select('detfacturas.*', 'p.descripcion as producto', 'p.sectorcomanda_id', DB::RAW("'' as importe"))
            ->where('detfacturas.factura_id', $this->factura_id)
            ->orderBy('detfacturas.id', 'asc')->get(); 
        $this->total = 0;
        foreach ($this->info as $i){
            $i->importe=$i->cantidad * $i->precio;
            $this->total += $i->importe;
        }
        $this->infoMediosDePago = DetMetodoPago::where('factura_id', $this->factura_id)
            ->where('comercio_id', $this->comercioId)
            ->select('*', DB::RAW("'' as medio_pago"))->orderBy('id')->get(); 
        $this->entrega = 0;
        if($this->infoMediosDePago->count()){
            foreach($this->infoMediosDePago as $i){
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
    }
    public function buscarComanda()
    {
        $estadoCom = Comanda::select('*')
            ->where('factura_id', $this->factura_id)->orderBy('updated_at', 'desc')->get();
        //busco si hay una comanda con estado 'cargado', sino inicializo una
        if($estadoCom->count()){
            if($estadoCom[0]->estado == 'cargado') $this->inicio_comanda = false;
            elseif($estadoCom[0]->estado == 'en espera') $this->inicio_comanda = true;
            else $this->inicio_comanda = true;
            $this->comanda_id     = $estadoCom[0]->id;
            $this->estadoComanda  = $estadoCom[0]->estado;
        }else $this->inicio_comanda = true; 

        $this->infoComanda = Detcomanda::join('comandas as c','c.id','detcomandas.comanda_id')
            ->join('facturas as f','f.id','c.factura_id')
            ->join('productos as p','p.id','detcomandas.producto_id')
            ->where('c.factura_id', $this->factura_id)
            ->where('c.estado', 'cargado')
            ->where('f.estado', 'like', 'abierta')
            ->orWhere('c.factura_id', $this->factura_id)
            ->where('c.estado', 'cargado')
            ->where('f.estado', 'like', 'pendiente')
            ->select('detcomandas.*', 'p.sectorcomanda_id', 'p.controlar_stock')
            ->orderBy('detcomandas.descripcion')->get(); 
    
        $this->infoComandaEnEspera = Detcomanda::join('comandas as c','c.id','detcomandas.comanda_id')
            ->join('facturas as f','f.id','c.factura_id')
            ->where('c.factura_id', $this->factura_id)
            ->where('c.estado', 'en espera')
            ->where('f.estado', 'like', 'abierta')
            ->orWhere('c.factura_id', $this->factura_id)
            ->where('c.estado', 'en espera')
            ->where('f.estado', 'like', 'pendiente')
            ->select('detcomandas.*')
            ->orderBy('detcomandas.descripcion')->get(); 
    }
    public function ver_permisos()
    {
        $this->emit('sin_permisos');
    }
    public function facturaDelivery()
    {
        $this->emit('delivery');
    }
    public function doAction($action)
    {
        $this->action = $action;
        if($this->action == 1) $this->resetInput();   //agregado
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
        $this->cantidad           = 1;
        $this->barcode            = '';
        $this->precio             = '';
        $this->costo              = '';
        $this->producto           = 'Elegir';
        $this->selected_id        = 0;
        $this->impresion          = 0;
        $this->action             = 1;
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
        $this->tab                = 'factura';
        $this->unir_comandas      = 'no';
        $this->controlar_stock    = 'no';
        $this->mostrar_sp         = 0;
        $this->articulos          = null;
        $this->es_producto        = 1;
        $this->stock              = '';
        $this->detalleFactId      = 0;
        $this->agregar_quitar_producto = 1;
        $this->tiene_receta      = false;
        $this->saldo             = 0;
        $this->info              = [];
        $this->infoMediosDePago  = [];
        $this->se_imprime_en_comanda = null;
        $this->permitir_carga_sin_stock = 'no';
    } 
    public function ocultar_sp()
    {
        $this->mostrar_sp = 0;
        $this->articulos = null;
    }
    public function buscarPorCodigo() //codigo de remitos
    {
        if($this->barcode != null){
            $this->mostrar_sp = 0;
            $this->articulos = null;
            $articulos = Producto::where('codigo', $this->barcode)
                            ->where('tipo', 'not like', 'Art. Compra')
                            ->where('estado', 'Disponible')
                            ->where('comercio_id', $this->comercioId)->get();
            if ($articulos->count()) $this->producto = $articulos->id;
            else session()->flash('msg-error', 'El Código no existe...');
        }
    } 
    protected $listeners = [
        'modCliRep'             => 'modCliRep',
        'deleteRow'             => 'destroy',
        'factura_contado'       => 'factura_contado',
        'factura_ctacte'        => 'factura_ctacte',      
        'anularFactura'         => 'anularFactura',
        'eliminarEntrega'       => 'eliminarEntrega',
        'elegirFormaDePago'     => 'elegirFormaDePago',
        'enviarDatosPago'       => 'enviarDatosPago',
        'dejar_pendiente'       => 'dejar_pendiente', 
        'StoreOrUpdate'         => 'StoreOrUpdate',
        'unirComandas'          => 'unirComandas',
        'verComanda'            => 'verComanda',
        'modificarComanda'      => 'modificarComanda',
        'ocultar_sp'            => 'ocultar_sp',
        'eliminarItemComanda'   => 'eliminarItemComanda',
        'salir'                 => 'salir',
        'permitirCargaSinStock' => 'permitirCargaSinStock',
        'agregarBanco'          => 'agregarBanco',
        'enviarDatosCheque'     => 'agregarCheque',
        'cobrar_factura'        => 'cobrar_factura',
        'guardarCliente'        => 'guardarCliente',
        'grabarImpresion'       => 'grabarImpresion'
    ];
    public function permitirCargaSinStock($option, $id)
    {
        $this->permitir_carga_sin_stock = $option;
        if($option == 'si') $this->verSalsaGuarn($id);
    }
    public function salir()
    {
        $factura = Factura::find($this->factura_id);
        if($factura) $factura->update(['estado' => 'pendiente']);        
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
                ->join('det_metodo_pagos as det', 'det.recibo_id', 'cta_cte.recibo_id')
                ->where('r.cliente_id', $i->cliente_id)
                ->select('det.importe')->get();
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
            'CbteDesde' => 1,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
            'CbteHasta' => 1,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
            'CbteFch' 	=> intval(date('Ymd')), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
            'ImpTotal' 	=> 121, // Importe total del comprobante
            'ImpTotConc'=> 0,   // Importe neto no gravado
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
	public function buscarCategoria($id)
	{
		$this->rubro_id = $id;
        $this->categoria_id = '';
        $this->search = '';
	}    
	public function buscarArticulo($id)
	{
        $this->categoria_id = $id;
        $this->mostrar_sp = 0;
	}    
    public function buscarProducto($id)
    {
        $pvta = Producto::select()->where('codigo', $id)
            ->where('tipo', 'not like', 'Art. Compra')
            ->where('tipo', 'not like', 'Art. Elaborado')
            ->where('estado', 'Disponible')
            ->where('comercio_id', $this->comercioId)->get();
        
        if ($pvta->count() > 0){
            $this->producto = $pvta[0]->id;
        }else{
            $this->producto = "Elegir";
            session()->flash('msg-error', 'El Código no existe...');
        } 
    }
    public function modificarComanda($id)
    {
        $record = Detcomanda::find($id); 
        $cantidad_detalle = $record->cantidad; 
        $record->update([                       //actualizamos el registro
            'producto_id' => $this->producto,
            'cantidad'    => $this->cantidad,
        ]);
        if($this->controlar_stock == 'si'){
            $record = Stock::where('producto_id', $this->producto)->first();  
            $stockActual = $record['stock_actual'] + $cantidad_detalle;
            $stockNuevo = $stockActual - $this->cantidad;  
            $record->update(['stock_actual' => $stockNuevo]);  
        } 
    }
    public function StoreOrUpdateButton($articuloId, $accion, $detalleId, $precio)
    {
        if ($accion == 0) $this->permitir_carga_sin_stock = 'si';
        $this->agregar_quitar_producto = $accion;
        $this->detalleFactId = $detalleId;
        if($precio) $this->precio = $precio;
        $this->verificar_stock($articuloId);
    }
    public function verificar_stock($id)
    {
        //$this->cantidad = 1;
        $this->producto = $id;
           //consulto si se controla el stock del producto cargado
        $producto = Producto::find($id);
        if ($producto) {
            $this->controlar_stock = $producto->controlar_stock;
            if($producto->tiene_receta == 'si') $this->tiene_receta = true;
            if($producto->sectorcomanda_id) $this->se_imprime_en_comanda = true;

            //recupero los precios de costo y de venta
            $this->costo = $producto->precio_costo;
            if($this->delivery == 0) $this->precio = $producto->precio_venta_l1;
            else $this->precio = $producto->precio_venta_l2;
        } else return; 

        if($this->agregar_quitar_producto == 1){
            if($this->controlar_stock == 'si') {
                $verificar_stock = $this->verificarStockTrait($this->cantidad, $id, $producto->tipo);
                if ($verificar_stock[0] == 1) {
                    $this->emit('receta_sin_principal', $producto->descripcion); 
                    return;
                } elseif ($verificar_stock[0] == 2) {
                    if ($this->venta_sin_stock_general == 1) {
                        $this->emit('stock_receta_no_disponible_con_opcion',$verificar_stock[1], $verificar_stock[2]);
                    } else $this->emit('stock_receta_no_disponible_sin_opcion',$verificar_stock[1], $verificar_stock[2]);
                    return;
                } elseif ($verificar_stock[0] == 3) {
                    $this->emit('receta_sin_detalle', $producto->descripcion);
                    return; 
                } elseif ($verificar_stock[0] == 4) { //stock no disponible CON opción
                    $peps = Peps::where('producto_id', $id)->where('comercio_id', $this->comercioId)->sum('resto');
                    if ($this->venta_sin_stock_general == 1) {
                        $this->emit('stock_no_disponible_con_opcion', $peps, $producto->descripcion, $producto->id);
                    } else $this->emit('stock_no_disponible_sin_opcion', $peps, $producto->descripcion);
                    return;
                } elseif ($verificar_stock[0] == 5) { //stock no disponible SIN opción
                    $peps = Peps::where('producto_id', $id)->where('comercio_id', $this->comercioId)->sum('resto');
                    $this->emit('stock_no_disponible_sin_opcion', $peps, $producto->descripcion);
                    return; 
                } else $this->verSalsaGuarn();
            } else $this->verSalsaGuarn();
        } else $this->verSalsaGuarn();
    }
    public function verSalsaGuarn()
    {
        if($this->se_imprime_en_comanda) {
            $record = Producto::join('texto_base_comandas as tbc', 'tbc.id', 'productos.texto_base_comanda_id')
                ->where('productos.id', $this->producto)
                ->where('productos.comercio_id', $this->comercioId)
                ->select('productos.guarnicion', 'productos.salsa', 'productos.sectorcomanda_id', 'tbc.descripcion')
                ->get();  
            if($record->count()){ 
                $this->guarnicion = $record[0]->guarnicion;
                $this->salsa      = $record[0]->salsa;
                $this->texto_base = $record[0]->descripcion;
            }  
     
            if($this->estadoComanda == 'en espera'){
               $this->verEstadoComandas(); 
            }else $this->emit('modal_comanda');
        }else $this->StoreOrUpdate(1,'');
    }
    public function StoreOrUpdate($cantidad, $texto_comanda)
    {
        $this->cantidad = $cantidad;
       // dd($this->cantidad);
        $this->texto_comanda = $texto_comanda;

        $producto = Producto::where('id', $this->producto)->where('comercio_id', $this->comercioId)->first();
        $this->sector_comanda = $producto->sectorcomanda_id;

        $this->validate([
            'cantidad' => 'required',
            'producto' => 'required',
            'precio'   => 'required',
            'costo'    => 'required'
        ]);
        ////calculo el total a grabar en la factura
        if($this->agregar_quitar_producto == 0) $this->totalAgrabar = $this->total - ($this->cantidad * $this->precio); 
        else $this->totalAgrabar = $this->total + ($this->cantidad * $this->precio); 
        
        $actualizarStock = true;
        $grabarComanda = true;

        DB::begintransaction();           //iniciar transacción para grabar
        try{                              //si se resta un producto desde el botón "-"
            if($this->agregar_quitar_producto == 0){
                $record = DetFactura::find($this->detalleFactId); 
                $cantidad_detalle = $record->cantidad;
                $cantidad_nueva = $cantidad_detalle - 1;
                if($cantidad_nueva > 0) $record->update(['cantidad' => $cantidad_nueva]);
                else $record->delete();   //si la cantidad es 0, eliminamos el registro   
                //AUMENTAR STOCK///
                $actualizarStock = $this->actualizarStockTrait(3, false, false, null, $this->detalleFactId, $this->cantidad, $this->producto, null, null);                        
                //dd('actualizar: ' . $actualizarStock);
                ///COMANDA??????///
            }else {                 //si se quiere agregar una venta
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
                
                if($this->mesaId != null) $estadoFactura = 'pendiente';
                else $estadoFactura = 'abierta';
          
                if($this->inicio_factura) {
                    $factura = Factura::create([
                        'numero'         => $this->numFactura,
                        'cliente_id'     => $this->cliente,
                        'importe'        => $this->totalAgrabar,
                        'estado'         => 'abierta',
                        'estado_pago'    => '0',
                        'estado_entrega' => $this->estado_entrega,
                        'repartidor_id'  => $this->empleado,
                        'mozo_id'        => $this->mozoId,
                        'mesa_id'        => $this->mesaId,
                        'user_id'        => auth()->user()->id, //id de quien confecciona la factura
                        'impresion'      => $this->impresion,
                        'comercio_id'    => $this->comercioId,
                        'arqueo_id'      => $this->nro_arqueo   //nro. de arqueo de caja de quien cobra la factura
                    ]);
                    if($this->mesaId != null){
                        $mesa = Mesa::find($this->mesaId); 
                        if($mesa->estado == 'Reservada'){
                            $reserva = Reserva::where('mesa_id', $this->mesaId)->first();
                            $reserva->update(['estado'=> 'Concretada']);   
                        }
                        $mesa->update(['estado' => 'Ocupada']);   
                    }
                    //dd($mesa);
                    $this->factura_id = $factura->id;
                }                

                $detalleVentaId = null;
                $existe = Detfactura::select('id')           //buscamos si el producto ya está cargado
                    ->where('factura_id', $this->factura_id)
                    ->where('comercio_id', $this->comercioId)
                    ->where('producto_id', $this->producto)->get();
                if ($existe->count()){
                    $edit_cantidad = Detfactura::find($existe[0]->id); 
                    $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
                    $edit_cantidad->update(['cantidad' => $nueva_cantidad]);
                    $detalleVentaId = $existe[0]->id;
                }else{
                    $add_item = Detfactura::create([         //creamos un nuevo detalle
                        'factura_id'  => $this->factura_id,
                        'producto_id' => $this->producto,
                        'cantidad'    => $this->cantidad,
                        'precio'      => $this->precio,
                        'costo'       => $this->costo,
                        'comercio_id' => $this->comercioId
                    ]);	
                    $detalleVentaId = $add_item->id;
                }  

                //PEPS
                if($this->controlar_stock == 'si'){
                    //$accion, $agregarVenta, $inicioFactura, $detalleCompraId, $detalleVentaId, $cantidad, $productoId, $costoHistorico,$stockActual                         
                    if($this->permitir_carga_sin_stock == 'no'){
                        $actualizarStock = $this->actualizarStockTrait(3, true, $this->inicio_factura, null, $detalleVentaId, $this->cantidad, $this->producto, $this->precio, null);
                    }else{
                        $actualizarStock = $this->actualizarStockTrait(8, true, $this->inicio_factura, null, $detalleVentaId, $this->cantidad, $this->producto, $this->costo, null);
                    }
                }   

                //COMANDA////
                
                if($this->sector_comanda) $grabarComanda = $this->grabarComanda();
                
            } 
            $record = Factura::find($this->factura_id);  //actualizamos el encabezado
            $record->update(['importe' => $this->totalAgrabar]); 
       
            if ($actualizarStock && $grabarComanda){  //verifico si se grabaron todos los datos de stock y la comanda si existiera
                DB::commit();
                if($this->selected_id) session()->flash('message', 'Registro Actualizado');       
                else{
                    if($this->agregar_quitar_producto == 0) $this->emit('registroAgregado', 'descontado');
                    else $this->emit('registroAgregado', 'agregado'); 
                } 
            } else {
                DB::rollback();
                if (!$actualizarStock) {
                    session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó porque existen problemas al actualizar el Stock...');
                } else {
                    session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó porque existen problemas al actualizar la Comanda...');
                }
            }            
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
    public function verComanda()
    {
        $estadoCom = Comanda::select('*') //verifico si hay alguna comanda en espera para esta mesa
            ->where('factura_id', $this->factura_id)
            ->where('estado', 'en espera')
            ->orderBy('sent_at')->get();
        $this->infoComandaEnEspera = Detcomanda::join('comandas as c','c.id','detcomandas.comanda_id')
            ->join('facturas as f','f.id','c.factura_id')
            ->where('c.factura_id', $this->factura_id)
            ->where('c.estado', 'en espera')
            ->where('f.estado', 'like', 'abierta')
            ->orWhere('c.factura_id', $this->factura_id)
            ->where('c.estado', 'en espera')
            ->where('f.estado', 'like', 'pendiente')
            ->select('detcomandas.*')
            ->orderBy('detcomandas.descripcion')->get();
        $this->tab = 'verComanda'; 
    }
    public function enviarComanda()
    {
        if($this->comanda_id){
            $record = Comanda::find($this->comanda_id);
            if($record->estado == 'cargado'){
                if($this->unir_comandas == 'si'){
                    $record->update(['estado' => 'en espera']); //solo cambiamos el estado y respetamos la hora de envío histórica
                }else{
                    $record->update([
                        'estado'  => 'en espera',
                        'sent_at' => Carbon::now()      //cambiamos estado y fecha/hora enviado
                    ]);
                }
                $this->resetInput();
                $this->emit('comandaEnviada');
            } else{
                $this->tab = 'factura';
                $this->emit('comandaVacia');
            }
        } else{
            $this->tab = 'factura';
            $this->emit('comandaVacia');
        }
    }
    public function verEstadoComandas()
    {        
        $estadoCom = Comanda::select('*') //primero verifico si hay alguna comanda en espera para esta mesa
            ->where('factura_id', $this->factura_id)
            ->where('estado', 'en espera')
            ->orderBy('sent_at', 'desc')->get();
        if($estadoCom->count()){          // si hay, pregunto si se quiere agregar el nuevo item 
            $this->comanda_id = $estadoCom[0]->id;
            if($this->unir_comandas == 'no') $this->emit('comandaEnEspera'); 
        }
    }
    public function grabarComanda()
    {
        DB::beginTransaction();
        try {
            if($this->inicio_comanda) {
                $comanda = Comanda::create([
                    'factura_id'       => $this->factura_id,
                    'estado'           => 'cargado',
                    'sectorcomanda_id' => $this->sector_comanda,
                    'comercio_id'      => $this->comercioId
                ]);
                $this->inicio_comanda = false;
                $this->comanda_id = $comanda->id;
            }  
            $existe = Detcomanda::select('id') //buscamos si el producto ya está cargado
                ->where('comanda_id', $this->comanda_id)
                ->where('producto_id', $this->producto)
                ->where('descripcion', $this->texto_comanda)
                ->where('comercio_id', $this->comercioId)
                ->get();
            if ($existe->count()){
                $edit_cantidad = Detcomanda::find($existe[0]->id); 
                $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
                $edit_cantidad->update(['cantidad' => $nueva_cantidad]);
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

            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollback();
            return false;
        }    
    }
    public function unirComandas($si_no)
    {
        $this->unir_comandas = $si_no;
        if($this->unir_comandas == 'si'){
            $record = Comanda::find($this->comanda_id);
            $record->update(['estado' => 'cargado']);
            $this->inicio_comanda = false;
        }else $this->inicio_comanda = true;
        $this->emit('modal_comanda');
    }
    public function cobrar_factura($formaDePago, $nroCompPago, $importe, $terminarFactura)
    {
        if($this->delivery == 1 && $importe < $this->saldo){ //no se permiten pagos a cuenta sin antes haber
            $this->emit('primeroEnviarACtaCte');             //enviado la factura a cuenta corriente   
            return;
        }
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
                    $record = Factura::find($this->factura_id);
                    $record->update([
                        'estado'        => 'contado',      //indica el estado de la factura
                        'estado_pago'   => '1',            //0 ctacte, 1 pagado, 2 entrega
                        'importe'       => $this->total,
                        'comentario'    => $this->comentarioPago
                    ]);
                    if($this->mesaId != null){
                        $mesa = Mesa::find($this->mesaId);
                        $mesa->update(['estado' => 'Disponible']);
                    }                    
                } 
                DB::commit();
                if($terminarFactura == 1) $this->emit('facturaCobrada');   //si se cancela la factura
                else $this->emit('cobroRegistrado');
                
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
            }  
            session(['facturaPendiente' => null]);
            $this->enviarAviso();
            return redirect()->to('/reservas-estado-mesas');
        }
    }
    public function enviarAviso()
    {

    //     curl -i -X POST `
    //     https://graph.facebook.com/v20.0/463910756795777/messages `
    //     -H 'Authorization: Bearer EAAMdWVewkXsBOzKqLZAK73VHrvlG17SEnp2T7hiCeI22ATEprXLMQ86MjnVhJX7fIC9ti1Fv8TNdVQzH4itsC2K9kke6XgTPedJqGycS1tLALWpJdZB8rAyi3rkdTZARis2CSCDObij6jNZBOelM8RLwZAaOhK3Nr10hRm0rZC12L1IJvZCUumIrOiG7NbrNy0DWgE5PlqcE0imV7QFpPpnATKUFrpNVilBrGsZD' `
    //     -H 'Content-Type: application/json' `
    //     -d '{ \"messaging_product\": \"whatsapp\", \"to\": \"543564416940\", \"type\": \"template\", \"template\": { \"name\": \"hello_world\", \"language\": { \"code\": \"en_US\" } } }'
    }
    public function factura_ctacte()
    {
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
                'factura_id' => $this->factura_id,
                'estado'     => '1'
            ]);
            $record = Cliente::find($this->clienteId); //marca que el cliente tiene un saldo en ctacte
            $record->update(['saldo' => '1']);

            if($this->mesaId != null){
                $mesa = Mesa::find($this->mesaId);
                $mesa->update(['estado' => 'Disponible']);
            }

            DB::commit();               
            $this->emit('facturaCtaCte');
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        session(['facturaPendiente' => null]);
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
        return redirect()->to('/reservas-estado-mesas');
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
    public function guardarCliente($clienteId)
    {
        if($clienteId){
            $this->clienteId = $clienteId;
            $record = Factura::find($this->factura_id)->update(['cliente_id'=> $clienteId]);
            $this->emit('cliente_agregado');
        }        
    }
    public function eliminarItemComanda($id, $cantidad, $controlar_stock)
    {
        if ($id) {
            DB::begintransaction();
            try{ 
                //capturo la cantidad en la comanda y modifico o elimino el item del detalle comanda
                $detComanda = Detcomanda::where('id', $id)->first();
                $idComanda = $detComanda->comanda_id;
                $idProducto = $detComanda->producto_id;
                $cantidad_a_descontar = $detComanda->cantidad - $cantidad;
                if($cantidad_a_descontar > 0) $detComanda->update(['cantidad' => $cantidad_a_descontar]);    
                elseif($cantidad_a_descontar == 0) $det_comanda = Detcomanda::find($id)->delete();
                else session()->flash('msg-error', '¡¡¡ATENCIÓN!!! No existe la cantidad a descontar...');

                //modifico o elimino el item del detalle factura
                $detFactura = Detfactura::join('facturas as f', 'f.id', 'detfacturas.factura_id')
                    ->join('productos as p', 'p.id', 'detfacturas.producto_id')
                    ->where('f.id', $this->factura_id)
                    ->where('p.id', $idProducto)
                    ->select('detfacturas.*')->get();
          
                $cantidad_a_descontar = $detFactura[0]->cantidad - $cantidad;
                if($cantidad_a_descontar > 0){
                    $det_factura = Detfactura::find($detFactura[0]->id);
                    $det_factura->update(['cantidad' => $cantidad_a_descontar]);  
                }     
                elseif($cantidad_a_descontar == 0) $det_factura = Detfactura::find($detFactura[0]->id)->delete();
                else session()->flash('msg-error', '¡¡¡ATENCIÓN!!! No existe la cantidad a descontar...');

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

                //si se controla el stock 
                if($controlar_stock == 'si'){
                  
                        $record = Stock::where('producto_id', $idProducto)->first();    
                        $stockAnterior = $record['stock_actual'];
                        $stockNuevo = $stockAnterior + $cantidad;  
                        $record->update(['stock_actual' => $stockNuevo]);    
                 
                }
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
    public function destroy($id, $idProducto, $cantidad, $comanda) //elimina item
    {
        if ($id) {
            DB::begintransaction();
            try{ 
                if($comanda){
                    //capturo la cantidad en la comanda y modifico o elimino el item del detalle comanda
                    $detComanda = Detcomanda::where('id', $id)->first();
                    $idComanda = $detComanda->comanda_id;
                    $cantidad_a_descontar = $detComanda->cantidad - $cantidad;
                    if($cantidad_a_descontar > 0) $detComanda->update(['cantidad' => $cantidad_a_descontar]);    
                    elseif($cantidad_a_descontar == 0) $det_comanda = Detcomanda::find($id)->delete();
                    else session()->flash('msg-error', '¡¡¡ATENCIÓN!!! No existe la cantidad a descontar...');
                }

                //modifico o elimino el item del detalle factura
               
                    $detFactura = Detfactura::join('facturas as f', 'f.id', 'detfacturas.factura_id')
                        ->join('productos as p', 'p.id', 'detfacturas.producto_id')
                        ->where('f.id', $this->factura_id)
                        ->where('p.id', $idProducto)
                        ->select('detfacturas.*')->get();
        
                $cantidad_a_descontar = $detFactura[0]->cantidad - $cantidad;
                if($cantidad_a_descontar > 0){
                    $det_factura = Detfactura::find($detFactura[0]->id);
                    $det_factura->update(['cantidad' => $cantidad_a_descontar]);  
                }     
                elseif($cantidad_a_descontar == 0) $det_factura = Detfactura::find($detFactura[0]->id)->delete();
                else session()->flash('msg-error', '¡¡¡ATENCIÓN!!! No existe la cantidad a descontar...');

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

                $producto = Producto::find($id)->first();
                $this->controlar_stock = $producto->controlar_stock;
                //si se controla el stock             
                if($this->controlar_stock == 'si'){
                    if($this->tiene_receta){
                        $receta = Receta::where('producto_id', $idProducto)->first();
                        if($receta){    //si tiene detalle de receta cargado
                            $detReceta = DetReceta::join('productos as p', 'p.id', 'det_recetas.producto_id')
                                ->where('p.comercio_id', $this->comercioId)
                                ->where('det_recetas.receta_id', $receta->id)
                                ->select('det_recetas.*', 'p.descripcion')->get();
                            if($detReceta){
                                foreach ($detReceta as $i) {
                                    $cantidadTotalAAgregar = $cantidad * $i->cantidad;
                                    $stock_local = 0;
                                    $stock = Stock::where('producto_id', $i->producto_id)->first();
                dd($cantidadTotalAAgregar,$i->producto_id);
                                    $stock_local = $stock->stock_actual;
                                    $stock_nuevo = $stock_local + $cantidadTotalAAgregar;
                                    $stock->update(['stock_actual' => $stock_nuevo]);
                                }
                            }else{       //si no tiene el detalle de la receta cargado
                                $this->emit('receta_sin_detalle', $producto->descripcion); 
                                return;
                            }
                        }
                    }else{
                       
                            $record = Stock::where('producto_id', $idProducto)->first();    
                            $stockAnterior = $record['stock_actual'];
                            $stockNuevo = $stockAnterior + $cantidad;  
                            $record->update(['stock_actual' => $stockNuevo]);   
                    }
                }

                //grabo en tabla auditoria
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Detalle/Facturas',
                    'estado'          => '0',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $this->comentario,
                    'comercio_id'     => $this->comercioId
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
                $factura->update(['estado' => 'anulado']);

                $factura = Factura::find($id)->delete();

                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Facturas',
                    'estado'          => '0',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $comentario,
                    'comercio_id'     => $this->comercioId
                ]);
                
                $mesa = Mesa::find($this->mesaId);
                $mesa->update(['estado' => 'Disponible']);

                //repone stock de los productos cargados en la factura
                $detalle = Detfactura::where('factura_id', $id)
                    ->select('cantidad', 'producto_id')->get();
                if ($detalle->count() > 0) {
                    foreach($detalle as $d){
                        $idProducto = Producto::find($d->producto_id);
                        
                        if($idProducto->controlar_stock == 'si'){
                            $record = Stock::where('producto_id', $d->producto_id)->first();    
                            $stockAnterior = $record['stock_actual'];
                            $stockNuevo = $stockAnterior + $d->cantidad;  
                            $record->update(['stock_actual' => $stockNuevo]);   
                        }
                    }
                }

                session()->flash('msg-ok', 'Factura anulada con éxito!!');
                DB::commit();               
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! La factura no se anuló...');
            }
            return redirect()->to('/reservas-estado-mesas');
        }
    }
    public function eliminarEntrega($id, $comentario)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $record = DetMetodoPago::find($id)->delete();              

                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Entregas/Facturas',
                    'estado'          => '0',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $comentario,
                    'comercio_id'     => $this->comercioId
                ]);

                DB::commit();
                $this->emit('entregaAnulada');               
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! La entrega no se anuló...');
            }
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
    public function enviarDatosPago($tipo, $nro, $importe, $saldo)
    {
        if($importe > $saldo) $this->emit('importeMayorQueSaldo');
        else{
            $this->f_de_pago = $tipo;
            $this->nro_comp_pago = $nro;
        }        
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