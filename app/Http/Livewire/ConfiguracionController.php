<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Categoria;
use App\Models\Comercio;
use App\Models\Producto;
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

            //actualizo los precios de venta sugeridos para los productos que tenga cargados con anterioridad
            $productos = Producto::where('comercio_id', $this->comercioId)->get();
            if($productos->count()){
                foreach ($productos as $i) {
                    $porcentaje = Categoria::where('id', $i->categoria_id)->select('margen_1', 'margen_2')->get();
                    if ($this->calcular_precio_de_venta == 0){
                        //calcula el precio de venta sumando el margen de ganancia al costo del producto
                        $pr_vta_sug_l1 = ($i->precio_costo * $porcentaje[0]->margen_1) / 100 + $i->precio_costo;
                        $pr_vta_sug_l2 = ($i->precio_costo * $porcentaje[0]->margen_2) / 100 + $i->precio_costo;
                    }else{
                        //calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
                        $pr_vta_sug_l1 = $i->precio_costo * 100 / (100 - $porcentaje[0]->margen_1);
                        $pr_vta_sug_l2 = $i->precio_costo * 100 / (100 - $porcentaje[0]->margen_2);
                    }
                    if ($this->redondear_precio_de_venta == 1){
                        $pr_vta_sug_l1 = round($pr_vta_sug_l1);
                        $pr_vta_sug_l2 = round($pr_vta_sug_l2);
                    }
                    $prod = Producto::find($i->id)->update([
                        'precio_venta_sug_l1' => $pr_vta_sug_l1,
                        'precio_venta_sug_l2' => $pr_vta_sug_l2
                    ]);
                }
            }   

            session()->flash('msg-ok', 'Configuraciones actualizadas');  
            DB::commit();               
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! Los registros no se grabaron...');
        }
        return;
    }
}
