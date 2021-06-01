<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Localidad;
use App\Models\Producto;
use App\Models\Provincia;
use App\Models\Vianda;
use App\Models\CajaUsuario;
use DB;

class ClienteController extends Component
{
    public $nombre, $apellido, $documento, $calle, $numero, $localidad = 'Elegir', $provincia = 'Elegir';
    public $telefono, $vianda = '0', $viandaSi, $viandaNo, $comercioId; 
    public $selected_id, $search, $action = 1, $caja_abierta; 
    public $nomCliV, $apeCliV, $producto = 'Elegir', $comentarios;
    public $h_lunes_m, $h_lunes_n, $h_martes_m, $h_martes_n, $h_miercoles_m, $h_miercoles_n, $h_jueves_m, $h_jueves_n;
    public $h_viernes_m, $h_viernes_n, $h_sabado_m, $h_sabado_n, $h_domingo_m, $h_domingo_n;
    public $c_lunes_m, $c_lunes_n, $c_martes_m, $c_martes_n, $c_miercoles_m, $c_miercoles_n, $c_jueves_m, $c_jueves_n;
    public $c_viernes_m, $c_viernes_n, $c_sabado_m, $c_sabado_n, $c_domingo_m, $c_domingo_n;
    public $recuperar_registro = 0, $descripcion_soft_deleted, $id_soft_deleted;

    public function render()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');

        //vemos si tenemos una caja habilitada con nuestro user_id
        $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', auth()->user()->id)
            ->where('caja_usuarios.estado', '1')->select('caja_usuarios.*')->get();
        $this->caja_abierta = $caja_abierta->count();
        if($caja_abierta->count() > 0){
            $this->nro_arqueo = $caja_abierta[0]->id; 
            $this->fecha_inicio = $caja_abierta[0]->created_at;  
        }

        $productos = Producto::select()->where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        $localidades = Localidad::where('comercio_id', $this->comercioId)->select()->orderBy('descripcion')->get();
        $provincias = Provincia::all();

