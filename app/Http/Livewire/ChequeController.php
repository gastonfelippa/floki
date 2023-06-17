<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\Banco;
use App\Models\CajaInicial;
use App\Models\Cheque;
use App\Models\Cliente;
use App\Models\DetMetodoPago;
use Carbon\Carbon;
use DB;

class ChequeController extends Component
{
    public $descripcion, $sucursal, $descripcion_soft_deleted, $id_soft_deleted;
    public $selected_id, $search, $action = 1, $recuperar_registro = 0;  
    public $comercioId, $searchBy = '1', $bancos, $clientes;
    public $cantidad, $infoIdCheque, $infoImporteCheque, $totalChequesSeleccionados;
    public $numero, $fecha_de_emision, $fecha_de_pago, $importe, $cuitTitular;
    public $banco = 'Elegir', $cliente = 'Elegir', $estado = 'Elegir';

    public function render()
    {
        $this->comercioId = session('idComercio');

        $this->bancos = Banco::all()->where('comercio_id', $this->comercioId);
        $this->clientes = Cliente::all()->where('comercio_id', $this->comercioId);

        if($this->search){
            if($this->searchBy == '1'){
                $infoCheques = Cheque::join('bancos as b','b.id', 'cheques.banco_id')
                ->where('cheques.comercio_id', $this->comercioId)
                ->where('b.descripcion', 'like', '%'. $this->search . '%')
                ->orWhere('cheques.comercio_id', $this->comercioId)
                ->where('b.sucursal', 'like', '%'. $this->search . '%')
                ->orWhere('cheques.comercio_id', $this->comercioId)
                ->where('cheques.numero', 'like', '%'. $this->search . '%')
                ->select('b.descripcion', 'b.sucursal', 'cheques.id', 'cheques.numero', 
                        'cheques.fecha_de_pago', 'cheques.importe', 'cheques.estado',
                        DB::RAW("'' as banco"), DB::RAW("'' as estadoCheque"))
                ->orderBy('cheques.fecha_de_pago')->get();
            }elseif ($this->searchBy == '2') {
                $infoCheques = Cheque::join('bancos as b','b.id', 'cheques.banco_id')
                ->where('cheques.comercio_id', $this->comercioId)
                ->where('b.descripcion', 'like', '%'. $this->search . '%')
                ->orWhere('cheques.comercio_id', $this->comercioId)
                ->where('b.sucursal', 'like', '%'. $this->search . '%')
                ->orWhere('cheques.comercio_id', $this->comercioId)
                ->where('cheques.numero', 'like', '%'. $this->search . '%')
                ->select('b.descripcion', 'b.sucursal', 'cheques.id', 'cheques.numero', 
                        'cheques.fecha_de_pago', 'cheques.importe', 'cheques.estado',
                        DB::RAW("'' as banco"), DB::RAW("'' as estadoCheque"))
                ->orderBy('cheques.fecha_de_pago', 'desc')->get();
            }else {
                $infoCheques = Cheque::join('bancos as b','b.id', 'cheques.banco_id')
                ->where('cheques.comercio_id', $this->comercioId)
                ->where('cheques.estado', 'en_cartera')
                ->where('b.descripcion', 'like', '%'. $this->search . '%')
                ->orWhere('cheques.comercio_id', $this->comercioId)
                ->where('b.sucursal', 'like', '%'. $this->search . '%')
                ->orWhere('cheques.comercio_id', $this->comercioId)
                ->where('cheques.numero', 'like', '%'. $this->search . '%')
                ->select('b.descripcion', 'b.sucursal', 'cheques.id', 'cheques.numero', 
                        'cheques.fecha_de_pago', 'cheques.importe', 'cheques.estado',
                        DB::RAW("'' as banco"), DB::RAW("'' as estadoCheque"))
                ->orderBy('cheques.importe')->get();
            }
        }else{
            if($this->searchBy == '1'){
                $infoCheques = Cheque::join('bancos as b','b.id', 'cheques.banco_id')
                    ->where('cheques.comercio_id', $this->comercioId)
                    ->select('b.descripcion', 'b.sucursal', 'cheques.id', 'cheques.numero', 
                            'cheques.fecha_de_pago', 'cheques.importe', 'cheques.estado',
                            DB::RAW("'' as banco"), DB::RAW("'' as estadoCheque"))
                    ->orderBy('cheques.fecha_de_pago')->get();
            }elseif($this->searchBy == '2'){
                $infoCheques = Cheque::join('bancos as b','b.id', 'cheques.banco_id')
                ->where('cheques.comercio_id', $this->comercioId)
                ->select('b.descripcion', 'b.sucursal', 'cheques.id', 'cheques.numero', 
                        'cheques.fecha_de_pago', 'cheques.importe', 'cheques.estado',
                        DB::RAW("'' as banco"), DB::RAW("'' as estadoCheque"))
                ->orderBy('cheques.fecha_de_pago', 'desc')->get();
            }else{
                $infoCheques = Cheque::join('bancos as b','b.id', 'cheques.banco_id')
                ->where('cheques.comercio_id', $this->comercioId)
                ->select('b.descripcion', 'b.sucursal', 'cheques.id', 'cheques.numero', 
                        'cheques.fecha_de_pago', 'cheques.importe', 'cheques.estado',
                        DB::RAW("'' as banco"), DB::RAW("'' as estadoCheque"))
                ->orderBy('cheques.importe')->get();
            } 
        }
        
        if($infoCheques->count()){
            foreach ($infoCheques as $i) {
                $i->banco = $i->descripcion . '/' . $i->sucursal;
                switch ($i->estado) {
                    case 'en_cartera':
                        $i->estadoCheque = 'En Cartera';
                        break;
                    case 'en_caja':
                        $verCaja = CajaInicial::join('caja_usuarios as cu', 'cu.id', 'caja_inicials.caja_user_id')
                            ->join('cajas as c', 'c.id', 'cu.caja_id')
                            ->join('users as u', 'u.id', 'cu.caja_usuario_id')
                            ->where('caja_inicials.cheque_id', $i->id)
                            ->select('c.descripcion as caja', 'u.name as usuario')->first();
                        if(!$verCaja){
                            $verCaja = DetMetodoPago::join('caja_usuarios as cu', 'cu.id', 'det_metodo_pagos.arqueo_id')
                                ->join('cajas as c', 'c.id', 'cu.caja_id')
                                ->join('users as u', 'u.id', 'cu.caja_usuario_id')
                                ->where('num_comp_pago', $i->numero)
                                ->select('c.descripcion as caja', 'u.name as usuario')->first();
                        }
                        $i->estadoCheque = 'En ' . $verCaja->caja . ' de ' . $verCaja->usuario;
                        break;
                    case 'entregado':
                        $i->estadoCheque = 'Entregado';
                        break;
                    case 'rechazado':
                        $i->estadoCheque = 'Rechazado';
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }

        return view('livewire.cheques.component', [
            'infoCheques' => $infoCheques
        ]);
    }  

    protected $listeners = [
        'deleteRow'         => 'destroy',
        'agregarBanco'      => 'agregarBanco',
        'enviarDatosCheque' => 'agregarCheque'
    ];

    public function doAction($action)
    {
        $this->action = $action;
        $this->resetInput();
    }
    private function resetInput()
    {
        $this->descripcion      = '';
        $this->sucursal         = '';
        $this->selected_id      = null;    
        $this->search           = '';
        $this->searchBy         = '1';
        $this->cliente          = 'Elegir';
        $this->banco            = 'Elegir';
        $this->numero           = '';
        $this->importe          = '';
        $this->fecha_de_emision = '';
        $this->fecha_de_pago    = '';
        $this->cuitTitular      = '';
    }
    public function edit($id)
    {
        $this->action = 2;
        $record = Cheque::findOrFail($id);
        $this->selected_id      = $id;
        $this->banco            = $record->banco_id;
        $this->numero           = $record->numero;
        $this->importe          = $record->importe;
        $this->cuitTitular      = $record->cuit_titular;
        $this->cliente          = $record->cliente_id;
        $this->fecha_de_emision = Carbon::parse($record->fecha_de_emision)->format('d-m-Y');
        $this->fecha_de_pago    = Carbon::parse($record->fecha_de_pago)->format('d-m-Y');
        switch ($record->estado) {
            case 'en_cartera':
                $this->estado = 'En Cartera';
                break;
            case 'en_caja':
                $this->estado = 'En Caja';
                break;
            case 'entregado':
                $this->estado = 'Entregado';
                break;
            case 'rechazado':
                $this->estado = 'Rechazado';
                break;
            default:
                # code...
                break;
        }

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
            Banco::onlyTrashed()->find($id)->restore();
            $audit = Auditoria::create([
                'item_deleted_id' => $id,
                'tabla'           => 'Bancos',
                'estado'          => '1',
                'user_delete_id'  => auth()->user()->id,
                'comentario'      => '',
                'comercio_id'     => $this->comercioId
            ]);
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
            'banco'   => 'not_in:Elegir',
            'cliente' => 'not_in:Elegir'
        ]);
           
        $this->validate([
            'numero'           => 'required', 
            'importe'          => 'required',
            'fecha_de_emision' => 'required',
            'fecha_de_pago'    => 'required',
            'cuitTitular'      => 'required'
        ]);  
        DB::begintransaction();                  
        try{
            if($this->selected_id > 0) {
                $existe = Cheque::where('numero', $this->numero)
                    ->where('banco_id', $this->banco)
                    ->where('id', '<>', $this->selected_id)
                    ->where('comercio_id', $this->comercioId)
                    ->withTrashed()->get();
                if($existe->count() && $existe[0]->deleted_at != null) {
                    $this->action = 1;
                    $this->recuperar_registro = 1;
                    $this->descripcion_soft_deleted = $existe[0]->descripcion;
                    $this->id_soft_deleted = $existe[0]->id;
                    return;
                }elseif($existe->count()) {
                    session()->flash('info', 'El Cheque ya existe en el sistema...');
                    $this->resetInput();
                    return;
                }
            }else {
                if($this->selected_id <= 0) {
                    $add_item = Cheque::create([         
                        'cliente_id'       => $this->cliente,
                        'banco_id'         => $this->banco,
                        'numero'           => $this->numero,
                        'fecha_de_emision' => Carbon::parse($this->fecha_de_emision)->format('Y,m,d') . ' 00:00:00',
                        'fecha_de_pago'    => Carbon::parse($this->fecha_de_pago)->format('Y,m,d') . ' 00:00:00',
                        'importe'          => $this->importe,
                        'cuit_titular'     => $this->cuitTitular,
                        'comercio_id'      => $this->comercioId
                    ]);
                }else {   
                    $record = Cheque::find($this->selected_id);
                    $record->update([
                        'cliente_id'       => $this->cliente,
                        'banco_id'         => $this->banco,
                        'numero'           => $this->numero,
                        'fecha_de_emision' => Carbon::parse($this->fecha_de_emision)->format('Y,m,d') . ' 00:00:00',
                        'fecha_de_pago'    => Carbon::parse($this->fecha_de_pago)->format('Y,m,d') . ' 00:00:00',
                        'importe'          => $this->importe,
                        'cuit_titular'     => $this->cuitTitular
                    ]);
                }
            }
            if($this->selected_id > 0) $this->emit('chequeModificado');
            else $this->emit('chequeCreado'); 

            DB::commit();  
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }     
        $this->resetInput();
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
    }       
    public function destroy($id, $comentario)
    {
        if ($id) {
            $record = Cheque::where('banco_id', $id)->get();
            if(!$record->count()){
                DB::begintransaction();
                try{
                    $banco = Cheque::find($id)->delete();
                    $audit = Auditoria::create([
                        'item_deleted_id' => $id,
                        'tabla' => 'Cheques',
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
            }else $this->emit('eliminarRegistro');
        
            $this->resetInput();
            return;
        }
    }
}
