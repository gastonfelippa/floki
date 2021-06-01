<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Caja;
use App\Models\CajaInicial;
use App\Models\CajaUsuario;
use App\Models\User;
use DB;

class CajaController extends Component
{	
    public $descripcion, $action = 1, $caja = 'Elegir', $usuario = 'Elegir';            
    public $selected_id, $caja_usuario, $estado = '0', $habilitar = '0', $importe; 
    public $comercioId;
    public $recuperar_registro = 0, $descripcion_soft_deleted, $id_soft_deleted, $comentario = '';

    public function render()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');

        $cajas = Caja::where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();

        $usuarios = User::join('usuario_comercio as uc', 'uc.usuario_id', 'users.id')
            ->where('comercio_id', $this->comercioId)->select('users.*')->get();

        $info = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
            ->join('users as u', 'u.id', 'caja_usuarios.caja_usuario_id')
            ->where('c.comercio_id', $this->comercioId)
            ->where('caja_usuarios.estado', '1')
            ->select('caja_usuarios.*', 'c.descripcion', 'u.name', 'u.apellido', 
                DB::RAW("'' as apeNomCajaHab"))
            ->orderby('c.descripcion')->get();
        foreach($info as $i){
            $info2 = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
                ->join('users as u', 'u.id', 'caja_usuarios.user_id')
                ->where('caja_usuarios.id', $i->id)
                ->select('u.name', 'u.apellido')->get();
            $i->apeNomCajaHab = $info2[0]->apellido . ' ' . $info2[0]->name;            
        }


        return view('livewire.cajausuario.component', [
            'info' =>$info,
            'cajas' =>$cajas,
            'usuarios' =>$usuarios
        ]);
    }

    public function doAction($action)
    {
        $this->action = $action;
        $this->resetInput();
    }

    private function resetInput()
    {
        $this->descripcion = '';
        $this->selected_id = null; 
        $this->caja = 'Elegir';
        $this->usuario = 'Elegir';
        $this->habilitar = '0';
        $this->estado = '0';
        $this->importe = '';
    }

    public function edit($id)
    {
        $record = CajaUsuario::join('caja_inicials as ci', 'ci.caja_user_id', 'caja_usuarios.id')
            ->where('caja_usuarios.id', $id)->select('caja_usuarios.*', 'ci.importe')->first();         
        $this->selected_id = $id;
        $this->caja = $record->caja_id;
        $this->usuario = $record->caja_usuario_id;
        $this->importe = $record->importe;
        $this->estado = $record->estado;
        $this->habilitar = $record->estado;
        
        $this->action = 2;
    }

    public function StoreOrUpdate()
    { 
        $this->validate([
            'caja' => 'not_in:Elegir',
            'usuario' => 'not_in:Elegir',
        ]);  

        DB::begintransaction();
        try{
            if($this->selected_id > 0) {
                $existe = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
                    ->where('c.comercio_id', $this->comercioId)
                    ->where('caja_usuarios.caja_id', $this->caja)
                    ->where('caja_usuarios.estado', '1')
                    ->where('caja_usuarios.id', '<>', $this->selected_id)
                    ->withTrashed()->get();
                if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                    session()->flash('info', 'La Caja que desea modificar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->descripcion;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count() > 0) {
                    session()->flash('info', 'El Operador ya está asignado a otra Caja...');
                    $this->resetInput();
                    return;
                }
            }else { //verificamos si la Caja no está asignada previamente
                $existe = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
                    ->where('c.comercio_id', $this->comercioId)
                    ->where('caja_usuarios.caja_id', $this->caja)
                    ->where('caja_usuarios.estado', '1')
                    ->select('caja_usuarios.*');
                if($existe->count() > 0) {
                    session()->flash('info', 'La Caja ya está asignada a otro Operador...');
                    $this->resetInput();
                    return;
                }      //verificamos si el Operador no está asignado previamente
                $existe = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
                    ->where('c.comercio_id', $this->comercioId)
                    ->where('caja_usuarios.caja_usuario_id', $this->usuario)
                    ->where('caja_usuarios.estado', '1');
                if($existe->count() > 0) {
                    session()->flash('info', 'El Operador ya está asignado a otra Caja...');
                    $this->resetInput();
                    return;
                }
            }
            if($this->selected_id == 0) {
                $cajausuario =  CajaUsuario::create([
                    'caja_id' => $this->caja,
                    'caja_usuario_id'  => $this->usuario,
                    'estado' => '1',
                    'user_id' => auth()->user()->id
                ]);
                $cajainicial = CajaInicial::create([
                    'caja_user_id' => $cajausuario->id,
                    'importe'      => $this->importe,
                    'user_id'      => auth()->user()->id
                ]);
            }else {   
                $cajausuario = CajaUsuario::find($this->selected_id);
                $cajausuario->update([
                    'caja_id' => $this->caja,
                    'caja_usuario_id'  => $this->usuario,
                    'estado' => '1',
                    'user_id' => auth()->user()->id
                ]);
                $cajainicial = CajaInicial::find($this->selected_id);
                $cajainicial->update([
                    'caja_user_id' => $cajausuario->id,
                    'importe'      => $this->importe,
                    'user_id'      => auth()->user()->id
                ]);                
                $this->action = 1;             
            }
            if($this->selected_id) session()->flash('msg-ok', 'Caja Actualizada');            
            else session()->flash('msg-ok', 'Caja Habilitada');
            
            DB::commit();               
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        return;
    }

    protected $listeners = [
        'deleteRow'=>'destroy',
        'createFromModal' => 'createFromModal'       
    ];  

    public function createFromModal($info)
    {
        $data = json_decode($info);
        $descripcion = ucwords($data->descripcion);
        DB::begintransaction();
        try{ 
            $existe = Caja::where('descripcion', $descripcion)
                ->where('comercio_id', $this->comercioId)->get();
            if($existe->count() > 0) {
                session()->flash('info', 'La Caja ya existe...');
                $this->resetInput();
                return;
            }else{
                Caja::create([
                    'descripcion' => $descripcion,
                    'comercio_id' => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Caja creada exitosamente!!!');
            } 
            DB::commit();               
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se creó...');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $cajaUsuario = CajaUsuario::find($id)->delete();

                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla'           => 'Caja',
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
