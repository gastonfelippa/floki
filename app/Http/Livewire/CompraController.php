<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Compra;
use App\Models\Detcompra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Stock;
use App\Models\Subproducto;
use Carbon\Carbon;
use DB;

class CompraController extends Component
{
    public $cantidad = 1, $precio, $estado='abierta';
    public $nombreProveedor, $proveedor="Elegir", $producto="Elegir", $subproducto="Elegir", $barcode;
    public $proveedores, $productos, $subproductos;
    public $selected_id = null, $search, $numFactura, $action = 1, $mostrar_datos;
    public $compras, $total, $importe, $totalAgrabar;  
    public $inicio_factura = true, $habilitar_botones =null,$modificar, $codigo;
    public $comercioId, $factura_id;
    public $numeroFactura, $letra, $sucursal, $numFact, $fecha;
    public $f_de_pago = null, $nro_comp_pago = null, $comentarioPago = '', $mercadopago = null;
    public $mostrar_sp = 0, $tiene_sp, $es_producto = 1;
	
	public function render()
	{        
        $this->comercioId = session('idComercio');
                
        $this->productos = Producto::select()->where('comercio_id', $this->comercioId)->orderBy('descripcion', 'asc')->get();
        if ($this->subproducto == 'Elegir'){
            $this->subproductos = Subproducto::where('producto_id', $this->producto)
                ->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        }else{
            $this->subproductos = Subproducto::where('id', $this->subproducto)
                ->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        }
        
        $this->proveedores = Proveedor::select()->where('comercio_id', $this->comercioId)->orderBy('nombre_empresa', 'asc')->get();
      
        $dProveedor = Proveedor::find($this->proveedor);
        if($dProveedor != null) {
            $this->nombreProveedor = $dProveedor->nombre_empresa;
            $this->dirProveedor = $dProveedor->calle . ' ' . $dProveedor->numero;
        }
        $encabezado = Compra::join('proveedores as prov','prov.id','compras.proveedor_id')
            ->join('localidades as loc','loc.id','prov.localidad_id')
            ->where('compras.estado','like','abierta')
            ->where('compras.comercio_id', $this->comercioId)
            ->select('compras.*','prov.nombre_empresa as nombre_empresa',
                    'prov.calle', 'prov.numero', 'loc.descripcion')->get();
                    
        if($encabezado->count()){
            //$this->action = 1;
            $this->inicio_factura = false;
            $this->factura_id = $encabezado[0]->id;                             
            $this->letra = $encabezado[0]->letra;
            $this->sucursal = $encabezado[0]->sucursal;
            $this->numFact = $encabezado[0]->num_fact;
            $this->fecha = Carbon::parse($encabezado[0]->fecha_fact)->format('d-m-Y');
            $this->proveedor = $encabezado[0]->proveedor_id;
            $this->nombreProveedor = $encabezado[0]->nombre_empresa;
        }

        $info = Detcompra::select('*')->where('comercio_id', $this->comercioId)->get();
        if($info->count()){ 
            $info = Detcompra::join('compras as r','r.id','det_compras.factura_id')
                ->select('det_compras.*', DB::RAW("'' as p_id"), 
                    DB::RAW("'' as codigo"), DB::RAW("'' as producto"), DB::RAW("'' as es_producto"))
                ->where('det_compras.factura_id', $this->factura_id)
                ->where('det_compras.comercio_id', $this->comercioId)
                ->orderBy('det_compras.id')->get();  
            
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

		return view('livewire.compras.component', [
            'info' => $info,
            'encabezado' => $encabezado
		]);
    }
    
    protected $listeners = [
        'buscarProducto'           => 'buscarProducto',
        'buscarPorCodigo'          => 'buscarPorCodigo',
        'buscarDomicilio'          => 'buscarDomicilio',
        'CrearModificarEncabezado' => 'CrearModificarEncabezado',
        'elegirFormaDePago'        => 'elegirFormaDePago',
        'factura_contado'          => 'factura_contado',
        'anularFactura'            => 'anularFactura',
        'deleteRow'                => 'destroy'         
    ];
    public function buscarPorCodigo() //codigo de remitos
    {
        if($this->barcode != null){
            $this->mostrar_sp = 0;
            $this->articulos = null;
            $articulos = Producto::where('codigo', $this->barcode)
                            ->where('comercio_id', $this->comercioId)->get();
            if ($articulos->count()) $this->producto = $articulos[0]->id;
            else session()->flash('msg-error', 'El Código no existe...');
        }
    }

    public function doAction($action)
    {
        $this->action = $action;
        if($this->action == 1) $this->resetInput();
    }

    public function resetInput()
    {
        $this->action      = 1;
        $this->cantidad    = 1;
        $this->barcode     = '';
        $this->precio      = '';
        $this->producto    = 'Elegir';
        $this->subproducto = 'Elegir';
        $this->selected_id = null;
        $this->search      = '';
        $this->letra       = '';
        $this->sucursal    = '';
        $this->numFact     = '';
        $this->fecha       = '';
        $this->mostrar_sp  = 0;
        $this->es_producto = 1;
    }

    public function resetInputTodos()
    {
        $this->action = 3;
        $this->cantidad = 1;
        $this->barcode ='';
        $this->precio = '';        
        $this->proveedor = 'Elegir';
        $this->dirProveedor = null;
        $this->producto = 'Elegir';
        $this->subproducto = 'Elegir';
        $this->selected_id = null;
        $this->search ='';
        $this->habilitar_botones = false;
        $this->letra       = '';
        $this->sucursal    = '';
        $this->numFact      = '';
        $this->fecha      = '';
        $this->mostrar_sp  = 0;
        $this->es_producto = 1;
    }

    public function CrearModificarEncabezado($data)
    {       
        $info = json_decode($data);
        $this->numeroFactura = $info->sucursal . '-' . $info->numero;
        $dataPro = Proveedor::find($info->proveedor_id);
        $this->nombreProveedor = $dataPro->nombre_empresa;
        
        $this->letra     = $info->letra;
        $this->sucursal  = $info->sucursal;
        $this->numFact   = $info->numero;
        $this->fecha     = Carbon::now();
        if($info->fecha != '') $this->fecha = $info->fecha;
        
        $this->proveedor = $info->proveedor_id;
        
        if(!$this->inicio_factura){
            $record = Compra::find($this->factura_id);
            $record->update([
                'letra'        => $this->letra,
                'sucursal'     => $this->sucursal,
                'num_fact'     => $this->numFact,
                'fecha_fact'   => Carbon::parse($this->fecha)->format('Y,m,d h:i:s'),
                'proveedor_id' => $this->proveedor
            ]);
            session()->flash('message', 'Encabezado Modificado...');
        }
    }
    public function edit($id, $es_producto)
    {
        $this->selected_id = $id;
        $this->es_producto = $es_producto;
        $record = Detcompra::find($id);
        if($this->es_producto == 1) $this->producto = $record->producto_id;
        else $this->subproducto = $record->subproducto_id;
        $this->precio   = $record->precio;
        $this->cantidad = $record->cantidad;
    }
 
    public function StoreOrUpdateButton($articuloId)
    {
        if($articuloId == 0 && $this->es_producto == 1){
            $this->validate([
                'producto' => 'not_in:Elegir|required',
                'cantidad' => 'required|numeric|min:0|not_in:0',
                'precio'   => 'required']);
        }elseif($articuloId == 0 && $this->es_producto == 0){
            $this->validate([
                'subproducto' => 'not_in:Elegir|required',
                'cantidad'    => 'required|numeric|min:0|not_in:0',
                'precio'      => 'required']);            
        }                                  
        if($this->es_producto == 1){       //si es un producto, verifico si tiene subproductos
            $record = Subproducto::where('producto_id', $this->producto)->get();
            if($record->count()){          //si tiene, los muestro en el select
                $this->es_producto = 0;
            }else $this->StoreOrUpdate($this->producto);
        }else $this->StoreOrUpdate($this->subproducto);
    }

    public function StoreOrUpdate($id)
    {       
        $this->totalAgrabar = $this->total + ($this->cantidad * $this->precio);
 
        DB::begintransaction();                         //iniciar transacción para grabar
        try{  
            if($this->selected_id > 0) {                //modifica
                $record = Detcompra::find($this->selected_id);  //actualizamos cantidad y precio
                $cantidad_detalle = $record->cantidad;         
                $record->update([
                    'cantidad' => $this->cantidad,
                    'precio'   => $this->precio
                ]);
                //modifico stock
                if($this->es_producto == 1) $record = Stock::where('producto_id', $id)->first();  
                else $record = Stock::where('subproducto_id', $id)->first(); 
                $stockActual = $record['stock_actual'] - $cantidad_detalle;
                $stockNuevo = $stockActual + $this->cantidad;  
                $record->update(['stock_actual' => $stockNuevo]); 
            }else {
                if($this->inicio_factura) {
                    if($this->proveedor == 'Elegir') $this->proveedor = null;
                
                    $factura = Compra::create([
                        'letra'        => $this->letra,
                        'sucursal'     => $this->sucursal,
                        'num_fact'     => $this->numFact,
                        'proveedor_id' => $this->proveedor,
                        'importe'      => $this->totalAgrabar,
                        'estado'       => 'abierta',
                        'user_id'      => auth()->user()->id,
                        'comercio_id'  => $this->comercioId,
                        'fecha_fact'   => Carbon::parse($this->fecha)->format('Y,m,d h:i:s')
                    ]);
                    $this->inicio_factura = false;
                    $this->factura_id = $factura->id;
                }
                $record = Compra::find($this->factura_id);  //actualizamos el encabezado
                $record->update(['importe' => $this->totalAgrabar]);
                // crea detalle
                if($this->es_producto == 1){         //si es un producto
                    $existe = Detcompra::select('id')       //buscamos si ya está cargado
                        ->where('factura_id', $this->factura_id)
                        ->where('comercio_id', $this->comercioId)
                        ->where('producto_id', $id)->get();  
                }else{                                 //sino
                    $existe = Detcompra::select('id') //buscamos si el subproducto ya está cargado
                        ->where('factura_id', $this->factura_id)
                        ->where('comercio_id', $this->comercioId)
                        ->where('subproducto_id', $id)->get();
                }
                if ($existe->count()){                    //actualizamos solo la cantidad
                    $edit_cantidad = DetCompra::find($existe[0]->id); 
                    $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
                    $edit_cantidad->update(['cantidad' => $nueva_cantidad]);
                }else{
                    if($this->es_producto == 1){
                        $add_item = DetCompra::create([         //creamos un nuevo detalle
                            'factura_id'  => $this->factura_id,
                            'producto_id' => $id,
                            'cantidad'    => $this->cantidad,
                            'precio'      => $this->precio,
                            'comercio_id' => $this->comercioId
                        ]);    
                    }else{ 
                        $add_item = DetCompra::create([         //creamos un nuevo detalle
                            'factura_id'  => $this->factura_id,
                            'subproducto_id' => $id,
                            'cantidad'    => $this->cantidad,
                            'precio'      => $this->precio,
                            'comercio_id' => $this->comercioId
                        ]);      
                    }    
                }
                //actualizamos stock
                if($this->es_producto == 1) $record = Stock::where('producto_id', $id)->first(); 
                else $record = Stock::where('subproducto_id', $id)->first();  
                $stockAnterior = $record['stock_actual'];
                $stockNuevo = $stockAnterior + $this->cantidad;  
                $record->update(['stock_actual' => $stockNuevo]); 
            }
            DB::commit();
            if($this->selected_id > 0){		
                session()->flash('message', 'Registro Actualizado');       
            }else{ 
                session()->flash('message', 'Registro Creado'); 
            }           
        }catch (Exception $e){
                DB::rollback(); 
                session()->flash('message', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }     
        $this->resetInput(); 
    }

    public function anularFactura($id, $comentario)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $factura = Compra::find($id);
                $factura->update([ 'estado' => 'anulado']);

                $factura = Compra::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Compras',
                    'estado'          => '0',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $comentario,
                    'comercio_id'     => $this->comercioId
                ]);
                //actualizo stock
                $record = Detcompra::where('factura_id', $id)->get();
                foreach ($record as $i){
                    if($i->producto_id != null) $record = Stock::where('producto_id', $i->producto_id)->first(); 
                    else $record = Stock::where('subproducto_id', $i->subproducto_id)->first();  
                    $stockAnterior = $record['stock_actual'];
                    $stockNuevo = $stockAnterior - $i->cantidad;  
                    $record->update(['stock_actual' => $stockNuevo]);   
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

    public function elegirFormaDePago()
    {
        if($this->proveedor != ''){
            $cli = Proveedor::where('id', $this->proveedor)->get();
            $this->nomCli = $cli[0]->nombre_empresa;
        }
        $this->f_de_pago = '1';        
        $this->doAction(2);
    }
    
    public function pagar_factura()
    {
        if($this->total != 0){
            $record = Compra::find($this->factura_id);
            $record->update([
                'estado' => 'PAGADA',
                'importe' => $this->total
            ]);              
            session()->flash('message', 'Compra Pagada'); 
            $this->resetInputTodos();
        }else{
            session()->flash('msg-error', 'Compra vacía...'); 
        }
    }
    public function factura_contado()
    {
        DB::begintransaction();                         //iniciar transacción para grabar
        try{
            $record = Compra::find($this->factura_id);
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
        
    public function cuenta_corriente()
    {
        if($this->total == 0){
            session()->flash('msg-error', 'Compra vacía...'); 
        }else{
            $record = Compra::find($this->factura_id);
            $record->update([
                'estado' => 'CUENTACORRIENTE',
                'importe' => $this->total
            ]);              
            session()->flash('message', 'Compra enviada a Cuenta Corriente'); 
            $this->resetInputTodos();
        }
    }

    public function destroy($id) //eliminar / delete / remove
    {
        if($id) {
            $record = Detcompra::where('id', $id);
            $record->delete();
            $this->resetInput();
            $this->emit('msg-ok','Registro eliminado con éxito');
        }
    }
}
