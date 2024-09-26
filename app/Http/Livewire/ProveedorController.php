<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\CategoriaGasto;
use App\Models\CondIva;
use App\Models\Localidad;
use App\Models\Proveedor;
use App\Models\Provincia;
use DB;

class ProveedorController extends Component
{
    public $nombre_empresa, $nombre_contacto, $apellido_contacto, $calle, $numero, $localidad = 'Elegir', $provincia = 'Elegir';
    public $tel_empresa, $tel_contacto, $comercioId, $condIvaId = 'Elegir', $cuit, $categoriaId = 'Elegir'; 
    public $selected_id = null, $search, $action = 1; 
    public $recuperar_registro = 0, $descripcion_soft_deleted, $id_soft_deleted;
   
    public function render()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        session(['facturaPendiente' => null]);  
   
        $localidades = Localidad::select()->where('localidades.comercio_id', $this->comercioId)->orderBy('descripcion','asc')->get();
        $provincias = Provincia::all();
        $condIva = CondIva::all();
        $categorias = CategoriaGasto::where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
           
        if(strlen($this->search) > 0)
        {
            $info = Proveedor::join('localidades as loc', 'loc.id', 'proveedores.localidad_id')
                ->join('cond_iva as cIva', 'cIva.id', 'proveedores.condiva_id')
                ->where('proveedores.nombre_empresa', 'like', '%' .  $this->search . '%')
                ->where('proveedores.comercio_id', $this->comercioId)
                ->orWhere('proveedores.nombre_contacto', 'like', '%' .  $this->search . '%')
                ->where('proveedores.comercio_id', $this->comercioId)
                ->orWhere('proveedores.cuit', 'like', $this->search . '%')
                ->where('proveedores.comercio_id', $this->comercioId)
                ->orWhere('proveedores.apellido_contacto', 'like', '%' .  $this->search . '%')
                ->where('proveedores.comercio_id', $this->comercioId)
                ->select('proveedores.*', 'loc.descripcion as localidad', 'cIva.descripcion as condiva')
                ->orderBy('proveedores.nombre_empresa')->get();
            return view('livewire.proveedores.component', [
                'info' =>$info,
                'localidades' => $localidades,
                'provincias' => $provincias,
                'condIva' => $condIva,
                'categorias' => $categorias
            ]);
        }
        else {
            return view('livewire.proveedores.component', [
                'info' => Proveedor::join('localidades as loc', 'loc.id', 'proveedores.localidad_id')
                    ->join('cond_iva as cIva', 'cIva.id', 'proveedores.condiva_id')
                    ->where('proveedores.comercio_id', $this->comercioId)
                    ->select('proveedores.*', 'loc.descripcion as localidad', 'cIva.descripcion as condiva')
                    ->orderBy('proveedores.nombre_empresa')->get(),
                'localidades' => $localidades,
                'provincias' => $provincias,
                'condIva' => $condIva,
                'categorias' => $categorias
            ]);
        }
        return view('livewire.proveedores.component');
    }

    protected $listeners = [
        'StoreOrUpdate'            => 'StoreOrUpdate',
        'deleteRow'                =>'destroy',
        'createFromModal'          => 'createFromModal',         
        'createIvaFromModal'       => 'createIvaFromModal',         
        'createCategoriaFromModal' => 'createCategoriaFromModal'         
    ];
    public function doAction($action)
    {
        $this->action = $action;
        $this->resetInput();
    }
   
    private function resetInput()
    {
        $this->nombre_empresa    = '';
        $this->tel_empresa       = '';
        $this->condIvaId         = 'Elegir';
        $this->cuit              = '';
        $this->calle             = '';
        $this->numero            = '';
        $this->localidad         = 'Elegir';
        $this->nombre_contacto   = '';
        $this->apellido_contacto = '';
        $this->tel_contacto      = '';
        $this->categoriaId       = 'Elegir';
        $this->selected_id       = null;   
        $this->search            = '';
    }   
   
    public function edit($id)
    {
        $record = Proveedor::findOrFail($id);
        $this->selected_id       = $id;
        $this->nombre_empresa    = $record->nombre_empresa;
        $this->tel_empresa       = $record->tel_empresa;
        $this->condIvaId         = $record->condiva_id;
        $this->cuit              = $record->cuit;
        $this->calle             = $record->calle;
        $this->numero            = $record->numero;
        $this->localidad         = $record->localidad_id;
        $this->nombre_contacto   = $record->nombre_contacto;
        $this->apellido_contacto = $record->apellido_contacto;
        $this->tel_contacto      = $record->tel_contacto;
        $this->categoriaId       = $record->categoria_id;

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
            Proveedor::onlyTrashed()->find($id)->restore();
            session()->flash('msg-ok', 'Registro recuperado');
            $this->volver();
            
            DB::commit();               
        }catch (\Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se recuperó...');
        }
    }

    public function validar_cuil($nCuit)
    {
        $nCuit = str_replace('-','',trim($nCuit));
        if (!is_numeric($nCuit)) return false;
        if (strlen($nCuit) != 11) return false;
        
        $factores = array(5,4,3,2,7,6,5,4,3,2);
        $sumatoria = 0;
        for ($i=0; $i < strlen($nCuit)-1; $i++) {
        $sumatoria += (substr($nCuit, $i, 1))*$factores[$i];
        }
        $resto = $sumatoria % 11;
        $digitoVerificador = ($resto != 0) ? (11 - $resto) : $resto;        
        return ($digitoVerificador == substr($nCuit, strlen($nCuit)-1));
    }
   
    public function StoreOrUpdate()
    {        
        $this->validate([
			'localidad'   => 'not_in:Elegir',
			'condIvaId'   => 'not_in:Elegir',
			'categoriaId' => 'not_in:Elegir'
		]); 

        $this->validate([
            'nombre_empresa' => 'required'
        ]);

        if($this->calle != '' && $this->numero == '') $this->numero = 's/n';  
        
        if($this->cuit != '' && !$this->validar_cuil($this->cuit)) {
            session()->flash('msg-ops', 'Error: CUIT inválido!!');
            return;
        }else {
            if($this->cuit != '') {
                $nCuit = $this->cuit;
                $nCuit = str_replace('-','',trim($nCuit));
                $tipo = substr($nCuit,0,2);
                $numero = substr($nCuit,2,8);
                $codVerificador = substr($nCuit,10,1);
                $this->cuit = $tipo . '-' . $numero . '-' . $codVerificador;
            }
            DB::begintransaction();
            try{
                if($this->selected_id > 0) {
                    if($this->cuit){
                        $existe = Proveedor::where('nombre_empresa', $this->nombre_empresa)
                        ->where('comercio_id', $this->comercioId)
                        ->where('id', '<>', $this->selected_id)
                        ->orWhere('cuit', $this->cuit)
                        ->where('comercio_id', $this->comercioId)
                        ->where('id', '<>', $this->selected_id)
                        ->select('*')
                        ->withTrashed()->get();
                    }else{
                        $existe = Proveedor::where('nombre_empresa', $this->nombre_empresa)
                        ->where('comercio_id', $this->comercioId)
                        ->where('id', '<>', $this->selected_id)
                        ->select('*')
                        ->withTrashed()->get();
                    }
                    
                    if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                        session()->flash('info', 'El Proveedor que desea modificar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
                        $this->action = 1;
                        $this->recuperar_registro = 1;
                        $this->descripcion_soft_deleted = $existe[0]->nombre_empresa;
                        $this->id_soft_deleted = $existe[0]->id;
                        return;
                    }elseif($existe->count()) {
                        session()->flash('info', 'El Proveedor ya existe...');
                        //$this->resetInput();
                        return;
                    }
                }else {
                    $existe = Proveedor::where('nombre_empresa', $this->nombre_empresa)
                        ->where('comercio_id', $this->comercioId)
                        ->orWhere('cuit', $this->cuit)
                        ->where('cuit', '<>', '')
                        ->where('comercio_id', $this->comercioId)
                        ->select('*')->withTrashed()->get();

                    if($existe->count() > 0 && $existe[0]->deleted_at != null) {
                        session()->flash('info', 'El Proveedor que desea agregar ya existe pero fué eliminado anteriormente, para recuperarlo haga click en el botón "Recuperar registro"');
                        $this->action = 1;
                        $this->recuperar_registro = 1;
                        $this->descripcion_soft_deleted = $existe[0]->nombre_empresa;
                        $this->id_soft_deleted = $existe[0]->id;
                        return;
                    }elseif($existe->count() > 0 ) {
                        session()->flash('info', 'El Proveedor ya existe...');
                        $this->resetInput();
                        return;
                    }   
                }   
                if($this->selected_id <= 0) {
                    Proveedor::create([
                        'nombre_empresa'    => mb_strtoupper($this->nombre_empresa),            
                        'tel_empresa'       => $this->tel_empresa,      
                        'condiva_id'        => $this->condIvaId,      
                        'cuit'              => $this->cuit,            
                        'calle'             => ucwords($this->calle),            
                        'numero'            => $this->numero,            
                        'localidad_id'      => $this->localidad,            
                        'nombre_contacto'   => mb_strtoupper($this->nombre_contacto),            
                        'apellido_contacto' => mb_strtoupper($this->apellido_contacto),            
                        'tel_contacto'      => $this->tel_contacto,      
                        'categoria_id'      => $this->categoriaId,      
                        'comercio_id'       => $this->comercioId            
                    ]);
                }else {   
                    $record = Proveedor::find($this->selected_id);
                    $record->update([
                        'nombre_empresa'    => mb_strtoupper($this->nombre_empresa),            
                        'tel_empresa'       => $this->tel_empresa,      
                        'condiva_id'        => $this->condIvaId,      
                        'cuit'              => $this->cuit,            
                        'calle'             => ucwords($this->calle),            
                        'numero'            => $this->numero,            
                        'localidad_id'      => $this->localidad,            
                        'nombre_contacto'   => mb_strtoupper($this->nombre_contacto),            
                        'apellido_contacto' => mb_strtoupper($this->apellido_contacto),            
                        'tel_contacto'      => $this->tel_contacto,
                        'categoria_id'      => $this->categoriaId
                    ]);
                    $this->action = 1;              
                }   
                if($this->selected_id) session()->flash('msg-ok', 'Proveedor Actualizado');            
                else session()->flash('msg-ok', 'Proveedor Creado');            
       
                DB::commit();               
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
            }
            $this->resetInput();
            return;
        }
    }
    
    public function destroy($id)
    {
        if ($id) {
            DB::begintransaction();
            try{
                $proveedor = Proveedor::find($id)->delete();
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
                    'descripcion'  => ucwords($data->localidad),
                    'provincia_id' => $data->provincia_id,
                    'comercio_id'  => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Localidad creada exitosamente!!!'); 
                DB::commit();               
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se creó...');
            }
        }
    }

    public function createIvaFromModal($info)
    {
        $data = json_decode($info);

        $existe = CondIva::where('descripcion', ucwords($data->descripcion));  
        if($existe->count() > 0 ) {
            session()->flash('info', 'La Condición de Iva ingresada ya existe!!!');
            return;
        }else{   
            DB::begintransaction();
            try{   
                CondIva::create([
                    'descripcion' => ucwords($data->descripcion)
                ]);
                session()->flash('msg-ok', 'Condición de Iva creada exitosamente!!!'); 
                DB::commit();               
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
            }  
        } 
    }  
    public function createCategoriaFromModal($info)
    {
        $data = json_decode($info);

        $existe = CategoriaGasto::where('descripcion', ucwords($data->descripcion))
            ->where('comercio_id', $this->comercioId)->get();  
        if($existe->count() > 0 ) {
            session()->flash('info', 'La Categoría de Gasto ingresada ya existe!!!');
            return;
        }else{   
            DB::begintransaction();
            try{   
                CategoriaGasto::create([
                    'descripcion' => ucwords($data->descripcion),
                    'tipo'        => $data->tipo,
                    'comercio_id' => $this->comercioId
                ]);
                session()->flash('msg-ok', 'Categoría de Gasto creada exitosamente!!!'); 
                DB::commit();               
            }catch (\Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
            }  
        } 
    }  
}
