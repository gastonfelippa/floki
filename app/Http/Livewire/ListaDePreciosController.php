<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Producto;

class ListaDePreciosController extends Component
{
    public $search, $comercioId, $action = 1, $lista = 1;

    public function render()
    {
        //busca el comercio que está en sesión
		$this->comercioId = session('idComercio');

        if($this->lista == 1){
            if(strlen($this->search) > 0){
                $info = Producto::select('codigo', 'descripcion', 'precio_venta_l1 as precio', 'estado')
                ->where('comercio_id', $this->comercioId)
                ->where('tipo', 'like', 'Art. Compra/Venta')
                ->where('estado', 'Disponible')
                ->where('descripcion', 'like', '%' .  $this->search . '%')
                ->orWhere('comercio_id', $this->comercioId)
                ->where('tipo', 'like', 'Art. Venta c/receta')
                ->where('descripcion', 'like', '%' .  $this->search . '%')
                ->where('estado', 'Disponible')
                ->get();
            }else{
                $info = Producto::select('codigo', 'descripcion', 'precio_venta_l1 as precio', 'estado')
                    ->where('comercio_id', $this->comercioId)
                    ->where('tipo', 'like', 'Art. Compra/Venta')
					->where('estado', 'Disponible')
                    ->orWhere('comercio_id', $this->comercioId)
                    ->where('tipo', 'like', 'Art. Venta c/receta')
					->where('estado', 'Disponible')
                    ->get();
            }
        }else{
            if(strlen($this->search) > 0){
                $info = Producto::select('codigo', 'descripcion', 'precio_venta_l2 as precio', 'estado')
                ->where('comercio_id', $this->comercioId)
                ->where('tipo', 'like', 'Art. Compra/Venta')
                ->where('estado', 'Disponible')
                ->where('descripcion', 'like', '%' .  $this->search . '%')
                ->orWhere('comercio_id', $this->comercioId)
                ->where('tipo', 'like', 'Art. Venta c/receta')
                ->where('estado', 'Disponible')
                ->where('descripcion', 'like', '%' .  $this->search . '%')
                ->get();
            }else{
                $info = Producto::select('codigo', 'descripcion', 'precio_venta_l2 as precio', 'estado')
                    ->where('comercio_id', $this->comercioId)
                    ->where('tipo', 'like', 'Art. Compra/Venta')
					->where('estado', 'Disponible')
                    ->orWhere('comercio_id', $this->comercioId)
                    ->where('tipo', 'like', 'Art. Venta c/receta')
					->where('estado', 'Disponible')
                    ->get();
            }
        }
        return view('livewire.listadeprecios.component', ['info' => $info]);
    }
    public function verLista($numero)
    {
        $this->lista = $numero;
    }
}
