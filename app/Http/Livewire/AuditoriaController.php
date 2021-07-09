<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Auditoria;
use App\Models\User;
use App\Models\Gasto;
use DB;

class AuditoriaController extends Component
{          
    public $search, $comercioId;

    public function render()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');

        if(strlen($this->search) > 0) {
            $info = Auditoria::join('users as u', 'u.id', 'auditorias.user_delete_id')
                ->where('tabla', 'like', '%' .  $this->search . '%')
                ->where('auditorias.comercio_id', $this->comercioId)
                ->orderby('auditorias.created_at','desc')->select('auditorias.*', 'u.name as nomUser', 'u.apellido as apeUser', 
                DB::RAW("'' as item"))->get();
        }else {
            $info = Auditoria::join('users as u', 'u.id', 'auditorias.user_delete_id')
                ->where('auditorias.comercio_id', $this->comercioId)
                ->orderby('auditorias.created_at','desc')->select('auditorias.*', 'u.name as nomUser', 'u.apellido as apeUser', 
                DB::RAW("'' as item"))->get();
        }
        foreach($info as $i){
            switch ($i->tabla) {
                case 'Egresos':                               
                    $info2 = Auditoria::join('gastos as g', 'g.id', 'auditorias.item_deleted_id')
                        ->where('auditorias.id', $i->id)
                        ->select('g.descripcion')->get();
                    $i->item = $info2[0]->descripcion;
                    break; 
                case 'Otros Ingresos':                               
                    $info2 = Auditoria::join('otro_ingresos as oi', 'oi.id', 'auditorias.item_deleted_id')
                        ->where('auditorias.id', $i->id)
                        ->select('oi.descripcion')->get();
                    $i->item = $info2[0]->descripcion;
                    break; 
                case 'Caja Inicial':                               
                    $info2 = Auditoria::join('caja_inicials as ci', 'ci.id', 'auditorias.item_deleted_id')
                        ->where('auditorias.id', $i->id)
                        ->select('ci.importe')->get();
                    $i->item ='$ ' . $info2[0]->importe;
                    break; 
                case 'Facturas':                               
                    $info2 = Auditoria::join('facturas as f', 'f.id', 'auditorias.item_deleted_id')
                        ->where('auditorias.id', $i->id)
                        ->select('f.numero')->get();
                    $i->item = 'FAC' . str_pad($info2[0]->numero, 6, '0', STR_PAD_LEFT);
                    break; 
                case 'Caja':                               
                    $info2 = Auditoria::join('caja_usuarios as cu', 'cu.id', 'auditorias.item_deleted_id')
                        ->join('cajas as c', 'c.id', 'cu.caja_id')
                        ->join('users as u', 'u.id', 'cu.caja_usuario_id')
                        ->where('auditorias.id', $i->id)
                        ->select('c.descripcion', 'u.apellido', 'u.name')->get();
                    $i->item = $info2[0]->descripcion . '/' . $info2[0]->apellido . ' ' . $info2[0]->name;
                    break; 
                case 'Empleados':                               
                    $info2 = Auditoria::join('users as u', 'u.id', 'auditorias.item_deleted_id')
                        ->where('auditorias.id', $i->id)
                        ->select('u.apellido', 'u.name')->get();
                    $i->item = $info2[0]->apellido . ' ' . $info2[0]->name;
                    break; 
                case 'Categorias':                               
                    $info2 = Auditoria::join('categorias as c', 'c.id', 'auditorias.item_deleted_id')
                        ->where('auditorias.id', $i->id)
                        ->select('c.descripcion')->get();
                    $i->item = $info2[0]->descripcion;
                    break; 
                case 'Detalle de Facturas':                               
                    $info2 = Auditoria::join('detFacturas as df', 'df.id', 'auditorias.item_deleted_id')
                        ->join('productos as p', 'p.id', 'df.producto_id')
                        ->join('facturas as f', 'f.id', 'df.factura_id')
                        ->where('auditorias.id', $i->id)
                        ->select('df.cantidad', 'p.descripcion', 'f.numero')->get();
                    $i->item = number_format($info2[0]->cantidad,2,',','.') . ' ' . $info2[0]->descripcion . ' - ' . 'FAC' . str_pad($info2[0]->numero, 6, '0', STR_PAD_LEFT);
                    break; 
                default:
            } 
        }
        return view('livewire.auditorias.component', ['info' =>$info]);
    }
}
