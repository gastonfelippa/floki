<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\CtaCteClub;
use App\Models\Debito;
use App\Models\DebitoGenerado;
use App\Models\DetDebito;
use App\Models\Socio;
use App\Models\SocioActividad;
use DB;

class DebitoController extends Component
{

    public $mes = 'Elegir', $año = 'Elegir', $mes_año;
    public $socios, $numDebito;
    public $comercioId, $otrosDebitos;
    
    public function render()
    {
        $this->comercioId = session('idComercio');

        $encabezado = Debito::select('*')->where('comercio_id', $this->comercioId)->get(); 
        if($encabezado->count() == 0){ //si es el primer débito, le asigno el nro:
            $this->numDebito = 1;      
        }else{  //sino, le agrego 1 al último número de débito cargado
            $encabezado = Debito::select('numero')->where('comercio_id', $this->comercioId)
                ->orderBy('numero', 'desc')->get();                             
            $this->numDebito = $encabezado[0]->numero + 1;
        }
        return view('livewire.debitos.component');
    }
    private function resetInput()
    {
        $this->mes = 'Elegir';
        $this->año = 'Elegir';
    }
    protected $listeners = [
        'guardar' => 'StoreOrUpdate'    
    ];
    public function StoreOrUpdate()
    { 
        $this->validate([                      
            'mes' => 'not_in:Elegir',
            'año' => 'not_in:Elegir'
        ]);

        $this->mes_año = $this->mes . '-' . $this->año;
        $existe = DebitoGenerado::where('mes_año', $this->mes_año)->select('*')->get();
        if($existe->count()){
            $this->emit('debitoGeneradoExistente');
            return;
        }

        $this->socios = Socio::join('categoria_club as cc', 'cc.id', 'socios.categoria_id')
            ->where('socios.comercio_id', $this->comercioId)->where('socios.tipo', '1')
            ->where('socios.estado', 'Activo')
            ->select('socios.id', 'cc.importe as importe', DB::RAW("'' as totalAgrabar"), 
            DB::RAW("'' as tieneActividades"))->get();
        foreach($this->socios as $s){
            $debitoPorCategoria = $s->importe;
            $this->otrosDebitos=0;
            $tieneActividad=0;
            $info = SocioActividad::join('otros_debitos as od', 'od.id', 'socio_actividad.actividad_id')
                ->where('socio_actividad.socio_id', $s->id)->select('od.importe')->get();
            if($info->count()){
                foreach($info as $i){
                    $tieneActividad += 1;
                    $this->otrosDebitos += $i->importe;
                }
            }
            $s->totalAgrabar = $debitoPorCategoria + $this->otrosDebitos;
            $s->tieneActividades = $tieneActividad;
        }

        DB::begintransaction();
        try{
            $debito_generado =  DebitoGenerado::create([
                'mes_año'     => $this->mes_año,
                'user_id'     => auth()->user()->id, //id de quien confecciona el débito
                'comercio_id' => $this->comercioId          
            ]);           
            foreach($this->socios as $s){
                $debito =  Debito::create([
                    'numero'      => $this->numDebito,
                    'socio_id'    => $s->id,
                    'importe'     => $s->totalAgrabar,
                    'estado'      => 'ctacte',
                    'estado_pago' => '0',
                    'comercio_id' => $this->comercioId          
                ]);
                $detDebito = DetDebito::create([
                    'debito_id'          => $debito->id,
                    'debito_generado_id' => $debito_generado->id,
                    'actividad_id'       => null,
                    'importe'            => $s->importe
                ]);
                if($s->tieneActividades > 0){
                    $infoActividades = SocioActividad::join('otros_debitos as od', 'od.id', 'socio_actividad.actividad_id')
                        ->where('socio_actividad.socio_id', $s->id)->select('od.id', 'od.importe')->get();
                    foreach($infoActividades as $i){
                        $detDebito = DetDebito::create([
                            'debito_id'          => $debito->id,
                            'debito_generado_id' => null,
                            'actividad_id'       => $i->id,
                            'importe'            => $i->importe
                        ]);
                    }
                }
                $ctacte = CtaCteClub::create([
                    'socio_id'  => $s->id,
                    'debito_id' => $debito->id
                ]);
                $this->numDebito += 1;
            }           
            $this->emit('generarPdfDebitos');
            DB::commit(); 
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }

        $this->resetInput();
        return;
    } 
}