        if(strlen($this->search) > 0){
            $info = Cliente::join('localidades as loc', 'loc.id', 'clientes.localidad_id')
                ->where('nombre', 'like', '%' .  $this->search . '%')
                ->where('clientes.comercio_id', $this->comercioId)
                ->orWhere('apellido', 'like', '%' .  $this->search . '%')
                ->where('clientes.comercio_id', $this->comercioId)
                ->orWhere('calle', 'like', '%' .  $this->search . '%')
                ->where('clientes.comercio_id', $this->comercioId)
                ->orWhere('loc.descripcion', 'like', '%' .  $this->search . '%')
                ->where('clientes.comercio_id', $this->comercioId)
                ->select('clientes.*', 'loc.descripcion as localidad')
                ->orderBy('apellido', 'asc')->get();
        }else {
            $info = Cliente::join('localidades as loc', 'loc.id', 'clientes.localidad_id')
                ->where('clientes.comercio_id', $this->comercioId)
                ->orderBy('apellido', 'asc')
                ->select('clientes.*', 'loc.descripcion as localidad')->get();
        }            
        return view('livewire.clientes.component', [
            'info'        =>$info,
            'localidades' => $localidades,
            'provincias'  => $provincias,
            'productos'  => $productos
        ]);
    }

    public function verViandas($id, $action)
    {
        $this->action = $action;
        $this->selected_id = $id;

        $record = Cliente::findOrFail($id);    
        $this->nomCliV = $record->nombre;
        $this->apeCliV = $record->apellido;

        $viandas = Vianda::join('productos as p', 'p.id', 'viandas.producto_id')
            ->where('cliente_id', $id)
            ->select('viandas.*', 'p.id as producto_id')->get();

        if($viandas->count() > 0){
            $this->producto = $viandas[0]->producto_id;
            $this->comentarios = $viandas[0]->comentarios;
            $this->h_lunes_m = $viandas[0]->h_lunes_m;   
            $this->h_lunes_n = $viandas[0]->h_lunes_n;   
            $this->h_martes_m = $viandas[0]->h_martes_m;   
            $this->h_martes_n = $viandas[0]->h_martes_n;   
            $this->h_miercoles_m = $viandas[0]->h_miercoles_m;   
            $this->h_miercoles_n = $viandas[0]->h_miercoles_n;   
            $this->h_jueves_m = $viandas[0]->h_jueves_m;   
            $this->h_jueves_n = $viandas[0]->h_jueves_n;   
            $this->h_viernes_m = $viandas[0]->h_viernes_m;   
            $this->h_viernes_n = $viandas[0]->h_viernes_n;   
            $this->h_sabado_m = $viandas[0]->h_sabado_m;   
            $this->h_sabado_n = $viandas[0]->h_sabado_n;   
            $this->h_domingo_m = $viandas[0]->h_domingo_m;   
            $this->h_domingo_n = $viandas[0]->h_domingo_n;
            $this->c_lunes_m = $viandas[0]->c_lunes_m;   
            $this->c_lunes_n = $viandas[0]->c_lunes_n;   
            $this->c_martes_m = $viandas[0]->c_martes_m;   
            $this->c_martes_n = $viandas[0]->c_martes_n;   
            $this->c_miercoles_m = $viandas[0]->c_miercoles_m;   
            $this->c_miercoles_n = $viandas[0]->c_miercoles_n;   
            $this->c_jueves_m = $viandas[0]->c_jueves_m;   
            $this->c_jueves_n = $viandas[0]->c_jueves_n;   
            $this->c_viernes_m = $viandas[0]->c_viernes_m;   
            $this->c_viernes_n = $viandas[0]->c_viernes_n;   
            $this->c_sabado_m = $viandas[0]->c_sabado_m;   
            $this->c_sabado_n = $viandas[0]->c_sabado_n;   
            $this->c_domingo_m = $viandas[0]->c_domingo_m;   
            $this->c_domingo_n = $viandas[0]->c_domingo_n;
        }       
    }
        //activa la vista edición o creación
    public function doAction($action)
    {
        $this->action = $action;
        $this->resetInput();
    }

        //método para reiniciar variables
    private function resetInput()
    {
        $this->nombre = '';
        $this->apellido = '';
        $this->documento = '';
        $this->calle = '';
        $this->numero = '';
        $this->localidad = 'Elegir';
        $this->provincia = 'Elegir';
        $this->producto = 'Elegir';
        $this->telefono = '';
        $this->vianda = '0';
        $this->selected_id = null;       
        $this->search = '';

        $this->comentarios = '';
        $this->h_lunes_m = '';   
        $this->h_lunes_n = '';   
        $this->h_martes_m = '';   
        $this->h_martes_n = '';   
        $this->h_miercoles_m = '';   
        $this->h_miercoles_n = '';   
        $this->h_jueves_m = '';   
        $this->h_jueves_n = '';   
        $this->h_viernes_m = '';   
        $this->h_viernes_n = '';   
        $this->h_sabado_m = '';   
        $this->h_sabado_n = '';   
        $this->h_domingo_m = '';   
        $this->h_domingo_n = '';
        $this->c_lunes_m = '';   
        $this->c_lunes_n = '';   
        $this->c_martes_m = '';   
        $this->c_martes_n = '';   
        $this->c_miercoles_m = '';   
        $this->c_miercoles_n = '';   
        $this->c_jueves_m = '';   
        $this->c_jueves_n = '';   
        $this->c_viernes_m = '';   
        $this->c_viernes_n = '';   
        $this->c_sabado_m = '';   
        $this->c_sabado_n = '';   
        $this->c_domingo_m = '';   
        $this->c_domingo_n = '';
    }

    public function edit($id)
    {
        $record = Cliente::findOrFail($id);
        $this->selected_id = $id;
        $this->nombre = $record->nombre;
        $this->apellido = $record->apellido;
        $this->documento = $record->documento;
        $this->calle = $record->calle;
        $this->numero = $record->numero;
        $this->localidad = $record->localidad_id;
        $this->telefono = $record->telefono;
        $this->vianda = $record->vianda;

        $this->action = 2;
    }
    
    public function volver()
    {
        $this->recuperar_registro = 0;
        $this->resetInput();
        return; 
    }

    public function RecuperarRegistro($id)
    {
        DB::begintransaction();
        try{
            Cliente::onlyTrashed()->find($id)->restore();
            session()->flash('msg-ok', 'Registro recuperado');
            $this->volver();
            
            DB::commit();               
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se recuperó...');
        }
    }

    public function StoreOrUpdate()
    {
        $this->validate([
            'vianda' => 'not_in:0',
            'localidad' => 'not_in:Elegir'
        ]);     
        $this->validate([
            'nombre' => 'required', 
            'apellido' => 'required',
            'calle' => 'required'
        ]);
        if($this->numero == '') $this->numero = 's/n';
        
        DB::begintransaction();
        try{
            if($this->selected_id > 0) {
                $existe = Cliente::where('nombre', $this->nombre)
                    ->where('apellido', $this->apellido)
                    ->where('calle', $this->calle)
                    ->where('numero', $this->numero)
                    ->where('localidad_id', $this->localidad)
                    ->where('comercio_id', $this->comercioId)
                    ->where('id', '<>', $this->selected_id)
                    ->select('*')
                    ->withTrashed()->get();
                if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                    session()->flash('info', 'El Cliente que desea modificar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->apellido . ' ' . $existe[0]->nombre;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif( $existe->count() > 0) {
                    session()->flash('info', 'El Cliente ya existe...e');
                    $this->resetInput();
                    return;
                }
            }else {
                $existe = Cliente::where('nombre', $this->nombre)
                    ->where('apellido', $this->apellido)
                    ->where('calle', $this->calle)
                    ->where('numero', $this->numero)
                    ->where('localidad_id', $this->localidad)
                    ->where('comercio_id', $this->comercioId)
                    ->select('*')->withTrashed()->get();

                if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                    session()->flash('info', 'El Cliente que desea agregar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->apellido . ' ' . $existe[0]->nombre;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count() > 0 ) {
                    session()->flash('info', 'El Cliente ya existe...');
                    $this->resetInput();
                    return;
                }
            }        
            if($this->selected_id <= 0) {
                Cliente::create([
                    'nombre' => strtoupper($this->nombre),            
                    'apellido' => strtoupper($this->apellido),     
                    'calle' => ucwords($this->calle),            
                    'numero' => $this->numero,            
                    'localidad_id' => $this->localidad,            
                    'telefono' => $this->telefono,
                    'vianda' => $this->vianda,
                    'comercio_id' => $this->comercioId            
                ]);
            }else {   
                $record = Cliente::find($this->selected_id);
                $record->update([
                    'nombre' => strtoupper($this->nombre),            
                    'apellido' => strtoupper($this->apellido),     
                    'calle' => ucwords($this->calle),            
                    'numero' => $this->numero,            
                    'localidad_id' => $this->localidad,            
                    'telefono' => $this->telefono,
                    'vianda' => $this->vianda
                ]); 
                $this->action = 1;             
            }
            if($this->selected_id) session()->flash('msg-ok', 'Cliente Actualizado');    
            else session()->flash('msg-ok', 'Cliente Creado'); 

            DB::commit();               
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        return;
    }
        
    public function grabarViandas()
    {
        $this->validate(['producto' => 'not_in:Elegir']);
            
        DB::begintransaction();                
        try{
            $existe = Vianda::where('cliente_id', $this->selected_id)->get();
            if($existe->count() > 0){
                   $existe->update([
                    'cliente_id' => $this->selected_id, 
                    'producto_id' => $this->producto,
                    'estado' => 'activo', 
                    'comentarios' => $this->comentarios, 
                    'h_lunes_m' => $this->h_lunes_m, 
                    'h_lunes_n' => $this->h_lunes_n, 
                    'h_martes_m' => $this->h_martes_m, 
                    'h_martes_n' => $this->h_martes_n, 
                    'h_miercoles_m' => $this->h_miercoles_m, 
                    'h_miercoles_n' => $this->h_miercoles_n, 
                    'h_jueves_m' => $this->h_jueves_m, 
                    'h_jueves_n' => $this->h_jueves_n, 
                    'h_viernes_m' => $this->h_viernes_m, 
                    'h_viernes_n' => $this->h_viernes_n, 
                    'h_sabado_m' => $this->h_sabado_m, 
                    'h_sabado_n' => $this->h_sabado_n, 
                    'h_domingo_m' => $this->h_domingo_m, 
                    'h_domingo_n' => $this->h_domingo_n, 
                    'c_lunes_m' => $this->c_lunes_m, 
                    'c_lunes_n' => $this->c_lunes_n, 
                    'c_martes_m' => $this->c_martes_m, 
                    'c_martes_n' => $this->c_martes_n, 
                    'c_miercoles_m' => $this->c_miercoles_m, 
                    'c_miercoles_n' => $this->c_miercoles_n, 
                    'c_jueves_m' => $this->c_jueves_m, 
                    'c_jueves_n' => $this->c_jueves_n, 
                    'c_viernes_m' => $this->c_viernes_m, 
                    'c_viernes_n' => $this->c_viernes_n, 
                    'c_sabado_m' => $this->c_sabado_m, 
                    'c_sabado_n' => $this->c_sabado_n, 
                    'c_domingo_m' => $this->c_domingo_m, 
                    'c_domingo_n' => $this->c_domingo_n 
                ]); 
            }else {
                Vianda::create([
                    'cliente_id' => $this->selected_id, 
                    'producto_id' => $this->producto,
                    'estado' => 'activo', 
                    'comentarios' => $this->comentarios, 
                    'h_lunes_m' => $this->h_lunes_m, 
                    'h_lunes_n' => $this->h_lunes_n, 
                    'h_martes_m' => $this->h_martes_m, 
                    'h_martes_n' => $this->h_martes_n, 
                    'h_miercoles_m' => $this->h_miercoles_m, 
                    'h_miercoles_n' => $this->h_miercoles_n, 
                    'h_jueves_m' => $this->h_jueves_m, 
                    'h_jueves_n' => $this->h_jueves_n, 
                    'h_viernes_m' => $this->h_viernes_m, 
                    'h_viernes_n' => $this->h_viernes_n, 
                    'h_sabado_m' => $this->h_sabado_m, 
                    'h_sabado_n' => $this->h_sabado_n, 
                    'h_domingo_m' => $this->h_domingo_m, 
                    'h_domingo_n' => $this->h_domingo_n, 
                    'c_lunes_m' => $this->c_lunes_m, 
                    'c_lunes_n' => $this->c_lunes_n, 
                    'c_martes_m' => $this->c_martes_m, 
                    'c_martes_n' => $this->c_martes_n, 
                    'c_miercoles_m' => $this->c_miercoles_m, 
                    'c_miercoles_n' => $this->c_miercoles_n, 
                    'c_jueves_m' => $this->c_jueves_m, 
                    'c_jueves_n' => $this->c_jueves_n, 
                    'c_viernes_m' => $this->c_viernes_m, 
                    'c_viernes_n' => $this->c_viernes_n, 
                    'c_sabado_m' => $this->c_sabado_m, 
                    'c_sabado_n' => $this->c_sabado_n, 
                    'c_domingo_m' => $this->c_domingo_m, 
                    'c_domingo_n' => $this->c_domingo_n 
                ]);        
            }
            if($existe->count() > 0) session()->flash('msg-ok', 'Detalle de Viandas actualizado exitosamente!!');            
            else session()->flash('msg-ok', 'Detalle de Viandas creado exitosamente!!');  
            
            DB::commit();
        }catch (Exception $e){
            DB::rollback();     
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        $this->action = 1;
    }
        
    protected $listeners = [
        'deleteRow'=>'destroy',
        'createFromModal' => 'createFromModal'       
    ];  
    
    public function destroy($id)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $cliente = Cliente::find($id)->delete();
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

    public function createFromModal($info)
    {
        $data = json_decode($info);

        $existe = Localidad::where('descripcion', ucwords($data->localidad))
            ->where('provincia_id', $data->provincia_id)
            ->where('comercio_id', $this->comercioId)->get();  
        if($existe->count() > 0 ) {
            session()->flash('info', 'La Localidad ingresada ya existe!!!');
            return;
        }else{
            DB::begintransaction();
            try{   
                Localidad::create([
                    'descripcion' => ucwords($data->localidad),
                    'provincia_id' => $data->provincia_id,
                    'comercio_id' => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Localidad creada exitosamente!!!'); 
                DB::commit();               
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se creó...');
            }
        }
    }
}
    