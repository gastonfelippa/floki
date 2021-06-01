<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Cajarepartidor;
use App\Models\CajaUsuario;
use App\Models\Cliente;
use App\Models\Ctacte;
use App\Models\Detfactura;
use App\Models\Empleado;
use App\Models\Factura;
use App\Models\Gasto;
use App\Models\Producto;
use App\Models\User;
use Carbon\Carbon;
use DB;

class CajarepartidorController2 extends Component
{
    //properties   
    public $clientes, $empleados, $productos, $repartidor = '0', $comentario = '', $caja_abierta = 0;
    public $selected_id = null, $search, $id_factura, $fact_id, $infoDetalle, $comercioId;
    public $producto="Elegir", $action='1', $totalfactura, $nombreCliente, $nomRep;
    public $cantidadEdit, $productoEdit, $precioEdit, $editFacturaId, $importeFactura, $estado_entrega;
    public $importe, $gasto, $gastos, $totalCI, $totalCobrado, $totalGastos, $totalCF, $cajaGasto, $cantFacturas;
	
	public function render()
	{
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');

        //vemos si tiene una caja habilitada con su id
        $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', auth()->user()->id)
            ->where('caja_usuarios.estado', '1')->get();           
        $this->caja_abierta = $caja_abierta->count();

        $this->productos = Producto::select()->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        $this->clientes = Cliente::select()->where('comercio_id', $this->comercioId)->orderBy('apellido')->get();     
        $this->gastos = Gasto::select()->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        $this->empleados = User::join('model_has_roles as mhr', 'mhr.model_id', 'users.id')
            ->join('roles as r', 'r.id', 'mhr.role_id')
            ->where('r.alias', 'Repartidor')
            ->where('r.comercio_id', $this->comercioId)
            ->select('users.*')->orderBy('apellido')->get();
        foreach ($this->empleados as $i){      //busca si el usuario es un repartidor
            if($i->id == auth()->user()->id) $this->repartidor = auth()->user()->id;     
        }

        $dProducto = Producto::find($this->productoEdit);
        if($dProducto != null) $this->precioEdit = $dProducto->precio_venta; 
        else $this->precioEdit = '';

        //calculo la caja inicial de cada repartidor
        $this->totalCI = 0;     
        $infoCaja = Cajarepartidor::select('*', DB::RAW("'' as totalCI"))
            ->where('empleado_id','like', $this->repartidor)
            ->where('tipo','like','Ingreso')
            ->where('estado','like','Pendiente')
            ->orderBy('created_at', 'asc')->get();
        $this->totalCI = 0;
        if($infoCaja->count() > 0){             
            foreach ($infoCaja as $i){
                $this->totalCI += $i->importe;
            }
        }
        //calculo los gastos de cada repartidor
        $infoGastos = Cajarepartidor::leftjoin('gastos as g','g.id','cajarepartidor.gasto_id')
            ->select('cajarepartidor.*', 'g.descripcion', DB::RAW("'' as totalGastos"))
            ->where('empleado_id','like', $this->repartidor)
            ->where('tipo','like','Gasto')
            ->where('estado','like','Pendiente')
            ->orderBy('created_at', 'asc')->get();
        $this->totalGastos = 0;
        if($infoGastos->count() > 0){             
            foreach ($infoGastos as $i){
                $this->totalGastos += $i->importe;
            }
        }
        //busco el nombre del repartidor para mostrarlo en los mensajes
        $nomRep = User::find($this->repartidor);
            if($nomRep != null) {$this->nomRep = $nomRep->apellido . ' ' . $nomRep->name;}
                else {$this->repartidor = '0';}
        
        $info = Factura::leftjoin('clientes as c','c.id','facturas.cliente_id')
            ->leftjoin('users as u','u.id','facturas.repartidor_id')
            ->select('facturas.*', 'c.nombre as nomCli', 'c.apellido as apeCli', 'u.name as nomRep',
                     'u.apellido as apeRep', DB::RAW("'' as total"))
            ->where('facturas.comercio_id', $this->comercioId)
            ->where('facturas.estado','like','PENDIENTE')
            ->where('facturas.repartidor_id', 'like', $this->repartidor)
            ->orderBy('facturas.id', 'asc')->get();         
        $this->totalCobrado = 0; 
        $this->cantFacturas = $info->count(); 
        if($info->count() > 0){             
            foreach ($info as $i){
                $this->totalCobrado += $i->importe;      
            }
            $this->fact_id = $info[0]->id;
        }
        $this->totalCF = $this->totalCI + $this->totalCobrado - $this->totalGastos;

		return view('livewire.cajarepartidor.component', [
            'info' => $info,
            'infoCaja' => $infoCaja,
            'infoGastos' => $infoGastos
        ]);
    }
    
