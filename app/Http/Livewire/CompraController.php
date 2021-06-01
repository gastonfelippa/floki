<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Detcompra;
use App\Models\Producto;
use App\Models\Compra;
use App\Models\Proveedor;
use Carbon\Carbon;
use DB;

class CompraController extends Component
{
	//properties
    public $cantidad = 1, $precio, $estado='ABIERTO';
    public $proveedor="Elegir", $producto="Elegir", $barcode;
    public $proveedores, $productos;
    public $selected_id = null, $search, $numFactura;
    public $compras, $total, $importe, $totalAgrabar;  
    public $grabar_encabezado, $habilitar_grabar_encabezado =null, $habilitar_botones =null,$modificar, $codigo;
    public $comercioId, $compra_id, $letra, $sucursal, $numFact;
	
	public function render()
	{        
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
                
        $this->productos = Producto::select()->where('comercio_id', $this->comercioId)->orderBy('descripcion', 'asc')->get();
        $this->proveedores = Proveedor::select()->where('comercio_id', $this->comercioId)->orderBy('nombre_empresa', 'asc')->get();
        
        if(strlen($this->barcode) > 0){ //strlen valida si está vacío o no
            $this->buscarProducto($this->barcode); 
        }
        else{
            $this->precio = '';
        }
        
        $dProveedor = Proveedor::find($this->proveedor);
        if($dProveedor != null) {$this->dirProveedor = $dProveedor->calle . ' ' . $dProveedor->numero;}
        
        $dProducto = Producto::find($this->producto);
        if($dProducto != null) {$this->precio = $dProducto->precio_venta;}else {$this->precio = '';}     
        
        $encabezado = Compra::select('*')->where('comercio_id', $this->comercioId)->get();                        // ->where('estado','like','ABIERTA')->get();
        
        if($encabezado->count() == 0){
            $this->grabar_encabezado = true;
        }else{
            $encabezado = Compra::join('proveedores as prov','prov.id','compras.proveedor_id')
            ->join('localidades as loc','loc.id','prov.localidad_id')
            ->where('compras.estado','like','ABIERTA')
            ->where('compras.comercio_id', $this->comercioId)
            ->select('compras.*','prov.nombre_empresa as nombre_empresa',
                     'prov.calle', 'prov.numero', 'loc.descripcion')->get();
                        
            if($encabezado->count() > 0){
                $this->letra = $encabezado[0]->letra;
                $this->sucursal = $encabezado[0]->sucursal;
                $this->numFact = $encabezado[0]->num_fact;
                //toma el id de la factura abierta
                $this->compra_id = $encabezado[0]->id;
                
                if($this->habilitar_grabar_encabezado) $this->grabar_encabezado = true;
                else $this->grabar_encabezado = false;                             
            }else{
                $this->grabar_encabezado = true;
            }
        }

        if($encabezado->count() > 0){
            if($encabezado[0]->proveedor_id != null){
                $this->dejar_pendiente = true;
            }else{
                $this->dejar_pendiente = false;
            }
        }

        $info = Detcompra::select('*')->where('comercio_id', $this->comercioId)->get();

        if($info->count() > 0){
            $info = Detcompra::leftjoin('compras as f','f.id','det_compras.compra_id')
                    ->leftjoin('productos as p','p.id','det_compras.producto_id')
                    ->select('det_compras.*', 'p.descripcion as producto', DB::RAW("'' as importe"))
                    ->where('det_compras.compra_id', $this->compra_id)
                    ->where('det_compras.comercio_id', $this->comercioId)
                    ->where('f.estado', 'like', 'ABIERTA')
                    ->orderBy('det_compras.id', 'asc')->get();  
        }    
        $this->total = 0;

        foreach ($info as $i)
        {
            $i->importe=$i->cantidad * $i->precio;
            $this->total += $i->importe;
        }

		return view('livewire.compras.component', [
            'info' => $info,
            'encabezado' => $encabezado
		]);
    }
    
    protected $listeners = [
        'buscarProducto' => 'buscarProducto',
        'buscarDomicilio' => 'buscarDomicilio',
        'deleteRow' => 'destroy'         
    ];
    
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

    public function resetInput()
    {
        $this->cantidad = 1;
        $this->barcode ='';
        $this->precio = '';
        $this->producto = 'Elegir';
        $this->selected_id = null;
        $this->action = 1;
        $this->search ='';
    }

    public function resetInputTodos()
    {
        $this->cantidad = 1;
        $this->barcode ='';
        $this->precio = '';        
        $this->proveedor = 'Elegir';
        $this->dirProveedor = null;
        $this->producto = 'Elegir';
        $this->selected_id = null;
        $this->action =1;
        $this->search ='';
        $this->habilitar_grabar_encabezado = true;
        $this->habilitar_botones = false;
    }

