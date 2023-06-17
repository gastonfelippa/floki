<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Caja;
use App\Models\Cajarepartidor;
use App\Models\CajaUsuario;
use App\Models\Cliente;
use App\Models\Ctacte;
use App\Models\Detfactura;
use App\Models\Empleado;
use App\Models\Factura;
use App\Models\Gasto;
use App\Models\ModelHasRole;
use App\Models\MovimientoDeCaja;
use App\Models\Producto;
use App\Models\User;
use Carbon\Carbon;
use DB;

class CajarepartidorController extends Component
{
    //properties
    public $clientes, $empleados, $productos, $repartidor = '0', $caja_abierta;
    public $usuarioAdmin = false, $usuario_habilitado = true;
    public $selected_id = null, $search, $id_factura, $fact_id, $infoDetalle, $comercioId, $estado = 1;
    public $producto="Elegir", $action = 1, $totalfactura, $nombreCliente, $nomRep, $nro_arqueo, $fecha_inicio;
    public $cantidadEdit, $productoEdit, $precioEdit, $editFacturaId = '', $importeFactura, $estado_entrega;
    public $importe, $gasto, $gastos, $totalCI, $totalCobrado, $totalGastos, $totalCF, $cajaGasto, $cantFacturas;
    public $arqueoGralId, $estadoArqueoGral, $user, $cantidad, $precio, $totalDetalle;

