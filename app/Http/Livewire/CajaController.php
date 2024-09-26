<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\ArqueoGral;
use App\Models\Auditoria;
use App\Models\Banco;
use App\Models\Caja;
use App\Models\CajaInicial;
use App\Models\CajaUsuario;
use App\Models\Cheque;
use App\Models\ModelHasRole;
use DB;

class CajaController extends Component
{	
    public $descripcion, $action = 1, $caja = 'Elegir', $usuario = 'Elegir';            
    public $selected_id, $caja_usuario, $estado = '0', $importe;
    public $edit = 0, $tipo = 1, $search, $searchBy = '1', $bancos; 
    public $recuperar_registro = 0, $descripcion_soft_deleted, $id_soft_deleted, $comentario = '';
    public $comercioId, $arqueoGralId, $estadoArqueoGral, $usuario_habilitado = 1;
    public $cantidad, $infoIdCheque, $infoImporteCheque, $totalChequesSeleccionados;

    public function render()
    {
        //busca el comercio que está en sesión y el id del ArqueoGral
        $this->comercioId = session('idComercio');
        $this->arqueoGralId = session('idArqueoGral');
        $this->estadoArqueoGral = session('estadoArqueoGral');
        session(['facturaPendiente' => null]);
  
         //si ya hay un arqueo cerrado con la misma fecha
        if($this->estadoArqueoGral == 'ya existe') return view('livewire.admin.mensajes.ya_existe_arqueo');

        if($this->arqueoGralId > 0) {    //si hay un arqueo abierto o pendiente
            //primero verifico si el usuario logueado es el Administrador del Sistema, en tal caso
            //no hago ninguna validación y le permito hacer cualquier procedimiento
            $usuadrioAdmin = ModelHasRole::join('roles as r', 'r.id', 'model_has_roles.role_id')
                ->join('users as u', 'u.id', 'model_has_roles.model_id')
                ->where('r.alias', 'Administrador')
                ->where('r.comercio_id', $this->comercioId)->select('u.id')->get();
            if($usuadrioAdmin[0]->id <> auth()->user()->id){
                //si no es el Admin, verifico si el usuario logueado es quien inició el Arqueo Gral,
                //si no lo es, muestro un mensaje y no lo dejo continuar
                //si no hay arqueo abierto, lo habilito para habilitar Cajas
                $usuarioArqueo = CajaUsuario::join('arqueo_grals as ag', 'ag.id', 'caja_usuarios.arqueo_gral_id')
                    ->where('caja_usuarios.user_id', auth()->user()->id)
                    ->where('caja_usuarios.arqueo_gral_id', $this->arqueoGralId)
                    ->where('ag.estado', '1')->get();
                if(!$usuarioArqueo->count()) $this->usuario_habilitado = 0;
            }else{
                if($this->estadoArqueoGral == 'pendiente') return view('arqueogral');
            }
        }
        // si es el usuario que corresponde y
        // si el valor de 'estadoArqueoGral' es 'pendiente', lo obligo a hacer el Arqueo Gral.
        // si es 'activo', todo sigue normal
        // y si es 'no existe', se creará un nuevo arqueo al habilitar la primera caja del día
        // SALVO que se trate del mismo día que cubre el último arqueo gral.
        // en este caso no se podrá iniciar nada hasta que culmine el horario de cobertura de 
        // dicho arqueo gral., que por defecto es a las 08:00 am.       

        $cajas = Caja::where('comercio_id', $this->comercioId)->orderBy('descripcion')->get();
        $this->bancos = Banco::all()->where('comercio_id', $this->comercioId);

        //selecciona solo los usuarios que su rol le permite poseer una Caja
        $usuarios = ModelHasRole::join('users as u', 'u.id', 'model_has_roles.model_id')
            ->join('roles as r', 'r.id', 'model_has_roles.role_id')
            ->where('r.comercio_id', $this->comercioId)
            ->where('r.admite_caja', '1')
            ->select('u.id', 'u.name', 'u.apellido')->get();

        $info = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
            ->join('users as u', 'u.id', 'caja_usuarios.caja_usuario_id')
            ->where('c.comercio_id', $this->comercioId)
            ->where('caja_usuarios.estado', '1')
            ->select('caja_usuarios.*', 'c.descripcion', 'u.name', 'u.apellido', 
                DB::RAW("'' as apeNomCajaHab"), DB::RAW("'' as importeCaja"))
            ->orderby('c.descripcion')->get();
        foreach($info as $i){   //traemos el nombre del usuario habilitante de cada caja
            $info2 = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
                ->join('users as u', 'u.id', 'caja_usuarios.user_id')
                ->where('caja_usuarios.id', $i->id)
                ->select('u.name', 'u.apellido')->get();
            $i->apeNomCajaHab = $info2[0]->apellido . ' ' . $info2[0]->name;            
        }
        foreach($info as $i){   //traemos los importes totales de inicio de cada caja
            $info2 = CajaInicial::where('caja_user_id', $i->id)
                ->sum('importe');
            $i->importeCaja = $info2;            
        }

        $infoImportesCaja = CajaInicial::join('caja_usuarios as cu', 'cu.id', 'caja_inicials.caja_user_id')
            ->join('usuario_comercio as uc', 'uc.id', 'caja_inicials.user_id')
            ->where('uc.comercio_id', $this->comercioId)
            ->where('cu.estado', '1')
            ->where('caja_inicials.caja_user_id', $this->selected_id)
            ->select('caja_inicials.*', DB::RAW("'' as tipoIngreso"))->get();
        if ($infoImportesCaja->count()) {
            foreach ($infoImportesCaja as $i) { 
                $i->tipoIngreso = 'Efectivo';               
                if ($i->tipo == '2') {
                    $infoNumCheque = Cheque::find($i->cheque_id);
                    if ($infoNumCheque) {
                        $i->tipoIngreso = 'Cheque N° ' . $infoNumCheque->numero;
                    }
                }
            }
        }

        if($this->search){
            if($this->searchBy == '1'){
                $infoCheques = Cheque::join('bancos as b','b.id', 'cheques.banco_id')
                ->where('cheques.comercio_id', $this->comercioId)
                ->where('cheques.estado', 'en_cartera')
                ->where('b.descripcion', 'like', '%'. $this->search . '%')
                ->orWhere('cheques.comercio_id', $this->comercioId)
                ->where('cheques.estado', 'en_cartera')
                ->where('b.sucursal', 'like', '%'. $this->search . '%')
                ->select('b.descripcion', 'b.sucursal', 'cheques.id', 'cheques.numero', 
                        'cheques.fecha_de_pago', 'cheques.importe', DB::RAW("'' as banco"))
                ->orderBy('cheques.fecha_de_pago')->get();
            }elseif ($this->searchBy == '2') {
                $infoCheques = Cheque::join('bancos as b','b.id', 'cheques.banco_id')
                ->where('cheques.comercio_id', $this->comercioId)
                ->where('cheques.estado', 'en_cartera')
                ->where('b.descripcion', 'like', '%'. $this->search . '%')
                ->orWhere('cheques.comercio_id', $this->comercioId)
                ->where('cheques.estado', 'en_cartera')
                ->where('b.sucursal', 'like', '%'. $this->search . '%')
                ->select('b.descripcion', 'b.sucursal', 'cheques.id', 'cheques.numero', 
                        'cheques.fecha_de_pago', 'cheques.importe', DB::RAW("'' as banco"))
                ->orderBy('cheques.fecha_de_pago', 'desc')->get();
            }else {
                $infoCheques = Cheque::join('bancos as b','b.id', 'cheques.banco_id')
                ->where('cheques.comercio_id', $this->comercioId)
                ->where('cheques.estado', 'en_cartera')
                ->where('b.descripcion', 'like', '%'. $this->search . '%')
                ->orWhere('cheques.comercio_id', $this->comercioId)
                ->where('cheques.estado', 'en_cartera')
                ->where('b.sucursal', 'like', '%'. $this->search . '%')
                ->select('b.descripcion', 'b.sucursal', 'cheques.id', 'cheques.numero', 
                        'cheques.fecha_de_pago', 'cheques.importe', DB::RAW("'' as banco"))
                ->orderBy('cheques.importe')->get();
            }
            
        }else{
            if($this->searchBy == '1'){
                $infoCheques = Cheque::join('bancos as b','b.id', 'cheques.banco_id')
                    ->where('cheques.comercio_id', $this->comercioId)
                    ->where('cheques.estado', 'en_cartera')
                    ->select('b.descripcion', 'b.sucursal', 'cheques.id', 'cheques.numero', 
                            'cheques.fecha_de_pago', 'cheques.importe', DB::RAW("'' as banco"))
                    ->orderBy('cheques.fecha_de_pago')->get();
            }elseif($this->searchBy == '2'){
                $infoCheques = Cheque::join('bancos as b','b.id', 'cheques.banco_id')
                ->where('cheques.comercio_id', $this->comercioId)
                ->where('cheques.estado', 'en_cartera')
                ->select('b.descripcion', 'b.sucursal', 'cheques.id', 'cheques.numero', 
                        'cheques.fecha_de_pago', 'cheques.importe', DB::RAW("'' as banco"))
                ->orderBy('cheques.fecha_de_pago', 'desc')->get();
            }else{
                $infoCheques = Cheque::join('bancos as b','b.id', 'cheques.banco_id')
                ->where('cheques.comercio_id', $this->comercioId)
                ->where('cheques.estado', 'en_cartera')
                ->select('b.descripcion', 'b.sucursal', 'cheques.id', 'cheques.numero', 
                        'cheques.fecha_de_pago', 'cheques.importe', DB::RAW("'' as banco"))
                ->orderBy('cheques.importe')->get();
            } 
        }
        
        if($infoCheques->count()){
            foreach ($infoCheques as $i) {
                $i->banco = $i->descripcion . '/' . $i->sucursal;
            }
        }

        return view('livewire.cajausuario.component', [
            'info'             => $info,
            'cajas'            => $cajas,
            'usuarios'         => $usuarios,
            'infoImportesCaja' => $infoImportesCaja,
            'infoCheques'      => $infoCheques
        ]);
    }
    protected $listeners = [
        'createFromModal'    => 'createFromModal',       
        'editFromModal'      => 'editFromModal',
        'doAction'           => 'doAction',
        'agregarBanco'       => 'agregarBanco',
        'chequeSeleccionado' => 'chequeSeleccionado',
        'deleteRow'          => 'destroy'
    ];
    public function doAction($action)
    {
        if($action == 1){
            $this->resetInput();
        }elseif ($action == 4) {
            $this->infoIdCheque = null;
            $this->importe = '';
        }
        if($action != 4) $this->action = $action;
    }
    private function resetInput()
    {
        $this->descripcion = '';
        $this->selected_id = null; 
        $this->caja        = 'Elegir';
        $this->usuario     = 'Elegir';
        $this->estado      = '0';
        $this->importe     = '';
        $this->edit        = 0;
        $this->action      = 1;
        $this->tipo        = 1;
        $this->search      = null;
        $this->searchBy    = '1';
        $this->infoIdCheque              = null; 
        $this->infoImporteCheque         = null;
        $this->totalChequesSeleccionados = null;
    }
    public function edit($id, $edit)
    {
        $this->edit = $edit;    //$edit indica '0' si se crea, '1' si edita o '2' si se agraga importe

        $record = CajaUsuario::join('caja_inicials as ci', 'ci.caja_user_id', 'caja_usuarios.id')
            ->where('caja_usuarios.id', $id)->select('caja_usuarios.*', 'ci.importe')->first();         
        $this->selected_id = $id;
        $this->caja        = $record->caja_id;
        $this->usuario     = $record->caja_usuario_id;
        if($this->edit != 2) $this->importe = $record->importe;
        $this->estado = $record->estado;
        
        $this->action = 2;
    }
    public function StoreOrUpdate()
    { 
        $this->validate([
            'caja'    => 'not_in:Elegir',
            'usuario' => 'not_in:Elegir',
            'importe' => 'required'
        ]);  

        DB::begintransaction();
        try{
            if($this->selected_id > 0) { //si se quiere modificar una habilitación
                //verificamos si la caja no está asignada previamente a otro usuario
                $existe = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
                    ->where('c.comercio_id', $this->comercioId)
                    ->where('caja_usuarios.caja_id', $this->caja)
                    ->where('caja_usuarios.estado', '1')
                    ->where('caja_usuarios.id', '<>', $this->selected_id)->get();
                if($existe->count()) {
                    session()->flash('msg-error', 'La Caja ya está asignada a otro Operador...');
                    $this->resetInput();
                    return;
                }
                //verificamos si el usuario no está asignado previamente a otra caja
                $existe = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
                    ->where('c.comercio_id', $this->comercioId)
                    ->where('caja_usuarios.caja_usuario_id', $this->usuario)
                    ->where('caja_usuarios.estado', '1')
                    ->where('caja_usuarios.id', '<>', $this->selected_id)->get();
                if($existe->count()) {
                    session()->flash('msg-error', 'El Operador ya está asignado a otra Caja...');
                    $this->resetInput();
                    return;
                }
            }else { // si se quiere agregar una habilitación
                    //verificamos si la Caja no está asignada previamente a otro usuario
                $existe = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
                    ->where('c.comercio_id', $this->comercioId)
                    ->where('caja_usuarios.caja_id', $this->caja)
                    ->where('caja_usuarios.estado', '1');
                if($existe->count() > 0) {
                    session()->flash('msg-error', 'La Caja ya está asignada a otro Operador...');
                    $this->resetInput();
                    return;
                }      //verificamos si el Operador no está asignado previamente a otra caja
                $existe = CajaUsuario::join('cajas as c', 'c.id', 'caja_usuarios.caja_id')
                    ->where('c.comercio_id', $this->comercioId)
                    ->where('caja_usuarios.caja_usuario_id', $this->usuario)
                    ->where('caja_usuarios.estado', '1');
                if($existe->count() > 0) {
                    session()->flash('msg-error', 'El Operador ya está asignado a otra Caja...');
                    $this->resetInput();
                    return;
                }
            }

            if($this->edit == 0) {                  //nueva Caja  
                if($this->arqueoGralId == -1){      //nuevo Arqueo Gral.
                    $idArqueoGral = ArqueoGral::create([
                        'estado'      => '1',
                        'comercio_id' => $this->comercioId
                    ]);
                    session(['idArqueoGral' => $idArqueoGral->id]);
                    $this->arqueoGralId = session('idArqueoGral');
                    session(['estadoArqueoGral' => 'activo']);
                }
                $cajausuario =  CajaUsuario::create([
                    'caja_id'         => $this->caja,
                    'caja_usuario_id' => $this->usuario,
                    'estado'          => '1',
                    'user_id'         => auth()->user()->id,
                    'arqueo_gral_id'  => $this->arqueoGralId
                ]);
                if($this->infoIdCheque){
                    $contadorId = 0;
                    foreach ($this->infoIdCheque as $i) {
                        $contadorImporte = 0;
                        foreach ($this->infoImporteCheque as $j) {
                            if($contadorImporte == $contadorId) $importe = $j;
                            $contadorImporte++;
                        }
                        $cajainicial = CajaInicial::create([
                            'caja_user_id' => $cajausuario->id,
                            'tipo'         => $this->tipo,
                            'cheque_id'    => $i,
                            'importe'      => $importe,
                            'user_id'      => auth()->user()->id
                        ]);

                        $cheque = Cheque::find($i);
                        $cheque->update(['estado' => 'en_caja']);

                        $contadorId++;
                    }
                }else{
                    $cajainicial = CajaInicial::create([
                        'caja_user_id' => $cajausuario->id,
                        'tipo'         => $this->tipo,
                        'importe'      => $this->importe,
                        'user_id'      => auth()->user()->id
                    ]); 
                }
                              
            }elseif($this->edit == 1) {                //editar Caja
                $cajausuario = CajaUsuario::find($this->selected_id);  //edita encabezado
                $cajausuario->update([
                    'caja_id'         => $this->caja,
                    'caja_usuario_id' => $this->usuario,
                    'estado'          => '1',
                    'user_id'         => auth()->user()->id
                ]);                
            }else {                                  //agregar Importe a Caja
                if($this->infoIdCheque){
                    $contadorId = 0;
                    foreach ($this->infoIdCheque as $i) {
                        $contadorImporte = 0;
                        foreach ($this->infoImporteCheque as $j) {
                            if($contadorImporte == $contadorId) $importe = $j;
                            $contadorImporte++;
                        }
                        $cajainicial = CajaInicial::create([
                            'caja_user_id' => $this->selected_id,
                            'tipo'         => $this->tipo,
                            'cheque_id'    => $i,
                            'importe'      => $importe,
                            'user_id'      => auth()->user()->id
                        ]);

                        $cheque = Cheque::find($i);
                        $cheque->update(['estado' => 'en_caja']);

                        $contadorId++;
                    }
                }else{
                    $cajainicial = CajaInicial::create([
                        'caja_user_id' => $this->selected_id,
                        'tipo'         => $this->tipo,
                        'importe'      => $this->importe,
                        'user_id'      => auth()->user()->id
                    ]);
                }      
            }
            if($this->edit == 0) session()->flash('msg-ok', 'Caja Habilitada');            
            elseif ($this->edit == 1) session()->flash('msg-ok', 'Caja Actualizada');            
            else session()->flash('msg-ok', 'Importe Agregado');
            
            DB::commit(); 
                          
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->resetInput();
        return;
    }  
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
    public function editFromModal($info)
    {
        $data = json_decode($info);
        DB::begintransaction();
        try{ 
            $record = CajaInicial::find($data->id);
            $record->update([
                'importe' => $data->importe
            ]);  
            session()->flash('msg-ok', 'Importe editado exitosamente!!!');
            DB::commit();               
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se editó...');
        }
    }
    public function agregarBanco($data)
    {
        $info = json_decode($data);
        DB::begintransaction();                 
        try{
            $add_item = Banco::create([         
                'descripcion' => mb_strtoupper($info->banco),
                'sucursal'    => ucwords($info->sucursal),
                'comercio_id' => $this->comercioId
            ]);
            $this->bancos = $add_item->id;
            DB::commit();
            $this->emit('bancoCreado');  
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }  
        return;  
    }
    public function chequeSeleccionado($dataId, $dataImporte, $total, $cantidad)
    {
        $this->infoIdCheque = json_decode($dataId);
        $this->infoImporteCheque = json_decode($dataImporte);
        $this->totalChequesSeleccionados = $total;
        $this->importe = $total;
        $this->cantidad = $cantidad;

        $this->doAction(2);
    }
    public function destroy($chequeId, $id, $comentario)
    {                   
        // verificar si el cheque todavía está disponible en caja o si ya se entregó
        if ($chequeId) {        // como medio de pago, en cuyo caso no se podrá eliminar el registro
            $record = Cheque::find($chequeId);
            if($record->estado != 'entregado'){
                DB::begintransaction();
                try{
                    $caja_inicial = CajaInicial::find($id)->delete();
                    $audit = Auditoria::create([
                        'item_deleted_id' => $id,
                        'tabla' => 'Caja Inicial',
                        'user_delete_id' => auth()->user()->id,
                        'comentario' => $comentario,
                        'comercio_id' => $this->comercioId
                    ]);
                    $cheque = Cheque::find($chequeId);
                    $cheque->update(['estado' => 'en_cartera']);
                    DB::commit();  
                    $this->emit('registroEliminado');             
                }catch (Exception $e){
                    DB::rollback();
                    session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
                }    
            }else $this->emit('eliminarRegistro');
            return;
        }else{
            DB::begintransaction();
            try{
                $caja_inicial = CajaInicial::find($id)->delete();
                $audit = Auditoria::create([
                    'item_deleted_id' => $id,
                    'tabla' => 'Caja Inicial',
                    'user_delete_id' => auth()->user()->id,
                    'comentario' => $comentario,
                    'comercio_id' => $this->comercioId
                ]);
                DB::commit();  
                $this->emit('registroEliminado');             
            }catch (Exception $e){
                DB::rollback();
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se eliminó...');
            } 
            return;
        }
    }
}
