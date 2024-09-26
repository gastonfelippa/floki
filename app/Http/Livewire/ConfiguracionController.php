<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Traits\GenericTrait;
use App\Models\Auditoria;
use App\Models\Categoria;
use App\Models\Comanda;
use App\Models\Comercio;
use App\Models\Compra;
use App\Models\Detcomanda;
use App\Models\Detcompra;
use App\Models\Detfactura;
use App\Models\DetMetodoPago;
use App\Models\Factura;
use App\Models\Mesa;
use App\Models\Peps;
use App\Models\Producto;
use Carbon\Carbon;
use DB;
use App\Models\Role;
use App\Models\ModelHasRole;
use App\Models\User;
use App\Models\ArqueoGral;
use Illuminate\Support\Facades\Mail;

class ConfiguracionController extends Component
{
    use GenericTrait;

    public $leyenda_factura, $periodo_arqueo, $hora_apertura;
    public $calcular_precio_de_venta, $redondear_precio_de_venta;
    public $opcion_de_guardado_compra, $opcion_de_guardado_producto;
    public $venta_sin_stock, $imp_por_hoja, $imp_duplicado; 
    public $comercioId, $error = 1, $action_edit = 'datos';

    public function mount()
    {
        $this->comercioId = session('idComercio');
       
        $comercio = Comercio::find($this->comercioId);
        if($comercio)
        {
            $this->leyenda_factura             = $comercio->leyenda_factura;
            $this->hora_apertura               = $comercio->hora_apertura;
            $this->periodo_arqueo              = $comercio->periodo_arqueo;
            $this->venta_sin_stock             = $comercio->venta_sin_stock;
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
    protected $listeners = [
        'borrarDatos',
        'recuperarDatos',
        'action_edit'
    ];
    public function action_edit($actionEdit)
	{
		$this->action_edit = $actionEdit;
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
            if ($comercio) {
                $comercio->update([
                    'leyenda_factura'             => $this->leyenda_factura,
                    'hora_apertura'               => $this->hora_apertura,
                    'periodo_arqueo'              => $this->periodo_arqueo,
                    'venta_sin_stock'             => $this->venta_sin_stock,
                    'imp_por_hoja'                => $this->imp_por_hoja,
                    'imp_duplicado'               => $this->imp_duplicado,
                    'calcular_precio_de_venta'    => $this->calcular_precio_de_venta,
                    'redondear_precio_de_venta'   => $this->redondear_precio_de_venta,
                    'opcion_de_guardado_compra'   => $this->opcion_de_guardado_compra,
                    'opcion_de_guardado_producto' => $this->opcion_de_guardado_producto
                ]); 
            } else {
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El Comercio no existe...');
                return;
            }

            //actualizo los precios de venta sugeridos para los productos que tenga cargados con anterioridad
            // $productos = Producto::where('comercio_id', $this->comercioId)->get();
            // if($productos->count()){
            //     foreach ($productos as $i) {
            //         $porcentaje = Categoria::where('id', $i->categoria_id)->select('margen_1', 'margen_2')->get();
            //         if ($this->calcular_precio_de_venta == 0){
            //             //calcula el precio de venta sumando el margen de ganancia al costo del producto
            //             $pr_vta_sug_l1 = ($i->precio_costo * $porcentaje[0]->margen_1) / 100 + $i->precio_costo;
            //             $pr_vta_sug_l2 = ($i->precio_costo * $porcentaje[0]->margen_2) / 100 + $i->precio_costo;
            //         }else{
            //             //calcula el precio de venta obteniendo el margen de ganancia sobre el mismo
            //             $pr_vta_sug_l1 = $i->precio_costo * 100 / (100 - $porcentaje[0]->margen_1);
            //             $pr_vta_sug_l2 = $i->precio_costo * 100 / (100 - $porcentaje[0]->margen_2);
            //         }
            //         if ($this->redondear_precio_de_venta == 1){
            //             $pr_vta_sug_l1 = round($pr_vta_sug_l1);
            //             $pr_vta_sug_l2 = round($pr_vta_sug_l2);
            //         }
            //         $prod = Producto::find($i->id)->update([
            //             'precio_venta_sug_l1' => $pr_vta_sug_l1,
            //             'precio_venta_sug_l2' => $pr_vta_sug_l2
            //         ]);
            //     }
            // }   
            session()->flash('msg-ok', 'Configuraciones actualizadas');  
            DB::commit();               
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! Los registros no se grabaron...');
        }
        return;
    }
    public function borrarDatos()
    {
        //Elimina todos datos con soft-delete y vuelve a cargar todos los productos pero con stock inicial en cero
        DB::beginTransaction();
        try {
            $mesa_status = Mesa::where('comercio_id', $this->comercioId)->get();
            if ($mesa_status->count() > 0) {
                foreach ($mesa_status as $i) $i->update(['estado' => 'Disponible']);
            }

            $detComandas = Detcomanda::where('comercio_id', $this->comercioId)->count();
            $comandas = Comanda::where('comercio_id', $this->comercioId)->count();
            $detMetodoPagos = DetMetodoPago::where('comercio_id', $this->comercioId)->count();
            $detFacturas = Detfactura::where('comercio_id', $this->comercioId)->count();
            $facturas = Factura::where('comercio_id', $this->comercioId)->count();
            $detCompras = Detcompra::where('comercio_id', $this->comercioId)->count();
            $compras = Compra::where('comercio_id', $this->comercioId)->count();
            $peps = Peps::where('comercio_id', $this->comercioId)->count();
            $auditorias = Auditoria::where('comercio_id', $this->comercioId)->count();

            if ($detComandas > 0) Detcomanda::where('comercio_id', $this->comercioId)->delete();
            if ($comandas > 0) Comanda::where('comercio_id', $this->comercioId)->delete();
            if ($detMetodoPagos > 0) DetMetodoPago::where('comercio_id', $this->comercioId)->delete();
            if ($detFacturas > 0) Detfactura::where('comercio_id', $this->comercioId)->delete();
            if ($facturas > 0) Factura::where('comercio_id', $this->comercioId)->delete();
            if ($detCompras > 0) Detcompra::where('comercio_id', $this->comercioId)->delete();
            if ($compras > 0) Compra::where('comercio_id', $this->comercioId)->delete();
            if ($auditorias > 0) Auditoria::where('comercio_id', $this->comercioId)->delete();
            if ($peps > 0) Peps::where('comercio_id', $this->comercioId)->delete();
            
            $productos = Producto::where('comercio_id', $this->comercioId)->get();
            if ($productos->count() > 0) { //agrego todos los productos con EI = 0
                foreach ($productos as $i) $actualizarStock = $this->actualizarStockTrait(1, false, false, null, null, null, $i->id, $i->precio_costo, null);
            }
            if ($actualizarStock) {
                DB::commit();
                session()->flash('msg-ok', 'Las tablas seleccionadas se blanquearon correctamente!!');
            } else {
                DB::rollback();
                session()->flash('msg-ops', 'El blanqueo de tablas no se pudo realizar porque hubo problemas' .
                ' al grabar la Existencia Inicial...');
            }
            
        } catch (Exception $e) {
            DB::rollback();
            session()->flash('msg-ops', 'El blanqueo de tablas no se pudo realizar, inténtalo nuevamente...');
        } 
    }
    public function truncate_peps()
    {
        if(Peps::truncate()) session()->flash('msg-ok', 'La tabla Peps se blanqueó correctamente!!');
        else session()->flash('msg-ops', 'El blanqueo de tablas no se pudo realizar porque hubo problemas' .
            ' al grabar la Existencia Inicial...');
    }
    public function recuperarDatos()
    {
        try {
            $fecha = Carbon::now()->toDateString(); // Fecha que deseas consultar
            $fechaCarbon = Carbon::parse($fecha);
            $fechaCarbon = "2024-07-05";
            //$resultados = Peps::onlyTrashed()->whereDate('deleted_at', '=', $fechaCarbon)->get();
            $resultados = Factura::onlyTrashed()->whereDate('deleted_at', '=', $fechaCarbon)->get();
            if ($resultados->count() > 0) {
                foreach ($resultados as $i) $i->restore();
            }
            session()->flash('msg-ok', 'Las tablas seleccionadas se recuperaron correctamente!!');
        } catch (\Exception $e) {
            session()->flash('msg-ops', 'Las tablas no se pudieron recuperar...');
        }
    }
}
