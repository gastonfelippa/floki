<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Comercio;
use DB;

class ConfiguracionController extends Component
{
    public $leyenda_factura, $periodo_arqueo, $imp_por_hoja, $imp_duplicado;
    public $comercioId;

    public function mount()
    {
        $this->comercioId = session('idComercio');
        $comercio = Comercio::where('id', $this->comercioId)->get();
        if($comercio->count())
        {
            $this->leyenda_factura = $comercio[0]->leyenda_factura;
            $this->periodo_arqueo  = $comercio[0]->periodo_arqueo;
            $this->imp_por_hoja    = $comercio[0]->imp_por_hoja;
            $this->imp_duplicado   = $comercio[0]->imp_duplicado;
        }
    }
    public function render()
    {
        return view('livewire.configuraciones.component');
    }
    public function doAction($action)
    {}
    public function StoreOrUpdate()
    {
        $this->validate([
            'periodo_arqueo' => 'required'
        ]);  
        DB::begintransaction();
        try{  
            $comercio = Comercio::find($this->comercioId);
            $comercio->update([
                'leyenda_factura' => $this->leyenda_factura,
                'periodo_arqueo'  => $this->periodo_arqueo,
                'imp_por_hoja'    => $this->imp_por_hoja,
                'imp_duplicado'   => $this->imp_duplicado
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
