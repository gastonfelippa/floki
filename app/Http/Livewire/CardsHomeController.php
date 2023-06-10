<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Factura;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Reserva;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use DB;

class CardsHomeController extends Component
{

    public $comercioId, $mozos;

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
        foreach ($infoReservasA as $i) {
            $mesaDesc = Mesa::find($i->mesa_id);
            $i->mesaDesc = $mesaDesc->descripcion;
        }  

        $infoProductos = Stock::join('productos as p', 'p.id', 'stock.producto_id')
            ->where('p.comercio_id', $this->comercioId)
            ->where('p.controlar_stock', 'si')
            ->where('stock.stock_actual', '<=', 0)
            ->orWhere('p.comercio_id', $this->comercioId)
            ->where('p.controlar_stock', 'si')
            ->where('stock.stock_actual', null)
            ->select('p.descripcion')
            ->get();
            
        $infoPedidos = Pedido::join('proveedores as p', 'p.id', 'pedidos.proveedor_id')
            ->where('pedidos.comercio_id', $this->comercioId)
            ->where('pedidos.estado', 'cargado')
            ->select('p.nombre_empresa')
            ->get();

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
            'infoReservasP'  => $infoReservasP,
            'infoReservasA'  => $infoReservasA,
            'infoProductos'  => $infoProductos,
            'infoPedidos'    => $infoPedidos,
            'infoMesas'      => $infoMesas
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