    protected $listeners = [
        'factura_contado'        => 'factura_contado',
        'factura_ctacte'         => 'factura_ctacte',
        'cobrarTodas'            => 'cobrarTodas',
        'deleteRow'              => 'destroy',
        'destroyGastoIngreso'    => 'destroyGastoIngreso',
        'deleteRowDel'           => 'destroyDel',
        'grabarCajaModal'        => 'grabarCajaModal',
        'grabarComentarioModal'  => 'grabarComentarioModal',
        'doAction2'              => 'doAction2', 
        'marcarEstadoPedido'     => 'marcarEstadoPedido' 
    ];
    public function marcarEstadoPedido($id, $estado)
    {
        $record = Factura::find($id);
        $record->update([
            'estado_entrega' => $estado
        ]);              
        $this->resetInput();
    }

    public function editDel($id)
    {
        $record = Detfactura::find($id);
        $this->selected_id = $id;
        $this->cantidadEdit = $record->cantidad;
        $this->productoEdit = $record->producto_id;
        $this->precioEdit = $record->precio;
        $this->verDetalle($id);
    }
    public function verDet($id, $nomCli, $apeCli)
    {
        $this->nombreCliente = $apeCli . ' ' . $nomCli;
        $this->verDetalle($id);
    }

    public function verDetalle($id)
    {
        $this->action = 2;
        $this->editFacturaId = $id;
        $this->infoDetalle = Detfactura::join('facturas as f','f.id','detfacturas.factura_id')
            ->join('productos as p','p.id','detfacturas.producto_id')
            ->select('detfacturas.*', 'p.descripcion as producto', DB::RAW("'' as importe"))
            ->where('detfacturas.factura_id', $id)
            ->orderBy('detfacturas.id', 'asc')->get();
      
        $this->importeFactura = 0;
        foreach ($this->infoDetalle as $i)
        {
            $i->importe=$i->cantidad * $i->precio;
            $this->importeFactura += $i->importe;
        }
        $record = Factura::find($this->editFacturaId);
        $record->update([
            'importe' => $this->importeFactura
        ]);       
    }

    public function doAction($action)
	{
        $this->action = $action;
    }

    public function resetInput()
    {
        $this->cantidad = '';
        $this->producto='Elegir';
        $this->precio ='';
        $this->totalDetalle='';
        $this->cantidadEdit = '';
        $this->productoEdit='Elegir';
        $this->precioEdit ='';
        $this->comentario ='';
        $this->selected_id = 0;
       
    }

