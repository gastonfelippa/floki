<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Comercio;
use DB;

class ConfiguracionController extends Component
{
    public $leyenda_factura, $periodo_arqueo, $imp_por_hoja, $imp_duplicado, $hora_apertura;
    public $calcular_precio_de_venta, $redondear_precio_de_venta;
    public $opcion_de_guardado_compra, $opcion_de_guardado_producto;
    public $comercioId;

    public function mount()
    {
        $this->comercioId = session('idComercio');
       
        $comercio = Comercio::find($this->comercioId);
        if($comercio->count())
        {
            $this->leyenda_factura             = $comercio->leyenda_factura;
            $this->hora_apertura               = $comercio->hora_apertura;
            $this->periodo_arqueo              = $comercio->periodo_arqueo;
            $this->imp_por_hoja                = $comercio->imp_por_hoja;
            $this->imp_duplicado               = $comercio->imp_duplicado;
            $this->calcular_precio_de_venta    = $comercio->calcular_precio_de_venta;
            $this->redondear_precio_de_venta   = $comercio->redondear_precio_de_venta;
            $this->opcion_de_guardado_compra   = $comercio->opcion_de_guardado_compra;
            $this->opcion_de_guardado_producto = $comercio->opcion_de_guardado_producto;
        }
    }
    public function render()
    {
        return view('livewire.configuraciones.component');
    }
    public function StoreOrUpdate()
    {
        if(!$this->periodo_arqueo || $this->periodo_arqueo == 0) $this->periodo_arqueo = '';
        $this->validate([
            'periodo_arqueo' => 'required'
        ]);  
        DB::begintransaction();
        try{  
            $comercio = Comercio::find($this->comercioId);
            $comercio->update([
                'leyenda_factura'             => $this->leyenda_factura,
                'hora_apertura'               => $this->hora_apertura,
                'periodo_arqueo'              => $this->periodo_arqueo,
                'imp_por_hoja'                => $this->imp_por_hoja,
                'imp_duplicado'               => $this->imp_duplicado,
                'calcular_precio_de_venta'    => $this->calcular_precio_de_venta,
                'redondear_precio_de_venta'   => $this->redondear_precio_de_venta,
                'opcion_de_guardado_compra'   => $this->opcion_de_guardado_compra,
                'opcion_de_guardado_producto' => $this->opcion_de_guardado_producto
            ]);   
            session()->flash('msg-ok', 'Configuraciones actualizadas');  
            DB::commit();               
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! Los registros no se grabaron...');
        }
        return;
    }
}
