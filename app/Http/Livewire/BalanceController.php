<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Compra;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\Stock;
use App\Models\StockEnConsignacion;
use App\Models\Subproducto;
use DB;

class BalanceController extends Component
{
    public $e_i, $compras, $e_f, $cmv;
    public $ventas, $p_cmv, $m_c, $p_m_c;
    public $comercioId;

    public function render()
    {
        //busca el comercio que está en sesión
		$this->comercioId = session('idComercio');
        
        //buscar facturas pendientes o arqueos abiertos

        $this->e_i = 48600;

        $this->compras();
        $this->stock_actual();
        $this->cmv = $this->e_i + $this->compras - $this->e_f;
        
        $this->ventas();
        $this->porcentajes();

        $this->m_c = $this->ventas - $this->cmv;
        if($this->ventas) $this->p_m_c = ($this->m_c * 100) / $this->ventas;
        
        return view('livewire.balance.component');
    }

    public function stock_actual()
    {
        $this->e_f = 0;
        $info = Producto::select('productos.*', DB::RAW("0 as stock_local"), DB::RAW("'' as stock_en_consignacion"),
            DB::RAW("'' as stock_total"), DB::RAW("0 as subtotal"))
            ->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();            
        foreach ($info as $i){
            $tiene_sp = Subproducto::where('producto_id', $i->id)->get();
            if($tiene_sp->count()){
                $total = 0;
                $total_c = 0;
                foreach ($tiene_sp as $l){
                    $stock = Stock::where('subproducto_id', $l->id)->first();
                    if($stock->count()) $total += $stock->stock_actual;

                    $stock_en_consig = StockEnConsignacion::where('subproducto_id', $l->id)->get()->sum('cantidad'); 
                    if($stock_en_consig) $total_c += $stock_en_consig;
                }
                $i->stock_local = $total;
                $i->stock_en_consignacion = $total_c;
            }else{
                $stock = Stock::where('producto_id', $i->id)->first();
                $i->stock_local = $stock->stock_actual;
               
                $stock_en_consig = StockEnConsignacion::where('producto_id', $i->id)->get()->sum('cantidad');  
                $i->stock_en_consignacion = $stock_en_consig;
            } 
            $i->stock_total = $i->stock_local + $i->stock_en_consignacion;
            $i->subtotal = $i->stock_total * $i->precio_costo;
            $this->e_f += $i->subtotal;
        }
    }
    public function compras()
    {
        $this->compras = Compra::where('comercio_id', $this->comercioId)->get()->sum('importe');
    }
    public function ventas()
    {
        $this->ventas = Factura::where('comercio_id', $this->comercioId)->get()->sum('importe');
    }
    public function porcentajes()
    {
        if($this->ventas) $this->p_cmv = ($this->cmv * 100) / $this->ventas;
    }
}