	public function render()
	{
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');        
        $this->arqueoGralId = session('idArqueoGral');
        $this->estadoArqueoGral = session('estadoArqueoGral');
        session(['facturaPendiente' => null]);  

        //primero verifico si el usuario logueado es el Administrador del Sistema, en tal caso
        //no hago ninguna validación y le permito hacer cualquier procedimiento
        $usuadrioAdmin = ModelHasRole::join('roles as r', 'r.id', 'model_has_roles.role_id')
            ->join('users as u', 'u.id', 'model_has_roles.model_id')
            ->where('r.alias', 'Administrador')
            ->where('r.comercio_id', $this->comercioId)->select('u.id')->get();
        if($usuadrioAdmin[0]->id <> auth()->user()->id){
            //si no es el Admin, verifico si el usuario logueado es quien inició el Arqueo Gral, en caso de existir...
            //si es ese usuario, habilito para que vea solo las cajas que habilitó
            //sino, debo averiguar si hay una Caja abierta con su Id, 
            //y en ese caso solo le dejo ver la suya, de lo contrario muestro un mensaje y vuelvo al home
            $usuarioArqueo = CajaUsuario::where('user_id', auth()->user()->id)
                    ->where('arqueo_gral_id', $this->arqueoGralId)->get();
            if($usuarioArqueo->count() == 0){   //si no es usuario habilitante de cajas, entonces nos
                                                //referimos a un repartidor. Veremos si tiene una Caja abierta
                $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', auth()->user()->id)
                    ->where('caja_usuarios.estado', '1')->select('caja_usuarios.*')->get();           
                $this->caja_abierta = $caja_abierta->count();
                if($caja_abierta->count() > 0){
                    $this->user = auth()->user()->id;
                    $this->nro_arqueo = $caja_abierta[0]->id;
                    $this->fecha_inicio = $caja_abierta[0]->created_at;
                }else $this->usuario_habilitado = false;
            }
        }else $this->usuarioAdmin = true;
        if($this->nro_arqueo == null){
            $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', $this->repartidor)
                ->where('caja_usuarios.estado', '1')->select('caja_usuarios.*')->get();    
            if($caja_abierta->count() > 0){
                $this->nro_arqueo = $caja_abierta[0]->id;
                $this->fecha_inicio = $caja_abierta[0]->created_at;
            }
        } 
 
        $this->fecha_inicio = Carbon::today();

        $egresos = Gasto::where('comercio_id', $this->comercioId)->get();

        $this->productos = Producto::select()->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        $this->clientes = Cliente::select()->where('comercio_id', $this->comercioId)->orderBy('apellido')->get();
        $this->gastos = Gasto::select()->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        
        if($this->usuarioAdmin){                //si es el Admin, le dejo ver todas las cajas
            $this->empleados = CajaUsuario::join('users as u', 'u.id', 'caja_usuarios.caja_usuario_id')
            ->join('model_has_roles as mhr', 'mhr.model_id', 'u.id')
            ->join('roles as r', 'r.id', 'mhr.role_id')
            ->where('r.alias', 'Repartidor')
            ->where('r.comercio_id', $this->comercioId)
            ->where('caja_usuarios.estado', '1')
            ->select('u.id', 'u.name', 'u.apellido')->orderBy('u.apellido')->get();
        }else{                                   //sino, solo verá las que habilitó
            $this->empleados = CajaUsuario::join('users as u', 'u.id', 'caja_usuarios.caja_usuario_id')
                ->join('model_has_roles as mhr', 'mhr.model_id', 'u.id')
                ->join('roles as r', 'r.id', 'mhr.role_id')
                ->where('r.alias', 'Repartidor')
                ->where('r.comercio_id', $this->comercioId)
                ->where('caja_usuarios.estado', '1')
                ->where('caja_usuarios.user_id', auth()->user()->id)
                ->select('u.id', 'u.name', 'u.apellido')->orderBy('u.apellido')->get();           
            if($this->empleados->count() == 0){  //sino, busca la caja abierta del repartidor logueado
                $this->empleados = User::join('model_has_roles as mhr', 'mhr.model_id', 'users.id')
                    ->join('roles as r', 'r.id', 'mhr.role_id')
                    ->join('caja_usuarios as cu', 'cu.caja_usuario_id', 'users.id')
                    ->where('r.alias', 'Repartidor')
                    ->where('r.comercio_id', $this->comercioId)
                    ->where('cu.estado', '1')
                    ->where('cu.caja_usuario_id', auth()->user()->id)
                    ->select('users.id', 'users.name', 'users.apellido')->orderBy('users.apellido')->get();
            }
        }  
        if($this->empleados->count()){
            foreach ($this->empleados as $i){   
                if($i->id == auth()->user()->id) $this->repartidor = auth()->user()->id;
            }            
        }           
     
        $dProducto = Producto::find($this->productoEdit);
        if($dProducto != null) $this->precioEdit = $dProducto->precio_venta;
        else $this->precioEdit = '';

        if ($this->editFacturaId != ''){
            $this->infoDetalle = Detfactura::join('facturas as f','f.id','detfacturas.factura_id')
                ->join('productos as p','p.id','detfacturas.producto_id')
                ->select('detfacturas.*', 'p.descripcion as producto', DB::RAW("'' as importe"))
                ->where('detfacturas.factura_id', $this->editFacturaId)
                ->orderBy('detfacturas.id', 'asc')->get();
            $this->importeFactura = 0;
            foreach ($this->infoDetalle as $i)
            {
                $i->importe=$i->cantidad * $i->precio;
                $this->importeFactura += $i->importe;
            }
        }
        //calculo la caja inicial de cada repartidor
        $this->totalCI = 0;
        $infoCaja = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
        ->join('caja_inicials as ci', 'ci.caja_user_id', 'caja_usuarios.id')
        ->where('caja_usuarios.caja_usuario_id', $this->repartidor)
        ->where('caja_usuarios.estado', '1')->sum('importe');
        $this->totalCI = $infoCaja;

        //calculo los gastos de cada repartidor
        $this->totalGastos = 0;
        $infoGastos = MovimientoDeCaja::join('gastos as g', 'g.id', 'movimiento_de_cajas.egreso_id')
                ->join('caja_usuarios as cu', 'cu.id', 'movimiento_de_cajas.arqueo_id')
                ->where('cu.estado', '1')
                ->where('cu.caja_usuario_id', $this->repartidor)
                ->where('movimiento_de_cajas.user_id', $this->repartidor)
                ->where('movimiento_de_cajas.egreso_id', '<>', null)->get();
        $this->totalGastos = 0;
        if($infoGastos->count() > 0){
            foreach ($infoGastos as $i){
                $this->totalGastos += $i->importe;
            }
        }
        //busco el nombre del repartidor para mostrarlo en los mensajes
        $nomRep = User::find($this->repartidor);
            if($nomRep != null) $this->nomRep = $nomRep->apellido . ' ' . $nomRep->name;
            else $this->repartidor = '0';
        //busco las facturas de cada repartidor
        $info = Factura::join('clientes as c','c.id','facturas.cliente_id')
                ->join('users as u','u.id','facturas.repartidor_id')
                ->where('facturas.comercio_id', $this->comercioId)
                ->where('facturas.estado', 'pendiente')
                ->where('facturas.repartidor_id', $this->repartidor)
                ->select('facturas.*', 'c.nombre as nomCli', 'c.apellido as apeCli', 'u.name as nomRep',
                        'u.apellido as apeRep', DB::RAW("'' as nombreCaja"))
                ->orderBy('facturas.id', 'asc')->get();
               
        $this->totalCobrado = 0;
        if($info->count()){
            foreach ($info as $i){
                $info2 = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
                    ->where('caja_usuarios.caja_usuario_id', $i->user_id)
                    ->orderBy('caja_usuarios.id', 'desc')->select('c.descripcion')->first();
                $i->nombreCaja = $info2->descripcion;
                $this->totalCobrado += $i->importe;
            }
        }
        $this->totalCF = $this->totalCI + $this->totalCobrado - $this->totalGastos;

		return view('livewire.cajarepartidor.component', [
            'info' => $info,
            'infoCaja' => $infoCaja,
            'infoGastos' => $infoGastos,
            'egresos' => $egresos
        ]);
    }

