<?php

namespace App\Http\Livewire;

use Livewire\Component;
use\App\Models\Factura;
use\App\Models\Ctacte;
use\App\Models\User;
use DB;

class FacturasACobrarController extends Component
{

    public $comercioId, $salon;

    public function render()
    {
        //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');

        $this->salon = User::join('model_has_roles as mhr', 'mhr.model_id', 'users.id')
            ->join('roles as r', 'r.id', 'mhr.role_id')
            ->where('users.name', '...')
            ->where('users.apellido', 'Salón')
            ->where('r.comercio_id', $this->comercioId)
            ->select('users.id')->get();

        $info = Factura::join('clientes as c','c.id','facturas.cliente_id')
            ->join('users as u','u.id','facturas.repartidor_id')
            ->select('facturas.*', 'c.nombre as nomCli', 'c.apellido as apeCli', 'u.name as nomRep',
                    'u.apellido as apeRep')
            ->where('facturas.comercio_id', $this->comercioId)
            ->where('facturas.estado','like','pendiente')
            ->where('facturas.repartidor_id', $this->salon[0]->id)
            ->where('facturas.user_id', auth()->user()->id)
            ->orderBy('facturas.id', 'asc')->get();
//  dd($this->salon[0]->id);
        return view('livewire.facturasacobrar.component', [
            'info' => $info
        ]);
    }

    protected $listeners = [
        'factura_contado' => 'factura_contado',
        'factura_ctacte'  => 'factura_ctacte'
    ];

    public function factura_contado($id)
    {
        $record = Factura::find($id);
        $record->update([
            'estado' => 'contado',
            'estado_pago' => '1'
        ]);
        $this->resetInput();
    }

    public function factura_ctacte($id, $cliId)
    {
        DB::begintransaction();                         //iniciar transacción para grabar
        try{
            $record = Factura::find($id);
            $record->update([
                'cliente_id' => $cliId,
                'estado' => 'ctacte',
                'estado_pago' => '0'
            ]);
            Ctacte::create([
                'cliente_id' => $cliId,
                'factura_id' => $id
            ]);
            session()->flash('msg-ok', 'La Factura fué enviada a Cuenta Corriente...');
            DB::commit();
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        //$this->resetInput();
        return;
    }
}
