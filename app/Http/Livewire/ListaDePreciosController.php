<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\StockEnConsignacion;
use DB;

class ListaDePreciosController extends Component
{
    public $comercioId, $action = 1, $lista = 1;

    public function render()
    {
        //busca el comercio que está en sesión
		$this->comercioId = session('idComercio');
        session(['facturaPendiente' => null]);  

        if($this->lista == 1){
            $info = Producto::select('codigo', 'descripcion', 'precio_venta_l1 as precio')
                ->where('comercio_id', $this->comercioId)->get();
        }else{
            $info = Producto::select('codigo', 'descripcion', 'precio_venta_l2 as precio')
                ->where('comercio_id', $this->comercioId)->get();
        }
        return view('livewire.listadeprecios.component', ['info' => $info]);
    }
    public function verLista($numero)
    {
        $this->lista = $numero;
    }
}