    public function edit($id)
    {
        $record = Detcompra::find($id);
        $this->selected_id = $id;
        $this->producto = $record->producto_id;
        $this->precio = $record->precio;
        $this->cantidad = $record->cantidad;
        $this->action = 2;
    }

    public function StoreOrUpdate()
    {
        $this->validate([
            'producto' => 'not_in:Elegir'
        ]);            
        $this->validate([
            'cantidad' => 'required',
            'producto' => 'required',
            'precio' => 'required'
        ]);
        $this->totalAgrabar = $this->total + ($this->cantidad * $this->precio);
 
        DB::begintransaction();                         //iniciar transacción para grabar
        try{  
            if($this->selected_id > 0) {                //valida si se quiere modificar o crear
                $record = Detcompra::find($this->selected_id);  
                $record->update([                       //actualizamos el registro
                    'producto_id' => $this->producto,
                    'cantidad' => $this->cantidad,
                    'precio' => $this->precio
                ]); 
            }else {
                if($this->proveedor == 'Elegir'){
                    $this->proveedor = null;
                } 
                if($this->grabar_encabezado == true)
                {
                    $factura = Compra::create([
                        'letra' => $this->letra,
                        'sucursal' => $this->sucursal,
                        'num_fact' => $this->numFact,
                        'proveedor_id' => $this->proveedor,
                        'importe' => $this->totalAgrabar,
                        'estado' => 'ABIERTA',
                        'user_id' => auth()->user()->id,
                        'comercio_id' => $this->comercioId
                    ]);
                }
                
                $idFactura = Compra::where('estado', 'like', 'ABIERTA')
                    ->where('comercio_id', $this->comercioId)->select('id')->get();
                $this->compra_id = $idFactura[0]->id;

                $existe = Detcompra::select('id')          //buscamos si el producto ya está cargado
                                    ->where('compra_id', 'like', $idFactura[0]->id)
                                    ->where('comercio_id', $this->comercioId)
                                    ->where('producto_id', 'like', $this->producto)->get();
                if ($existe->count() > 0){
                    $edit_cantidad = Detcompra::find($existe[0]->id); 
                    $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidad; 
                    $edit_cantidad->update([                //actualizamos solo la cantidad                                      
                        'cantidad' => $nueva_cantidad
                    ]);
                }else{
                    $add_item = Detcompra::create([         //creamos un nuevo detalle
                        'compra_id' => $this->compra_id,
                        'producto_id' => $this->producto,
                        'cantidad' => $this->cantidad,
                        'precio' => $this->precio,
                        'comercio_id' => $this->comercioId
                    ]);	
                }

                $record = Compra::find($idFactura[0]->id);  //actualizamos el encabezado
                $record->update([
                    'importe' => $this->totalAgrabar
                ]); 
            }
                //confirmar la transaccion
            DB::commit();
            if($this->selected_id > 0){		
                session()->flash('message', 'Registro Actualizado');       
            }else{ 
                session()->flash('message', 'Registro Creado'); 
            }           
        }catch (Exception $e){
                DB::rollback();    //en caso de error, deshacemos para no generar inconsistencia de datos
                session()->flash('message', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }     
        $this->resetInput(); 
        $this->habilitar_grabar_encabezado = false;
    }
    
    public function pagar_factura()
    {
        if($this->total != 0){
            $record = Compra::find($this->compra_id);
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
        
    public function cuenta_corriente()
    {
        if($this->total == 0){
            session()->flash('msg-error', 'Compra vacía...'); 
        }else{
            $record = Compra::find($this->compra_id);
            $record->update([
                'estado' => 'CUENTACORRIENTE',
                'importe' => $this->total
            ]);              
            session()->flash('message', 'Compra enviada a Cuenta Corriente'); 
            $this->habilitar_grabar_encabezado = true;
            $this->resetInputTodos();
        }
    }
    
    public function cancelarModEncabezado()
    { 
        $this->habilitar_grabar_encabezado = false;
        $this->habilitar_botones = false;
    }
    public function modificarEncabezado()
    { 
        $this->habilitar_grabar_encabezado = true;
        $this->habilitar_botones = true;
    }
    public function grabarModEncabezado($id)
    { 
        $this->validate([
            'proveedor' => 'not_in:Elegir'
        ]);
        $record = Compra::find($id);
        $record->update([
            'letra' => $this->letra,
            'sucursal' => $this->sucursal,
            'num_fact' => $this->numFact,
            'proveedor_id' => $this->proveedor
        ]);
        $this->habilitar_grabar_encabezado = false;
        $this->habilitar_botones = false;
        session()->flash('message', 'Encabezado Modificado...');
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