    public function StoreOrUpdate()
    {
        $this->validate([
            'productoEdit' => 'not_in:Elegir'
        ]);            
        $this->validate([
            'cantidadEdit' => 'required',
            'productoEdit' => 'required',
            'precioEdit' => 'required'
        ]);
        //valida si se quiere modificar o grabar
        DB::begintransaction();         //iniciar transacción para grabar
        try{
            if($this->selected_id > 0) {
                $record = Detfactura::find($this->selected_id);
                $record->update([
                    'cantidad' => $this->cantidadEdit,
                    'producto_id' => $this->productoEdit,
                    'precio' => $this->precioEdit
                ]);
            }else {           
                $existe = Detfactura::select('id')          //buscamos si el producto ya está cargado
                    ->where('factura_id', $this->editFacturaId)
                    ->where('comercio_id', $this->comercioId)
                    ->where('producto_id', $this->productoEdit)->get();
                if($existe->count() > 0){
                    $edit_cantidad = Detfactura::find($existe[0]->id); 
                    $nueva_cantidad = $edit_cantidad->cantidad + $this->cantidadEdit; 
                    $edit_cantidad->update([                //actualizamos solo la cantidad                                      
                        'cantidad' => $nueva_cantidad
                    ]);
                }else{
                    $add_item = Detfactura::create([         //creamos un nuevo detalle
                        'factura_id' => $this->editFacturaId,
                        'cantidad' => $this->cantidadEdit,
                        'producto_id' => $this->productoEdit,
                        'precio' => $this->precioEdit,
                        'comercio_id' => $this->comercioId
                    ]);	
                }
            }  
            if($this->selected_id > 0) session()->flash('message', 'Registro Actualizado');       
            else session()->flash('message', 'Registro Creado');             
            DB::commit();         
        }catch (\Exception $e){
            DB::rollback();            
            $status = $e->getMessage();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->verDetalle($this->editFacturaId);
        $this->resetInput();
    }

    public function factura_contado($id)
    {
        $record = Factura::find($id);
        $record->update([
            'estado' => 'contado',
            'estado_pago' => '1'
        ]);              
        $this->resetInput();
    }

    public function factura_ctacte($id, $cliId)
    {
        DB::begintransaction();                         //iniciar transacción para grabar
        try{ 
            $record = Factura::find($id);
            $record->update([
                'cliente_id' => $cliId,
                'estado' => 'ctacte',
                'estado_pago' => '0'
            ]);
            Ctacte::create([
                'cliente_id' => $cliId,
                'factura_id' => $id
            ]);
            session()->flash('msg-ok', 'La Factura fué enviada a Cuenta Corriente...');
            DB::commit();               
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        return;
    }

    public function cobrarTodas($repId)
    {
        $record = Factura::select('estado', 'estado_pago')->where('repartidor_id',$repId)->where('estado', 'PENDIENTE');
        $record->update([
            'estado' => 'contado',
            'estado_pago' => '1'
        ]); 
        $record = Cajarepartidor::select('estado')->where('empleado_id',$repId)->where('estado', 'Pendiente');
        $record->update([
            'estado' => 'Terminado'
        ]); 
    }

    public function destroyGastoIngreso($id) //elimina gasto o ingreso
    { 
        if($id) {
            $record = Cajarepartidor::where('id', $id);
            $record->delete();
            $this->emit('msg-ok','Registro eliminado con éxito');
            $this->resetInput();            
        }
    }

    public function destroy($id) //anula la factura seleccionada
    { 
        dd($id);     
        if($id) {
            $record = Factura::find($id);
            $record->update([
                'estado' => 'ANULADA',
                'user_id_delete' => auth()->user()->id,
                'comentario' => $this->comentario
            ]);
            $this->resetInput();
            $this->emit('msg-ok','Registro anulado con éxito');
        }
    }

    public function destroyDel($id) //eliminar item
    {
        if($id) {
            $record = Detfactura::where('id', $id);
            $record->delete();

            $this->verDetalle($this->editFacturaId);
            $this->resetInput();
            $this->emit('msg-ok','Registro eliminado con éxito');
        }
    }
    	// es diferente porque se usan ventanas modales
	public function grabarCajaModal($info)
	{
		$data = json_decode($info);
		$this->selected_id = $data->id;
        $this->importe = $data->importe;
        $this->gasto = $data->gasto;
		$this->cajaGasto = $data->cajaGasto;

		$this->StoreOrUpdateCaja();
    }
	public function grabarComentarioModal($info)
	{
		$data = json_decode($info);
        DB::begintransaction();       
        try{
            $record = Factura::find($data->factura_id);
            if($data->accion == 0){         //graba o modifica
                $record->update(['comentario' => $data->comentario]);
                session()->flash('message', 'Comentario Grabado...'); 
            }else{                          //elimina
                $record->update(['comentario' => '',]);
                session()->flash('message', 'Comentario Eliminado...'); 
            }            
            DB::commit();             
        }catch (\Exception $e){
            DB::rollback(); 
            if($data->accion == 0) session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El Comentario no se grabó...');
            else session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El Comentario no se eliminó...'); 
        }
    }
    
    public function StoreOrUpdateCaja()
    {
        if($this->cajaGasto == 1) {
           $this->gasto = null; 
        } 

        //valida si se quiere modificar o grabar   
        if($this->selected_id > 0) {
            $record = Cajarepartidor::find($this->selected_id);
            $record->update([
                'importe' => $this->importe,               
                'gasto_id' => $this->gasto                        
            ]);
        }else {          
            DB::begintransaction();         //iniciar transacción para grabar
            try{
                if($this->cajaGasto == 1){    
                    Cajarepartidor::create([
                        'importe'     => $this->importe,
                        'tipo'        => 'Ingreso',
                        'estado'      => 'Pendiente',
                        'empleado_id' => $this->repartidor
                    ]); 
                }else{
                    Cajarepartidor::create([
                        'importe'     => $this->importe,
                        'tipo'        => 'Gasto',
                        'estado'      => 'Pendiente',
                        'empleado_id' => $this->repartidor,
                        'gasto_id'    => $this->gasto
                    ]); 
                }    
                if($this->selected_id > 0)		
                    session()->flash('message', 'Registro Actualizado');       
                else 
                    session()->flash('message', 'Registro Creado');             
                DB::commit();          
            }catch (\Exception $e){
                DB::rollback();           
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
            }
        }
        $this->resetInput();
    }
}
