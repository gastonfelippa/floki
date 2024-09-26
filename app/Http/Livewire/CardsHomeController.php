<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Detpedido;
use App\Models\Factura;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Peps;
use App\Models\Producto;
use App\Models\Reserva;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use DB;

class CardsHomeController extends Component
{

    public $comercioId, $mozos, $tab, $tabP;

    public function render()
    {
        $this->comercioId = session('idComercio');
        session(['asignarReserva' => null]);

        $this->mozos = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('uc.comercio_id', $this->comercioId)->select('users.*')->orderBy('apellido')->get();

        $hoy = Carbon::now();  
        $hoy_solo_fecha = Carbon::parse($hoy)->format('Y-m-d');   

        $infoReservasP = Reserva::where('comercio_id', $this->comercioId)
            ->where('fecha', $hoy_solo_fecha)
            ->where('estado', 'Pendiente')
            ->select('id', 'nombre', 'apellido', 'cantidad', 'horario')
            ->get();    

        $infoReservasA = Reserva::where('comercio_id', $this->comercioId)
            ->where('fecha', $hoy_solo_fecha)
            ->where('estado', 'Asignada')
            ->select('*', DB::RAW("'' as mesaDesc"))
            ->orderBy('horario')->get();

        if ($infoReservasA->count() > 0) $this->tab = "asignada";
        else $this->tab = "sinasignar";

        foreach ($infoReservasA as $i) {
            $mesaDesc = Mesa::find($i->mesa_id);
            $i->mesaDesc = $mesaDesc->descripcion;
        } 
        
        $infoProductosCompra = [];
        $infoProdCompra = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
            ->where('c.tipo_id', 2)
            ->where('productos.comercio_id', $this->comercioId)
            ->where('productos.controlar_stock', 'si')
            ->orWhere('c.tipo_id', 2)
            ->where('productos.comercio_id', $this->comercioId)
            ->where('productos.controlar_stock', 'si')
            ->select('productos.id', 'productos.descripcion')
            ->get();
        if ($infoProdCompra->count() > 0) {
            foreach ($infoProdCompra as $i) {
                $peps = Peps::where('producto_id', $i->id)->sum('resto');
                if ($peps <= 0) $infoProductosCompra[] = $i->descripcion;
            }
        }

        $infoProductosVenta = [];
        $infoProdVenta = Producto::join('categorias as c', 'c.id', 'productos.categoria_id')
            ->where('c.tipo_id', 2)
            ->where('productos.comercio_id', $this->comercioId)
            ->where('productos.controlar_stock', 'si')
            ->orWhere('c.tipo_id', 3)
            ->where('productos.comercio_id', $this->comercioId)
            ->where('productos.controlar_stock', 'si')
            ->select('productos.id', 'productos.descripcion')
            ->get();
        if ($infoProdVenta->count() > 0) {
            foreach ($infoProdVenta as $i) {
                $peps = Peps::where('producto_id', $i->id)->sum('resto');
                if ($peps <= 0) $infoProductosVenta[] = $i->descripcion;
            }
        }
            
        $infoPedidosC = Pedido::join('proveedores as p', 'p.id', 'pedidos.proveedor_id')
            ->where('pedidos.comercio_id', $this->comercioId)
            ->where('pedidos.estado', 'cargado')
            ->select('pedidos.id', 'p.nombre_empresa', DB::RAW("0 as importe"))
            ->get();
        if ($infoPedidosC->count()) {
            foreach ($infoPedidosC as $i) {
                $detalle = Detpedido::join('productos as p', 'p.id', 'detpedidos.producto_id')
                    ->where('detpedidos.pedido_id', $i->id)
                    ->select('detpedidos.cantidad', 'p.precio_costo')->get();
                $total = 0;
                if ($detalle->count()) {
                    foreach ($detalle as $j) {
                        $total += $j->cantidad * $j->precio_costo;
                    }
                }
                $i->importe = $total;
            }
        }

        $infoPedidosP = Pedido::join('proveedores as p', 'p.id', 'pedidos.proveedor_id')
            ->where('pedidos.comercio_id', $this->comercioId)
            ->where('pedidos.estado', 'pedido')
            ->select('pedidos.id', 'p.nombre_empresa', DB::RAW("0 as importe"))
            ->get();
        if ($infoPedidosP->count()) {
            foreach ($infoPedidosP as $i) {
                $detalle = Detpedido::join('productos as p', 'p.id', 'detpedidos.producto_id')
                    ->where('detpedidos.pedido_id', $i->id)
                    ->select('detpedidos.cantidad', 'p.precio_costo')->get();
                $total = 0;
                if ($detalle->count()) {
                    foreach ($detalle as $j) {
                        $total += $j->cantidad * $j->precio_costo;
                    }
                }
                $i->importe = $total;
            }
        }

        if ($infoPedidosC->count() > 0) $this->tabP = "cargado";
        else $this->tabP = "pedido";

        $infoMesas = Factura::join('mesas as m', 'm.id', 'facturas.mesa_id')
            ->join('users as u', 'u.id', 'facturas.mozo_id')
            ->where('facturas.comercio_id', $this->comercioId)
            ->where('facturas.estado', 'abierta')
            ->where('facturas.impresion', 1)
            ->where('facturas.mesa_id', '<>', null)
            ->select('facturas.id', 'facturas.mesa_id', 'm.descripcion as mesa', 'u.name', 'u.apellido', 
                     'facturas.importe')
            ->get();

        return view('livewire.cardshome.component', [
            'infoReservasP'       => $infoReservasP,
            'infoReservasA'       => $infoReservasA,
            'infoProductosCompra' => $infoProductosCompra,
            'infoProductosVenta'  => $infoProductosVenta,
            'infoPedidosC'        => $infoPedidosC,
            'infoPedidosP'        => $infoPedidosP,
            'infoMesas'           => $infoMesas
        ]);
    }
    protected $listeners = [
        'agregaMozo' => 'agregaMozo',
        'cerrar' => 'cerrar'
    ];
    public function cerrar(){
        return redirect()->to('/reservas-estado-mesas');
    }
    public function abrirReservaPendiente($reservaId)
    { 
        session(['asignarReserva' => $reservaId]);    
        return redirect()->to('/reservas-estado-mesas'); 
    }
    public function abrirReservaAsignada($mesaId)
    {     
        session(['idMesa' => $mesaId]);
        $buscar_mesa = Mesa::find($mesaId);
        $mesa = $buscar_mesa->descripcion;
        $cliente = Reserva::where('mesa_id', $mesaId)->get();
        $cliente = $cliente[0]->apellido . ' ' . $cliente[0]->nombre;
        $this->emit('abrir_mesa_reserva', $mesa, $cliente);
    }
    public function abrirPedido()
    {       
        return redirect()->to('/pedidos');  
    }
    public function abrirMesa($mesaId)
    {       
        session(['idMesa' => $mesaId]);
        return redirect()->to('/facturasbar');  
    }
    public function agregaMozo($idMozo)
    {
        if($idMozo){
            session(['idMozo' => $idMozo]);
            session(['facturaPendiente' => null]);
            return redirect()->to('/facturasbar');
        }
    }

}
