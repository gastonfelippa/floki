<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\CajaUsuario;
use App\Models\Cliente;
use App\Models\Ctacte;
use App\Models\Detfactura;
use App\Models\Factura;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\User;
use DB;

class FacturasACobrarController extends Component
{
    
    public $salon, $action='1', $nombreCliente, $infoDetalle, $selected_id = null;
    public $producto, $productos, $cantidadEdit, $productoEdit, $precioEdit;
    public $editFacturaId ='', $comentario = '', $caja_abierta, $importeFactura;
    public $f_de_pago = null, $nro_comp_pago = null, $comentarioPago = '', $mercadopago = null;
    public $facturaId, $clienteId, $total_factura, $esConsFinal;
    public $comercioId, $modDelivery, $modComandas; 

    public function render()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        $this->modDelivery = session('modDelivery');
        $this->modComandas = session('modComandas');
        session(['facturaPendiente' => null]); 
        
        //averiguo el id del Cons Final
        $this->esConsFinal = Cliente::where('comercio_id', $this->comercioId)
            ->where('nombre', 'FINAL')->select('id')->first();
        $this->esConsFinal = $this->esConsFinal->id;

        //vemos si tenemos una caja habilitada con el user_id
        $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', auth()->user()->id)
            ->where('caja_usuarios.estado', '1')->select('caja_usuarios.*')->get();
        $this->caja_abierta = $caja_abierta->count();

        $this->productos = Producto::select()->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();

        //capturo el id del repartidor Salón para ver las facturas que lo indican como repartidor
        //pero que fueron facturadas por esta Caja y están asignadas al arqueo de la misma.
        $this->salon = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('users.name', '...')
            ->where('users.apellido', 'Salón')
            ->where('uc.comercio_id', $this->comercioId)
            ->select('users.id')->get();

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
        if($this->modDelivery == '1'){
            $info = Factura::join('clientes as c','c.id','facturas.cliente_id')
                ->join('users as u','u.id','facturas.repartidor_id')
                ->select('facturas.*', 'c.nombre as nomCli', 'c.apellido as apeCli', 'u.name as nomRep',
                        'u.apellido as apeRep')
                ->where('facturas.estado','like','pendiente')
                ->where('facturas.repartidor_id', $this->salon[0]->id)
                ->where('facturas.comercio_id', $this->comercioId)
                ->orderBy('facturas.id', 'asc')->get(); 
            // $info = Factura::select('facturas.*', DB::RAW("'' as descripcion"))
            //     ->where('facturas.comercio_id', $this->comercioId)
            //     ->where('facturas.estado','like','pendiente')
            //     ->where('facturas.user_id', auth()->user()->id)
            //     ->orderBy('id', 'asc')->get();
            // foreach($info as $i)
            // {
            //     if($i->mesa_id != null){
            //         $mesa = Mesa::find($i->mesa_id);
            //         $i->descripcion = $mesa->descripcion;
            //     }
            // }
        }else{
            $info = Factura::join('clientes as c','c.id','facturas.cliente_id')
                ->join('users as u','u.id','facturas.repartidor_id')
                ->select('facturas.*', 'c.nombre as nomCli', 'c.apellido as apeCli', 'u.name as nomRep',
                        'u.apellido as apeRep')
                ->where('facturas.comercio_id', $this->comercioId)
                ->where('facturas.estado','like','pendiente')
                ->where('facturas.repartidor_id', $this->salon[0]->id)
                ->orWhere('facturas.comercio_id', $this->comercioId)
                ->where('facturas.estado','like','pendiente')
                ->where('facturas.user_id', auth()->user()->id)
                ->orderBy('facturas.id', 'asc')->get(); 
        }
        return view('livewire.facturasacobrar.component', [
            'info' => $info
        ]);
    }
    public function resetInput()
    {
        $this->cantidadEdit = '';
        $this->productoEdit = 'Elegir';
        $this->precioEdit   = '';
        $this->selected_id  = 0;
        $this->comentario   = '';
        $this->action       = 1;
    }
    protected $listeners = [
        'factura_contado'   => 'factura_contado',
        'factura_ctacte'    => 'factura_ctacte',
        'eliminarRegistro'  => 'eliminarRegistro',
        'doAction'          => 'doAction',
        'elegirFormaDePago' => 'elegirFormaDePago',
        'enviarDatosPago'   => 'enviarDatosPago',      
        'anularFactura'     => 'anularFactura' 
    ];
    public function doAction($action)
	{
        $this->action = $action;
    }
    public function verDet($id)
    {
        session(['idMesa' => 'D']);
        session(['facturaPendiente' => $id]);
        if($this->modComandas == "1") return redirect()->to('/facturasbar');
        else return redirect()->to('/facturas');
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
    public function verDetalle($id)
    {
        $this->doAction(2);
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
        DB::begintransaction();       
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
    } 
    public function factura_contado()
    {
        DB::begintransaction();                         //iniciar transacción para grabar
        try{
            $record = Factura::find($this->facturaId);
            $record->update([
                'estado'        => 'contado',
                'estado_pago'   => '1',
                'importe'       => $this->total_factura,
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
        $this->resetInput();
    }
    public function factura_ctacte($data)
    {
        $info = json_decode($data);
        //$info = json_decode($cliId);
        if($info->cliente_id == $this->esConsFinal){
            $this->emit('esConsFinal');
            return;
        }else $this->clienteId = $info->cliente_id;
        $this->facturaId = $info->factura_id;
        $this->clienteId = $info->cliente_id;
        $total    = $info->total;
        DB::begintransaction();                         //iniciar transacción para grabar
        try{
            $record = Factura::find($this->facturaId);
            $record->update([
                'cliente_id'  => $this->clienteId,
                'estado'      => 'ctacte',
                'estado_pago' => '0',
                'importe'     => $total
            ]);
            Ctacte::create([
                'cliente_id' => $this->clienteId,
                'factura_id' => $this->facturaId
            ]);
            $record = Cliente::find($this->clienteId);   //marca que el cliente tiene un saldo en ctacte
            $record->update([
                'saldo' => '1'
            ]);
            DB::commit();
            $this->emit('facturaCtaCte');
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        return;
    }
    public function elegirFormaDePago($facturaId, $idCli, $total)
    {
        $this->facturaId = $facturaId;
        $this->total_factura = $total;
        $this->f_de_pago = '1';        
        $this->doAction(3);
    }
    public function enviarDatosPago($tipo,$nro)
    {
        $this->f_de_pago = $tipo;
        $this->nro_comp_pago = $nro;
    }
    public function eliminarRegistro($id, $comentario) //elimina item
    {
        $this->comentario= $comentario;
        if ($id) {
            DB::begintransaction();
            try{
                $detFactura = Detfactura::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Detalle de Facturas Pendientes',
                    'estado'          => '0',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $this->comentario,
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
    public function anularFactura($id, $comentario)    //anula la factura seleccionada
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
}