    protected $listeners = [
        'factura_contado'        => 'factura_contado',
        'factura_ctacte'         => 'factura_ctacte',
        'cobrarTodas'            => 'cobrarTodas',
        'destroyGastoIngreso'    => 'destroyGastoIngreso',
        'eliminarRegistro'       => 'eliminarRegistro',
        'grabarGastosModal'      => 'grabarGastosModal',
        'grabarComentarioModal'  => 'grabarComentarioModal',
        'doAction2'              => 'doAction2',
        'marcarEstadoPedido'     => 'marcarEstadoPedido',
        'anularFactura'          => 'anularFactura'
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
        $this->infoDetalle = Detfactura::join('facturas as f','f.id','detfacturas.factura_id')
            ->join('productos as p','p.id','detfacturas.producto_id')
            ->select('detfacturas.*', 'p.descripcion as producto', DB::RAW("'' as importe"))
            ->where('detfacturas.factura_id', $this->editFacturaId)
            ->orderBy('detfacturas.id', 'asc')->get();
        $this->importeFactura = 0;
        foreach ($this->infoDetalle as $i)
        {
            $i->importe=$i->cantidad * $i->precio;
            $this->importeFactura += $i->importe;
        }
    }
    public function verDet($id)
    {
        session(['idMesa' => 'D']);
        session(['facturaPendiente' => $id]);
        return redirect()->to('/facturasbar');
        // $this->nombreCliente = $apeCli . ' ' . $nomCli;
        // $this->verDetalle($id);
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
        $this->resetInput();
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
            'precioEdit'   => 'required'
        ]);
        //valida si se quiere modificar o grabar
        DB::begintransaction();         //iniciar transacción para grabar
        try{
            if($this->selected_id > 0) {
                $record = Detfactura::find($this->selected_id);
                $record->update([
                    'cantidad'    => $this->cantidadEdit,
                    'producto_id' => $this->productoEdit,
                    'precio'      => $this->precioEdit
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
                        'factura_id'  => $this->editFacturaId,
                        'cantidad'    => $this->cantidadEdit,
                        'producto_id' => $this->productoEdit,
                        'precio'      => $this->precioEdit,
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
        DB::begintransaction();                         //iniciar transacción para grabar
        try{
            $record = Factura::find($id);
            $record->update([
                'estado'      => 'contado',
                'estado_pago' => '1'
            ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
    }
    public function factura_ctacte($id, $cliId)
    {
        DB::begintransaction();                       
        try{
            $record = Factura::find($id);
            $record->update([
                'cliente_id'  => $cliId,
                'estado'      => 'ctacte',
                'estado_pago' => '0'
            ]);
            Ctacte::create([
                'cliente_id' => $cliId,
                'factura_id' => $id
            ]);
            $record = Cliente::find($cliId);    //marca que el cliente tiene un saldo en ctacte
            $record->update([
                'saldo' => '1'
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
        DB::begintransaction();                       
        try{
            $record = Factura::select('estado', 'estado_pago')->where('repartidor_id',$repId)->where('estado', 'pendiente');
            $record->update([
                'estado'      => 'contado',
                'estado_pago' => '1'
            ]);
           // session()->flash('msg-ok', 'La Factura fué enviada a Cuenta Corriente...');
            $this->emit('facturas_cobradas');
            DB::commit();

        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        return;
        // $record = CajaUsuario::where('id', $this->nro_arqueo);
        // $record->update([
        //     'estado' => '0'
        // ]);
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
    public function eliminarRegistro($id, $comentario) //elimina item
    {
        if ($id) {
            DB::begintransaction();
            try{
                $detFactura = Detfactura::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Detalle de Facturas Delivery',
                    'estado'          => '0',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $comentario,
                    'comercio_id'     => $this->comercioId
                ]);
                DB::commit();               
                $this->emit('registroEliminado'); 
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
            }
            $this->verDetalle($this->editFacturaId);
            $this->resetInput();
            return;
        }
    }
    public function anularFactura($id, $comentario) //anula la factura seleccionada
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
                    'tabla' => 'Facturas',
                    'estado' => '0',
                    'user_delete_id' => auth()->user()->id,
                    'comentario' => $comentario,
                    'comercio_id' => $this->comercioId
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
	public function grabarGastosModal($info)
	{
		$data = json_decode($info);
        DB::begintransaction();
        try{
            MovimientoDeCaja::create([
                'ingreso_id'  => null,
                'egreso_id'   => $data->gasto,
                'importe'     => $data->importe,
                'user_id'     => $this->repartidor,
                'comercio_id' => $this->comercioId,
                'arqueo_id'   => $this->nro_arqueo      //nro_arqueo repartidor
            ]);
            session()->flash('msg-ok', 'Movimiento creado exitosamente!!!');
            DB::commit();
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se creó...');
        }
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

    public function StoreOrUpdateCaja()  //no está en uso
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
