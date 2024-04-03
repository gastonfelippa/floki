<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\CajaUsuario;
use App\Models\Gasto;
use App\Models\MovimientoDeCaja;
use App\Models\OtroIngreso;
use App\Models\Proveedor;
use DB;

class MovimientosDeCajaController extends Component
{
    public $proveedor='Elegir', $egreso='Elegir', $otro_ingreso='Elegir', $comentario = '';
    public $comercioId, $estado = 1, $mov_importe, $selected_id = null, $caja_abierta, $nro_arqueo;
    public $fecha_inicio, $categoria;

    public function render()
    {        
        $this->comercioId = session('idComercio');  //busca el comercio que está en sesión
        session(['facturaPendiente' => null]);  
        //vemos si tenemos una caja habilitada para nuestro user_id
        $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', auth()->user()->id)
            ->where('caja_usuarios.estado', '1')->get();
        $this->caja_abierta = $caja_abierta->count();
        if($caja_abierta->count() > 0){
            $this->nro_arqueo = $caja_abierta[0]->id;
            $this->fecha_inicio = $caja_abierta[0]->created_at;  
        }

        $egresos = Gasto::where('comercio_id', $this->comercioId)->get();
        $proveedores = Proveedor::where('comercio_id', $this->comercioId)->get();
        // $proveedores = Proveedor::join('users as u', 'u.comercio_id', 'proveedores.comercio_id')
        //                 ->where('proveedores.comercio_id', $this->comercioId)->get();
        // dd($proveedores);
        $ingresos = OtroIngreso::where('comercio_id', $this->comercioId)->get();
     
        switch ($this->estado) {    //'estado' indica si se ven egresos u otros ingresos de entrada al form
            case 1:                 //egresos
                $info = MovimientoDeCaja::join('gastos as p', 'p.id', 'movimiento_de_cajas.egreso_id')
                    ->where('movimiento_de_cajas.egreso_id', '<>', null)
                    ->where('movimiento_de_cajas.arqueo_id', $this->nro_arqueo)
                    ->orderBy('movimiento_de_cajas.created_at')
                    ->select('movimiento_de_cajas.*', 'p.descripcion')->get();    
                break;
            case 2:                 //otros ingresos
                $info = MovimientoDeCaja::join('otro_ingresos as g', 'g.id', 'movimiento_de_cajas.ingreso_id')
                    ->where('movimiento_de_cajas.ingreso_id', '<>', null)
                    ->where('movimiento_de_cajas.arqueo_id', $this->nro_arqueo)
                    ->orderBy('movimiento_de_cajas.created_at')
                    ->select('movimiento_de_cajas.*', 'g.descripcion')->get();
                break;
            default:
        }
        return view('livewire.movimientos_de_caja.component',[
            'info' => $info,
            'proveedores' => $proveedores,
            'egresos' => $egresos,
            'ingresos' => $ingresos
        ]);
    }

    private function resetInput()
    {
        $this->selected_id = null;  
        $this->proveedor = 'Elegir';
        $this->otro_ingreso  = '';
        $this->categoria   = 'Elegir';
    }

    protected $listeners = [
        'deleteRow'=>'destroy',
        'createFromModal' => 'createFromModal'       
    ];

    public function createFromModal($info)
    {
        $data = json_decode($info);

        DB::begintransaction();
        try{  
            if($data->edit_ing_egr == 1){               //editar egreso
                $record = MovimientoDeCaja::find($data->mov_id);  
                $record->update([                       
                    'egreso_id' => $data->ing_egr_id,
                    'importe'   => $data->mov_importe
                ]);
            }elseif($data->edit_ing_egr == 2){          //editar ingreso
                $record = MovimientoDeCaja::find($data->mov_id);  
                $record->update([                       
                    'ingreso_id' => $data->ing_egr_id,
                    'importe'    => $data->mov_importe
                ]);
            }else{                                      //registro nuevo
                if($this->estado == 1) $this->proveedor = $data->ing_egr_id;
                else $this->otro_ingreso = $data->ing_egr_id;
        
                if($this->proveedor == 'Elegir') $this->proveedor = null;
                if($this->otro_ingreso == 'Elegir') $this->otro_ingreso = null;

                MovimientoDeCaja::create([
                    'ingreso_id'  => $this->otro_ingreso,
                    'egreso_id'   => $this->proveedor,
                    'importe'     => $data->mov_importe,
                    'user_id'     => auth()->user()->id,
                    'comercio_id' => $this->comercioId,
                    'arqueo_id'   => $this->nro_arqueo
                ]);
            }
            session()->flash('msg-ok', 'Movimiento creado exitosamente!!!');
            DB::commit();               
            $this->emit('agregarDetalle'); 
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se creó...');
        }
    }    
    public function destroy($id)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $movimiento = MovimientoDeCaja::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Movimiento De Caja',
                    'estado'          => '0',
                    'user_delete_id'  => auth()->user()->id,
                    'comentario'      => $this->comentario,
                    'comercio_id'     => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Registro eliminado con éxito!!');
                DB::commit();               
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
            }
            $this->resetInput();
            return;
        }
    } 
}
