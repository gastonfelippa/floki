<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Factura;
use Carbon\Carbon;

class VentasPorFechasController extends Component
{
    use WithPagination;

    public $fecha_ini, $fecha_fin;

    public function render()
    {
        $fi = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 00:00:00';
        $ff = Carbon::parse(Carbon::now())->format('Y-m-d'). ' 23:59:00';

        if($this->fecha_ini !==''){
            $fi = Carbon::parse($this->fecha_ini)->format('Y-m-d'). ' 00:00:00';
            $ff = Carbon::parse($this->fecha_fin)->format('Y-m-d'). ' 23:59:00';
        }

        $cantVentas = Factura::leftjoin('clientes as c', 'c.id', 'facturas.cliente_id')
        ->leftjoin('empleados as e', 'e.id', 'facturas.repartidor_id')
        ->select('facturas.*', 'c.nombre as cliente', 'e.nombre as repartidor')
        ->whereBetween('facturas.created_at', [$fi, $ff])
        ->where('facturas.estado', 'PAGADA')
        ->where('facturas.cliente_id', '<>', null)
        ->orderBy('id', 'desc');

        $ventas = Factura::leftjoin('clientes as c', 'c.id', 'facturas.cliente_id')
        ->leftjoin('empleados as e', 'e.id', 'facturas.repartidor_id')
        ->select('facturas.*', 'c.nombre as cliente', 'e.nombre as repartidor')
        ->whereBetween('facturas.created_at', [$fi, $ff])
        ->where('facturas.estado', 'PAGADA')
        ->where('facturas.cliente_id', '<>', null)
        ->orderBy('id', 'desc')
        ->paginate(5);

    //   $ventas = Renta::leftjoin('tarifas as t', 't.id', 'rentas.tarifa_id')
    //    ->leftjoin('users as u', 'u.id', 'rentas.user_id')
    //    ->select('rentas.*', 't.costo as tarifa', 't.descripcion as vehiculo', 'u.nombre as usuario')
    //    ->whereBetween('rentas.created_at', [$fi, $ff])
    //    ->orderBy('rentas.barcode','desc')
    //    ->paginate(10);

        $total = Factura::whereBetween('created_at', [$fi, $ff])->where('estado', 'PAGADA')->sum('importe');

        return view('livewire.reportes.component-ventas-por-fechas', [
            'info'      => $ventas,
            'sumaTotal'=> $total,
            'cantVentas'=> $cantVentas
        ]);
    }
}