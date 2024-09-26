<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\CajaUsuario;
use App\Models\Socio;
use App\Models\CtaCteClub;
use App\Models\Debito;
use App\Models\ReciboClub;
use App\Models\ReciboDebito;
use DB;

class CtaCteClubController extends Component
{
    //public properties
	public $socio = 'Elegir', $importeCobrado, $comentario, $suma = 0, $sumaDebitos = 0, $sumaRecibos = 0;           
    public $selected_id = null, $search = '', $ver_historial = 0, $verHistorial = 0;  
    public $comercioId, $action = 1, $nomSocio, $numRecibo, $cliSelected = '', $socioId = '';
    public $nomApeSocio, $totalSocio, $debitos_a_cobrar = array(), $entrega = 0;
    public $importeDebito, $importeEntrega, $saldo = 0, $entregas = 0, $saldoDebito, $nro_arqueo, $caja_abierta;
    public $f_de_pago = '1', $nro_comp_pago = '0', $mercadopago = '0';

    public function render()
    {
         //busca el comercio que está en sesión
        $this->comercioId = session('idComercio');
        session(['DebitoPendiente' => null]);  

        //vemos si tenemos una caja habilitada para nuestro user_id 
        //en cuyo caso podremos cobrar, sino solo podremos ver e imprimir resúmenes de cta
        $caja_abierta = CajaUsuario::where('caja_usuarios.caja_usuario_id', auth()->user()->id)
            ->where('caja_usuarios.estado', '1')->get();
        $this->caja_abierta = $caja_abierta->count();  
        if($caja_abierta->count() > 0){
            $this->nro_arqueo = $caja_abierta[0]->id;
            // $this->fecha_inicio = $caja_abierta[0]->created_at;  
        }

        $infoEntrega = '';
        $this->importeEntrega = 0;
        $socios = Socio::select()->where('comercio_id', $this->comercioId)->orderBy('apellido', 'asc')->get();
    
        if(strlen($this->search) == 0){
            $this->resetInput();    
        }
        if(strlen($this->search) > 0 || $this->socioId != ''){          
            if($this->verHistorial == 1){   
                $info = CtaCteClub::join('socios as c', 'c.id', 'cta_cte_clubs.socio_id')
                    ->where('cta_cte_clubs.socio_id', $this->socioId)
                    ->select('cta_cte_clubs.debito_id', 'cta_cte_clubs.recibo_id', 
                            'c.nombre', 'c.apellido', DB::RAW("'' as fecha") , DB::RAW("'' as numero") , DB::RAW("'' as importe"), DB::RAW("'' as importe_debito"))
                    ->orderBy('cta_cte_clubs.created_at', 'desc')->get();
            }else{ //verHistorial = 0
                if($this->socioId == ''){
                    $info = CtaCteClub::join('socios as c', 'c.id', 'cta_cte_clubs.socio_id')
                        ->join('localidades as loc', 'loc.id', 'c.localidad_id')
                        ->where('c.nombre', 'like', '%' .  $this->search . '%')
                        ->where('c.comercio_id', $this->comercioId)
                        ->orWhere('c.apellido', 'like', '%' .  $this->search . '%')
                        ->where('c.comercio_id', $this->comercioId)
                        ->select('cta_cte_clubs.socio_id', 'c.nombre', 'c.apellido', 'c.calle', 'c.numero', 'loc.descripcion as localidad', DB::RAW("'' as numero_deb") , DB::RAW("'' as importe_debito"))
                        ->groupBy('cta_cte_clubs.socio_id', 'c.nombre', 'c.apellido', 'c.calle', 'c.numero', 'loc.descripcion')
                        ->orderBy('c.apellido')->orderBy('c.nombre')->get(); 
                }else{
                    $info = CtaCteClub::join('socios as c', 'c.id', 'cta_cte_clubs.socio_id')
                        ->join('debitos as f', 'f.id', 'cta_cte_clubs.debito_id')
                        ->where('cta_cte_clubs.socio_id', $this->socioId)
                        ->where('f.estado', 'ctacte')
                        ->where('f.estado_pago', '0')
                        ->orWhere('cta_cte_clubs.socio_id', $this->socioId)
                        ->where('f.estado', 'ctacte')
                        ->where('f.estado_pago', '2')
                        ->select('cta_cte_clubs.debito_id', 'cta_cte_clubs.recibo_id', 'cta_cte_clubs.socio_id', 
                                 'c.nombre', 'c.apellido', DB::RAW("'' as fecha"), DB::RAW("'' as numero_deb") , DB::RAW("'' as importe"), 
                                 DB::RAW("'' as importe_debito"), DB::RAW("'' as resto"))
                        ->orderBy('cta_cte_clubs.created_at')->get();                     
                }
            } 
        }else{
            $this->verHistorial = 0;
            $this->socioId = '';
            $info = CtaCteClub::join('socios as c', 'c.id', 'cta_cte_clubs.socio_id')
                ->join('localidades as loc', 'loc.id', 'c.localidad_id')
                ->where('c.comercio_id', $this->comercioId)
                ->select('cta_cte_clubs.socio_id', 'c.nombre', 'c.apellido', 'c.calle', 'c.numero', 'loc.descripcion as localidad', DB::RAW("'' as importe"), DB::RAW("'' as importe_debito"))
                ->groupBy('cta_cte_clubs.socio_id', 'c.nombre', 'c.apellido', 'c.calle', 'c.numero', 'loc.descripcion')
                ->orderBy('c.apellido')->orderBy('c.nombre')->get();
        }

        if(strlen($this->search) == 0){
                foreach($info as $i) {
                    $this->sumaDebitos=0;
                    $this->sumaRecibos=0;
                     //verifico si el registro es una Debito o es un recibo
                    $registroCtaCteClub = CtaCteClub::where('cta_cte_clubs.socio_id', $i->socio_id)->get();
                    foreach($registroCtaCteClub as $r) {   
                        if($r->debito_id != null) {    //si es Debito las voy sumando
                            $importe = Debito::where('id', $r->debito_id)
                            ->where('estado', 'ctacte')
                            ->where('estado_pago', '0')
                            ->orWhere('id', $r->debito_id)
                            ->where('estado', 'ctacte')
                            ->where('estado_pago', '2')
                            ->select('numero', 'importe')->get();
                            foreach($importe as $imp){
                                $this->sumaDebitos += $imp->importe; //calculo el total de las Debitos de cada Socio
                                $i->numero_deb = $imp->numero;
                            }
                        }else {                         //busco todos los recibos
                            $importe = ReciboClub::where('id', $r->recibo_id)
                            ->where('entrega', 1)
                            ->select('id','numero', 'importe')->get();
                            foreach($importe as $imp){
                                $verEstadoPagoDebito = ReciboDebito::join('debitos as f','f.id','recibo_debitos.debito_id')
                                    ->where('recibo_debitos.recibo_club_id',$imp->id)
                                    ->where('f.estado_pago','2')->get();
                                if($verEstadoPagoDebito->count() > 0){
                                    $this->sumaRecibos += $imp->importe; //calculo el total de recibos de cada Socio
                                    $i->numero_deb = $imp->numero; 
                                }
                            }
                        }
                    }
                    if($this->sumaDebitos == 0) $this->sumaRecibos = 0;  //si no debe nada, no muestro las entregas
                    //calculo el total para cada Socio
                    $i->importe = $this->sumaDebitos - $this->sumaRecibos;
                    $this->totalSocio = $i->importe;
                    //pinto el importe de diferente color
                    if($i->importe < 0) $i->importe_debito = 0;
                    else $i->importe_debito = 1;
                }
        }

        if(strlen($this->search) > 0 || $this->socioId != ''){
            if($this->verHistorial == 0){   
                if($this->socioId == ''){
                    foreach($info as $i) {
                        $this->sumaDebitos=0;
                        $this->sumaRecibos=0;
                         //verifico si el registro es una Debito o es un recibo
                        $registroCtaCteClub = CtaCteClub::where('cta_cte_clubs.socio_id', $i->socio_id)->get();
                        foreach($registroCtaCteClub as $r) {   
                            if($r->debito_id != null) {    //si es Debito las voy sumando
                                $importe = Debito::where('id', $r->debito_id)
                                ->where('estado', 'ctacte')
                                ->where('estado_pago', '0')
                                ->orWhere('id', $r->debito_id)
                                ->where('estado', 'ctacte')
                                ->where('estado_pago', '2')
                                ->select('numero', 'importe')->get();
                                foreach($importe as $imp){
                                    $this->sumaDebitos += $imp->importe; //calculo el total de las Debitos de cada Socio
                                    $i->numero_deb = $imp->numero;
                                }
                            }else {                         //busco todos los recibos
                                $importe = ReciboClub::where('id', $r->recibo_id)
                                ->where('entrega', 1)
                                ->select('id','numero', 'importe')->get();
                                foreach($importe as $imp){
                                    $verEstadoPagoDebito = ReciboDebito::join('debitos as f','f.id','recibo_debitos.debito_id')
                                        ->where('recibo_debitos.recibo_club_id',$imp->id)
                                        ->where('f.estado_pago','2')->get();
                                    if($verEstadoPagoDebito->count() > 0){
                                        $this->sumaRecibos += $imp->importe; //calculo el total de recibos de cada Socio
                                        $i->numero_deb = $imp->numero; 
                                    }
                                }
                            }
                        }
                        if($this->sumaDebitos == 0) $this->sumaRecibos = 0;  //si no debe nada, no muestro las entregas
                        //calculo el total para cada Socio
                        $i->importe = $this->sumaDebitos - $this->sumaRecibos;
                        $this->totalSocio = $i->importe;
                        //pinto el importe de diferente color
                        if($i->importe < 0) $i->importe_debito = 0;
                        else $i->importe_debito = 1;
                    }      
                }else{  //si verHistorial = 0 y socioId != ''
                    $this->sumaDebitos=0;
                    $this->sumaRecibos=0;
                    if($info->count() > 0){        //si debe algo... 
                        foreach($info as $i) {     //busco todas las Debitos                            
                            $importe = CtaCteClub::join('debitos as f', 'f.id', 'cta_cte_clubs.debito_id') 
                                ->where('f.id', $i->debito_id)
                                ->where('f.estado', 'ctacte')
                                ->where('f.estado_pago', '0')
                                ->orWhere('f.id', $i->debito_id)
                                ->where('f.estado', 'ctacte')
                                ->where('f.estado_pago', '2')
                                ->select('f.estado_pago', 'f.importe as importe', 'f.numero', 'f.created_at')->get();
                            $this->sumaDebitos += $importe[0]->importe; //calculo el total de las Debitos de cada Socio
                            if($importe[0]->estado_pago == 0) $i->importe_debito = 1; //aviso de Debito para pintar rojo   
                            else $i->importe_debito = 2;  //aviso de Debito para pintar rojo/negrita            
                            
                            $i->numero_deb = $importe[0]->numero;
                            $i->fecha      = $importe[0]->created_at;                        
                            $i->importe    = $importe[0]->importe;
                            
                            //busco las entregas y calculo el resto de las Debitos que correspondan                           
                            if($importe[0]->estado_pago == 2){
                                $this->entregas = 0;
                                $pagos = ReciboDebito::join('recibos as r', 'r.id', 'recibo_debitos.recibo_club_id')
                                    ->where('recibo_debitos.debito_id', $i->debito_id)
                                    ->select('r.importe')->get();
                                foreach($pagos as $p){
                                    $this->entregas += $p->importe;
                                }
                                $this->importeEntrega += $this->entregas;
                                $i->resto = $i->importe - $this->entregas;
                            }
                        } 
                        $this->totalSocio = $this->sumaDebitos;
                        //calculo el saldo del Socio seleccionado
                        $this->saldo = $this->totalSocio - $this->importeEntrega;
                    }else{  //si el saldo es cero, dejo todo en cero...
                        $this->totalSocio = $this->sumaDebitos;
                        $this->importeEntrega = 0;
                    }
                }
            }else{  //si verHistorial = 1
                foreach($info as $i) {
                    if($i->debito_id != null) {    //busco todas las Debitos
                        $importe = Debito::where('id', $i->debito_id)
                        ->select('numero', 'importe', 'created_at')->get();
                        $i->numero_deb = $importe[0]->numero;
                        $i->fecha = $importe[0]->created_at;
                        $i->importe_debito = 1;        //aviso de Debito para pintar rojo                   
                    }else {                         //busco todos los recibos
                        $importe = ReciboClub::where('id', $i->recibo_id)
                            ->select('numero', 'importe', 'created_at')->get();
                        $i->numero_deb      = $importe[0]->numero;
                        $i->fecha           = $importe[0]->created_at;
                        $i->importe_debito = 0;        //aviso de recibo para pintar verde
                    }
                    $i->importe = $importe[0]->importe;
                    if($i->importe_debito == 0) $this->suma += $i->importe;
                    else $this->suma -= $i->importe;
                }
            }                 
        }else{
            $this->suma=0;
            foreach($info as $i) {
                $this->sumaDebitos=0;
                $this->sumaRecibos=0;
                //verifico si el registro es una Debito o es un recibo
                $registroCtaCteClub = CtaCteClub::where('cta_cte_clubs.socio_id', $i->socio_id)->get();
                foreach($registroCtaCteClub as $r) {   
                    if($r->debito_id != null) {    //si es Debito las voy sumando
                        $importe = Debito::where('id', $r->debito_id)
                        ->where('estado', 'ctacte')
                        ->where('estado_pago', '0')
                        ->orWhere('id', $r->debito_id)
                        ->where('estado', 'ctacte')
                        ->where('estado_pago', '2')
                        ->select('importe')->get();
                        foreach($importe as $imp){
                            $this->sumaDebitos += $imp->importe; //calculo el total de las Debitos de cada Socio
                        }
                    }else {                         //busco todos los recibos
                        $importe = ReciboClub::where('id', $r->recibo_id)
                        ->where('entrega', 1)
                        ->select('id','importe')->get();
                        foreach($importe as $imp){
                            $verEstadoPagoDebito = ReciboDebito::join('debitos as f','f.id','recibo_debitos.debito_id')
                            ->where('recibo_debitos.recibo_club_id',$imp->id)
                            ->where('f.estado_pago','2')->get();
                            if($verEstadoPagoDebito->count() > 0)
                            $this->sumaRecibos += $imp->importe; //calculo el total de recibos de cada Socio
                        }
                    }
                }
                if($this->sumaDebitos == 0) $this->sumaRecibos = 0;  //si no debe nada, no muestro las entregas
                // calculo el total para cada Socio
                $i->importe = $this->sumaDebitos - $this->sumaRecibos;
                //solo calculo el importe del total gral si se están mostrando todos los Socios
                $this->suma += $i->importe;
                //pinto el importe de diferente color
                if($i->importe < 0) $i->importe_debito = 0;
                else $i->importe_debito = 1;
            }
        }
        return view('livewire.CtaCteClub.component', [
            'info'        => $info,
            'infoEntrega' => $infoEntrega,
            'socios'      => $socios
        ]);
    }
    protected $listeners = [  
        'cobrar'          => 'cobrar',      
        'mostrar_debitos' => 'mostrar_debitos',
        'enviarDatosPago' => 'enviarDatosPago',
        'StoreOrUpdate'   => 'StoreOrUpdate'
    ];
    public function doAction($action)
    {
        $this->action = $action;
    }
    private function resetInput()
    {
        //$this->f_de_pago = '1';
        $this->importeCobrado = '';
        $this->socio = 'Elegir';
        $this->comentario = '';
        $this->selected_id = null;    
        $this->search = '';
        $this->verHistorial = 0;
        $this->nomApeSocio = '';
        $this->totalSocio = '';
        $this->socioId = '';
        $this->action = 1;
    }
    public function clearSocioSelected()
    {
        $this->resetInput();
    }
    public function verHistorial($tipo)
    {
        $this->verHistorial = $tipo;
    }
    public function edit($id)
    {
        $record = CtaCteClub::findOrFail($id);
        $this->selected_id = $id;
        $this->socio = $record->socio_id;
        $this->f_de_pago = $record->created_at;
        $this->importeCobrado = $record->importe;
        $this->comentario = $record->comentario;
    }
    public function StoreOrUpdate()
    { 
        if($this->entrega == 1){
            if($this->importeCobrado == $this->importeDebito){
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El importe a registrar es igual al importe total de la Debito... en esta vista solo se registran entregas');
                return;
            }elseif($this->importeCobrado > $this->importeDebito){
                session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El importe a registrar no puede ser mayor al importe total de la Debito...');
                return;
            }  
        }     
        $existe = ReciboClub::select('*')->where('comercio_id', $this->comercioId)->get();                        // ->where('estado','like','abierta')->get();
        //si es el primer recibo, le asigno el nro: 1
        if($existe->count() == 0){
            $this->numRecibo = 1;
        }else{ 
            $encabezado = ReciboClub::select('numero')
            ->where('comercio_id', $this->comercioId)
            ->orderBy('numero', 'desc')->get();                             
            $this->numRecibo = $encabezado[0]->numero + 1;
        }
        DB::begintransaction();                         //iniciar transacción para grabar
        try{
            $recibo =  ReciboClub::create([
                'numero'        => $this->numRecibo,            
                'importe'       => $this->importeCobrado,
                'forma_de_pago' => $this->f_de_pago,
                'nro_comp_pago' => $this->nro_comp_pago,  //nro ticket tarjeta o nro transferencia
                'mercadopago'   => $this->mercadopago,
                'comentario'    => $this->comentario,
                'entrega'       => $this->entrega,
                'socio_id'      => $this->socioId, 
                'user_id'       => auth()->user()->id,         
                'comercio_id'   => $this->comercioId,
                'arqueo_id'     => $this->nro_arqueo          
            ]);
            CtaCteClub::create([
                'socio_id'  => $this->socioId,  
                'recibo_id' => $recibo->id            
            ]);
            foreach($this->debitos_a_cobrar as $i){
                if($this->entrega == 0){
                    $record = Debito::find($i); 
                    $record->update([
                        'estado_pago' => '1'
                    ]);
                }else{
                    $record = Debito::find($i); 
                    $record->update([
                        'estado_pago' => '2'
                    ]);
                }
                ReciboDebito::create([
                    'recibo_club_id' => $recibo->id,  
                    'debito_id'      => $i       
                ]);      
            }
            session()->flash('msg-ok', 'El Cobro se registró correctamente...');
            DB::commit();          
        }catch (Exception $e){
            DB::rollback();
            session()->flash('msg-error', '¡¡¡ATENCIÓN!!! El registro no se grabó...');
        }
        $this->verificar_saldo();    
    }
    public function verificar_saldo()    //busca si hay alguna Debito que el estado_pago no sea =1
    {        
        $saldo = Debito::where('socio_id', $this->socioId)
            ->where('estado_pago', '<>', '1')
            ->where('estado', '<>', 'anulado')
            ->where('estado', '<>', 'pendiente')->get();
        if($saldo->count() == 0){
            $record = Socio::find($this->socioId);
            $record->update([
                'saldo' => '0'
            ]);
        }
        $this->resetInput();
        return;
    } 
    public function enviarDatosPago($tipo,$nro)
    {
        $this->f_de_pago = $tipo;
        $this->nro_comp_pago = $nro;
    }
    public function mostrar_debitos($id)
    {
        if($this->socioId == ''){
            if($id != ''){
                $this->socioId = $id;
                $socio = Socio::where('id', $id)->select('nombre','apellido')->first();
                $this->nomApeSocio = $socio->apellido . ' ' . $socio->nombre;
            }
        }
    }
    public function cobrar($data, $total, $entrega, $cantidad)
    {
        $this->entrega = $entrega;
        $this->debitos_a_cobrar = json_decode($data);
        $importeDebito = json_decode($total);
        $importeCobrado = json_decode($total);
        if($entrega == 0){
            $this->importeDebito = $importeDebito;       
            $this->importeCobrado = $importeCobrado;
        }else{
            $this->importeDebito = $importeDebito[0];       
            $this->importeCobrado = $importeCobrado[0];
        }
        if($cantidad > 1){
            $DebitosConEntrega =0;
            foreach($this->debitos_a_cobrar as $f){
                $ver = Debito::where('id', $f)
                    ->select('estado_pago')->get();
                if($ver[0]->estado_pago == 2){
                    $DebitosConEntrega ++;
                }
            }
            if($DebitosConEntrega > 0){
                session()->flash('info', '¡¡¡ATENCIÓN!!! En la selección de Debitos existe alguna que tiene entregas a cuenta...');
                return;
            }
        }

        $socio = Socio::where('id', $this->socioId)->get();
        $this->nomSocio = $socio[0]->apellido . ' ' . $socio[0]->nombre;

        $ver = Debito::where('id', $this->debitos_a_cobrar[0])
            ->select('importe', 'estado_pago')->get();

        if($ver[0]->estado_pago == 2){
            $totalEntregas = 0;
            $pagos = ReciboDebito::join('recibos as r', 'r.id', 'recibo_debitos.recibo_club_id')
                ->where('recibo_debitos.debito_id', $this->debitos_a_cobrar[0])
                ->select('r.importe')->get();
            foreach($pagos as $p){
                $totalEntregas += $p->importe;
            }
            // $this->saldoDebito = $ver[0]->importe - $totalEntregas;
            $this->importeCobrado = $ver[0]->importe - $totalEntregas;
        }
        $this->f_de_pago = '1';        
        $this->doAction(2);
    }
}
