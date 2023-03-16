<div class="row layout-top-spacing justify-content-center">
    @if($action == 1)	
    <div class="col-md-12 col-lg-6 layout-spacing"> 		
		<div class="widget-content-area br-4">
			<div class="widget-one widget-h">
                <div class="row">
                    <div class="col-md-6 text-left">
                        <h3>Factura N°: {{str_pad($numFactura, 6, '0', STR_PAD_LEFT)}}</h3>
                    </div>
                    <div class="col-md-6 text-center">
                        <h3 class="bg-danger" style="border-radius: 5px;">Total : $ {{number_format($total,2,',','.')}}</h3> 
                    </div>
                </div>  
                <div class="row">
                    <div class="col-md-3">
                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                            @if($inicio_factura)
                                @if($comercioTipo != 11)
                                    @if($lista == '1')
                                        <button id="btn1" type="button" class="btn btn-danger"
                                            wire:click="usarLista('1')">L1</button>                    
                                        <button id="btn2" type="button" class="btn btn-outline-danger"
                                            wire:click="usarLista('2')">L2</button>
                                    @else
                                        <button id="btn1" type="button" class="btn btn-outline-danger"
                                            wire:click="usarLista('1')">L1</button>                    
                                        <button id="btn2" type="button" class="btn btn-danger"
                                            wire:click="usarLista('2')">L2</button>
                                    @endif
                                @else
                                    @if($lista == '1')
                                        <button id="btn1" type="button" class="btn btn-danger"
                                            wire:click="usarLista('1')">L1</button>                    
                                        <button id="btn2" type="button" class="btn btn-outline-danger"
                                            wire:click="usarLista('2')">L2</button>
                                        @if($modConsignaciones == "1")
                                        <button id="btn3" type="button" class="btn btn-outline-danger"
                                            wire:click="usarLista('3')">LC</button>
                                        @endif
                                    @elseif($lista == '2')
                                        <button id="btn1" type="button" class="btn btn-outline-danger"
                                            wire:click="usarLista('1')">L1</button>                    
                                        <button id="btn2" type="button" class="btn btn-danger"
                                            wire:click="usarLista('2')">L2</button>
                                        @if($modConsignaciones == "1")
                                        <button id="btn3" type="button" class="btn btn-outline-danger"
                                            wire:click="usarLista('3')">LC</button>
                                        @endif
                                    @else
                                        <button id="btn1" type="button" class="btn btn-outline-danger"
                                            wire:click="usarLista('1')">L1</button>                    
                                        <button id="btn2" type="button" class="btn btn-outline-danger"
                                            wire:click="usarLista('2')">L2</button>
                                        @if($modConsignaciones == "1")
                                        <button id="btn3" type="button" class="btn btn-danger"
                                            wire:click="usarLista('3')">LC</button>
                                        @endif
                                    @endif
                                @endif
                            @else
                                @if($comercioTipo == 10)
                                    @if($lista == '1')
                                        <button id="btn1" type="button" class="btn btn-danger" disabled>L1</button>                    
                                        <button id="btn2" type="button" class="btn btn-outline-danger" disabled>L2</button>
                                    @else
                                        <button id="btn1" type="button" class="btn btn-outline-danger" disabled>L1</button>                    
                                        <button id="btn2" type="button" class="btn btn-danger" disabled>L2</button>
                                    @endif
                                @else
                                    @if($lista == '1')
                                        <button id="btn1" type="button" class="btn btn-danger" disabled>L1</button>                    
                                        <button id="btn2" type="button" class="btn btn-outline-danger" disabled>L2</button>
                                        @if($modConsignaciones == "1")
                                        <button id="btn3" type="button" class="btn btn-outline-danger" disabled>LC</button>
                                        @endif
                                    @elseif($lista == '2')
                                        <button id="btn1" type="button" class="btn btn-outline-danger" disabled>L1</button>                    
                                        <button id="btn2" type="button" class="btn btn-danger" disabled>L2</button>
                                        @if($modConsignaciones == "1")
                                        <button id="btn3" type="button" class="btn btn-outline-danger" disabled>LC</button>
                                        @endif
                                    @else
                                        <button id="btn1" type="button" class="btn btn-outline-danger" disabled>L1</button>                    
                                        <button id="btn2" type="button" class="btn btn-outline-danger" disabled>L2</button>
                                        @if($modConsignaciones == "1")
                                        <button id="btn3" type="button" class="btn btn-danger" disabled>LC</button>
                                        @endif
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="col-md-9 text-right">
                        <div class="btn-group mb-2" role="group" aria-label="Basic mixed styles example">            
                            @if($total == 0)
                                @if($modDelivery == "1")
                                <button type="button" onclick="openModal({{$factura_id}})"
                                    class="btn btn-dark" enabled>
                                    Delivery   
                                </button>  
                                @else      
                                <button type="button" onclick="openModal({{$factura_id}})"
                                    class="btn btn-dark" enabled>
                                    Clientes   
                                </button>
                                @endif        
                                <button type="button" class="btn btn-warning" disabled>Dejar Pendiente</button>                    
                                <button type="button" class="btn btn-primary" disabled>Cobrar</button>
                                <button type="button" class="btn btn-success" disabled>Imprimir</button>
                            @else
                                @if($delivery == 0)
                                    @if($modDelivery == "1")
                                    <button type="button" onclick="openModal({{$factura_id}})"
                                        class="btn btn-dark" enabled>
                                        Delivery   
                                    </button>  
                                    @else      
                                    <button type="button" onclick="openModal({{$factura_id}})"
                                        class="btn btn-dark" enabled>
                                        Clientes   
                                    </button>
                                    @endif
                                    <button type="button" class="btn btn-warning" disabled>
                                        Dejar Pendiente
                                    </button>
                                @else
                                    @if($modDelivery == "1")
                                    <button type="button" onclick="openModal({{$factura_id}})"
                                        class="btn btn-dark" enabled>
                                        Mod Cli/Rep                                         
                                    </button>
                                    @else
                                        @if($lista != "3")
                                        <button type="button" onclick="openModal({{$factura_id}})"
                                            class="btn btn-dark" enabled>
                                            Mod Cliente                                         
                                        </button>
                                        @endif
                                    @endif
                                    <button type="button" onclick="dejar_pendiente()"
                                        class="btn btn-warning" enabled>
                                        Dejar Pendiente
                                    </button>
                                @endif 
                                <!-- <button type="button" onclick="Cobrar({{$delivery}},{{$clienteId}})"  -->
                                <button type="button" wire:click="doAction(2)" 
                                    class="btn btn-primary" enabled>
                                    Cobrar   
                                </button>
                                <button type="button" class="btn btn-success" enabled>
                                    <a href="{{url('pdfFactDel',array($factura_id))}}" target="_blank">
                                    Imprimir</a>
                                </button>
                                <button type="button" onclick="AnularFactura({{$factura_id}})" 
                                    class="btn btn-info" enabled>
                                    Anular Factura  
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- si es delivery --> 
                @if($delivery == 1)          
                    @if($inicio_factura)   <!-- si es inicio de factura -->
                        <div class="row mt-2">
                            <div class="col-7">
                                <h6>Cliente:  {{$apeNomCli}}</h6>
                            </div>
                            @if($modDelivery == "1")
                            <div class="col-5">
                                <h6>Rep:  {{$apeNomRep}}</h6>
                            </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-7">
                                <h6>Dirección:  {{$dirCliente}}</h6>
                            </div>
                            <div class="col-5">
                                @if($saldoCtaCte < 0)
                                    <h6 style="color:red">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6>   
                                @else
                                    <h6 style="color:green">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6> 
                                @endif
                            </div>
                        </div>       
                    @else
                        <div class="row mt-2">
                            <div class="col-7">
                                <h6>Cliente:  {{$encabezado[0]->apeCli}} {{$encabezado[0]->nomCli}}</h6>
                            </div>
                            @if($modDelivery == "1")
                            <div class="col-5">
                                <h6>Rep:  {{$encabezado[0]->apeRep}} {{$encabezado[0]->nomRep}}</h6>
                            </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-7">
                                <h6>Dirección:  {{$encabezado[0]->calle}} {{$encabezado[0]->numero}} - {{$encabezado[0]->localidad}}</h6>
                            </div>
                            <div class="col-5">
                                @if($saldoCtaCte < 0)
                                    <h6 style="color:red">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6>   
                                @else
                                    <h6 style="color:green">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6> 
                                @endif
                            </div>
                        </div>                
                    @endif          
                @endif
                @if($mostrar_datos == 1)
                    <div class="row mt-2">
                        <div class="col-7">
                            <h6>Cliente:  {{$apeNomCli}}</h6>
                        </div>
                        <div class="col-5">
                            <h6>Rep:  {{$apeNomRep}}</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-7">
                            <h6>Dirección:  {{$dirCliente}}</h6>
                        </div>
                        <div class="col-5">
                            @if($saldoCtaCte < 0)
                                <h6 style="color:red">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6>   
                            @else
                                <h6 style="color:green">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6> 
                            @endif
                        </div>
                    </div>   
                @endif
                @include('common.alerts')
                <div class="table-responsive scroll">
                    <table class="table table-hover table-checkable table-sm mb-4">
                        <thead>
                            <tr>
                                <th class="text-center">CANTIDAD</th>
                                <th class="text-center">DESCRIPCIÓN</th>
                                <th class="text-right">P/UNITARIO</th>
                                <th class="text-right">IMPORTE</th>
                                @can('Facturas_edit_item')
                                <th class="text-center">ACCIONES</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($info as $r)
                            <tr class="">
                                <td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
                                <td class="text-left">{{$r->producto}}</td>
                                <td class="text-right">{{number_format($r->precio,2,',','.')}}</td>
                                <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
                                @can('Facturas_edit_item')
                                <td class="text-center">
                                    <ul class="table-controls">
                                        <li>
                                            <a href="javascript:void(0);" wire:click="edit({{$r->id}},{{$r->es_producto}})" data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"          		
                                            onclick="Confirm('{{$r->id}}','{{$r->es_producto}}')"
                                            data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                        </li>
                                    </ul>
                                    <!-- @include('common.actions', ['edit' => 'Facturas_edit_item', 'destroy' => 'Facturas_destroy_item']) -->
                                </td>
                                @endcan
                            </tr>
                            @endforeach
                        </tbody>
                    </table>                   
                </div>  
			</div>			
		</div>
	</div>

    <div class="col-md-12 col-lg-6 layout-spacing">
        <div class="widget-content-area">
            <div class="widget-one">
                @if($selected_id > 0)
                    <h5><b>Editar Item</b></h5>
                @endif 
                <form>
                    @include('common.messages')    
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-2">
                            <label>Cantidad</label>
                            <input id="cantidad" wire:model.lazy="cantidad" type="text" 
                                class="form-control form-control-sm text-center">
                        </div> 
                        @if($selected_id == null)
                            <div class="form-group col-sm-12 col-md-2">
                                <label >Código</label>
                                <input id="barcode" wire:model.lazy="barcode" type="text" 
                                    class="form-control form-control-sm">
                            </div>
                        @endif
                        @if($selected_id == null)
                        <div class="form-group col-sm-12 col-md-3">
                        @else
                        <div class="form-group col-sm-12 col-md-5">
                        @endif
                            @if($es_producto == 1)
                                <label>Producto</label>
                                @if($selected_id > 0)
                                    <select wire:model="producto" onclick="ocultar_sp()" class="form-control form-control-sm" disabled>
                                    @foreach($productos as $t)    
                                        <option value="{{ $t->id }}">{{$t->descripcion}}</option>
                                    @endforeach 
                                    </select>
                                @else
                                    <select wire:model="producto" onclick="ocultar_sp()" class="form-control form-control-sm">
                                        <option value="Elegir" >Elegir</option>
                                        @foreach($productos as $t)
                                        <option value="{{ $t->id }}">
                                            {{$t->descripcion}}                         
                                        </option> 
                                        @endforeach   
                                    </select>			               
                                @endif 			               
                            @else                            
                                <label>Subproducto</label>
                                @if($selected_id > 0)
                                    <select wire:model="subproducto" class="form-control form-control-sm" disabled>
                                        @foreach($subproductos as $t)
                                            <option value="{{ $t->id }}">{{$t->descripcion}}</option> 
                                        @endforeach   
                                    </select>  
                                @else
                                    <select wire:model="subproducto" class="form-control form-control-sm">
                                        <option value="Elegir" >Elegir</option>
                                            @foreach($subproductos as $t)
                                            <option value="{{ $t->id }}">
                                                {{$t->descripcion}}                         
                                            </option> 
                                            @endforeach   
                                    </select> 
                                @endif 
                            @endif 
                        </div>            
                        <div class="form-group col-sm-12 col-md-3">
                            <label>P/Unitario</label>
                            <input wire:model.lazy="precio" type="text" class="form-control form-control-sm text-right" disabled>
                        </div>
                        <div class="form-group col-sm-12 col-md-2 mt-2">
                        @if($selected_id == 0)
                            <label></label>
                            <button type="button" wire:click="StoreOrUpdateButton(0)" class="btn btn-primary mt-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-save2" viewBox="0 0 16 16"><path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v4.5h2a.5.5 0 0 1 .354.854l-2.5 2.5a.5.5 0 0 1-.708 0l-2.5-2.5A.5.5 0 0 1 5.5 6.5h2V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/></svg>                                
                            </button>
                        @endif
                        </div>
                    </div>
                    @if($selected_id > 0)
                    <div class="row">
                    <div class="col-12">
                        <button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">Cancelar</button>
                        <button type="button" wire:click="StoreOrUpdateButton(0)" class="btn btn-primary">
                        Guardar</button>    
                    </div>
                    @endif
                </form>
            </div>
        </div>
        @if($selected_id == 0)
        <div class="row mt-2">
            <div class="col-sm-12 col-lg-4">
                <div class="widget-content-area">
                    @if($categorias->count() > 6)
                        <div class="widget-one scrollb"> 
                            <div class="scrollContent"> 
                                @foreach($categorias as $c)                    
                                    <button style="width: 100%;" wire:click.prevent="buscarArticulo({{$c->id}})" type="button" class="btn btn-warning mb-1">{{$c->descripcion}}</button>
                                @endforeach
                            </div>
                        </div>
                    @else                   
                        @foreach($categorias as $c)                    
                            <button style="width: 100%;" wire:click.prevent="buscarArticulo({{$c->id}})" type="button" class="btn btn-warning mb-1">{{$c->descripcion}}</button>
                        @endforeach                       
                    @endif
                </div>
            </div>
            <div class="col-sm-12 col-lg-8">
                <div class="widget-content-area">
                    <div class="widget-one scrollb"> 
                        <div class="scrollContent"> 
                            @if($articulos != null)
                                @if($mostrar_sp == 0)
                                    @foreach($articulos as $a)                    
                                        <button style="width: 30%;height: 75px;" wire:click="StoreOrUpdateButton({{$a->id}})" type="button" class="btn btn-primary mb-1">{{$a->descripcion}}</button>
                                    @endforeach 
                                @else
                                    @foreach($tiene_sp as $sp)                    
                                        <button style="width: 30%;height: 75px;" wire:click="StoreOrUpdateButton({{$sp->id}})" type="button" class="btn btn-success mb-1">{{$sp->descripcion}}</button>
                                    @endforeach 
                                @endif
                            @endif                    
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="caja_abierta" wire:model="caja_abierta">  
            <input type="hidden" id="forzar_arqueo" wire:model="forzar_arqueo">  
            <input type="hidden" id="ultima_factura" wire:model="ultima_factura"> 
            <input type="hidden" id="inicio_factura" value="{{$inicio_factura}}">  
            <input type="hidden" id="modDelivery" wire:model="modDelivery">  
            <input type="hidden" id="lista" wire:model="lista">  
            <input type="hidden" id="importeTotal" value="{{$total}}">  
            <input type="hidden" id="comercioTipo" wire:model="comercioTipo">  
        </div>
        @endif 
    </div>
    @include('livewire.facturas.modal')  
    @else    
    @include('livewire.facturas.formaDePago')    
    @include('livewire.facturas.modalNroCompPago')  
    @include('livewire.facturas.modalCheques')  
    @include('livewire.facturas.modalBancos')  
	@endif        
</div>

<style type="text/css" scoped>
    .widget-h{
        position: relative;
        height:375px;
        overflow: hidden;
    }
    .scroll{
        position: relative;
        max-height: 230px;
        margin-top: .5rem;
        overflow: auto;
    }
    .scrollPagos{
        position: relative;
        max-height: 230px;
        padding: 0 10px;
        overflow: auto;
    }
    .scrollb {
        width: 100%;
        max-height:240px;
        overflow:hidden;
    }
    .scrollContent{
        width: 108%;
        height:240px;
        overflow-y:auto;
        overflow-x:hidden;
    }
    .scrollc {
        width: 100%;
        height:200px;
        overflow:hidden;
    }
    .scrollContentC{
        width: 108%;
        height:200px;
        overflow-y:auto;
        overflow-x:hidden;
    }
    thead tr th {     /* fija la cabecera de la tabla */
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #ffffff;
    }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> 

<script type="text/javascript">
 	function Confirm(id, es_producto)
    {
        let me = this
        swal({
            title: 'CONFIRMAR',
            text: '¿DESEAS ELIMINAR EL REGISTRO?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false
            },
            function() {
                window.livewire.emit('deleteRow', id, es_producto)   
                swal.close()   
            });
    }   
    function cobrar_factura()
    { 
        $('[id="saldo"]').val(Number.parseFloat($('[id="saldo"]').val()).toFixed(2));
        $('[id="importe"]').val(Number.parseFloat($('[id="importe"]').val()).toFixed(2));
        var saldo           = $('[id="saldo"]').val();
        var formaDePago     = $('[id="formaDePago"]').val();
        var nroCompPago     = $('[id="num"]').val();
        var importe         = $('[id="importe"]').val();
        var terminarFactura = 1;

        if(importe > saldo){
            Swal.fire('Cancelado','El importe ingresado es mayor al saldo','info');
            resetear();
        }
   
        if(importe != saldo) terminarFactura = 0; 

        Swal.fire({
            icon: 'question',
            title: 'Confirmar',
            text: '¿Deseas registrar el pago de esta factura?',
            showDenyButton: true,
            confirmButtonColor: '#3085d6',
            denyButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            denyButtonText: 'Cancelar',
            closeOnConfirm: false
            }).then((result) => {
                if (result.isConfirmed) {                    
                    window.livewire.emit('cobrar_factura',formaDePago,nroCompPago,importe,terminarFactura);
                } else if (result.isDenied) {
                    Swal.fire('Cancelado','Tu registro está a salvo :)','info')
                    resetear();
                }
            });            
    }
    function AnularFactura(id)
    {
        Swal.fire({
    		title: 'CONFIRMAR',
    		text: 'Antes de Anular la Factura, agrega un pequeño comentario del motivo que te lleva a realizar esta acción',
    		icon: 'warning',
			input: 'text',
    		showCancelButton: true,
    		confirmButtonText: 'Aceptar',
    		cancelButtonText: 'Cancelar',
    		closeOnConfirm: false,
			inputValidator: comentario => {
				if (!comentario) return "Por favor escribe un breve comentario";
				else return undefined;
			}
		}).then((result) => {
			if (result.isConfirmed) {
				if (result.value) {
					let comentario = result.value;
					Swal.fire(
						'Anulado!',
						'Tu registro se Anuló correctamente...',
						'success'
					);
					window.livewire.emit('anularFactura', id, comentario)
				}
			}else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire(
					'Cancelado',
					'Tu registro está a salvo :)',
					'error'
				)
            }
		})
    }
    function dejar_pendiente()
    {
        window.livewire.emit('dejar_pendiente')
    }
    function openModal(id)
    {
        $('#facturaId').val(id)
        $('#facturaId').hide()
        $('#cliente').val('Elegir')
        $('#empleado').val('Elegir')
        $('#modal').modal('show')
	}
	function save()
    {
        if($('#lista').val() != 3){     
            if($('#cliente option:selected').val() == 'Elegir') {
                toastr.error('Elige una opción válida para el Cliente')
                return;
            }
        }else{
            if($('#consignatario option:selected').val() == 'Elegir') {
                toastr.error('Elige una opción válida para el Cliente')
                return;
            }
        }
        if($('#modDelivery').val() == "1"){	
            if($('#empleado option:selected').val() == 'Elegir') {
                toastr.error('Elige una opción válida para el Repartidor')
                return;
            }
        }
        if($('#modDelivery').val() == "1"){	
            var data = JSON.stringify({
                'factura_id'   : $('#facturaId').val(),
                'cliente_id'   : $('#cliente option:selected').val(),
                'empleado_id'  : $('#empleado option:selected').val()
            });
        }else{
            var cliente = null;
            if($('#lista').val() != 3) cliente = $('#cliente option:selected').val();
            else cliente = $('#consignatario option:selected').val();
            var data = JSON.stringify({
                'factura_id'   : $('#facturaId').val(),
                'cliente_id'   : cliente,
                'empleado_id'  : "Salon"
            });
        }
        $('#modal').modal('hide')
        window.livewire.emit('modCliRep', data)
    }
    function openModalBancos()
    {
        $('#modalCheques').modal('hide')
        $('#banco').val('')
        $('#sucursal').val('')
        $('#modalBancos').modal('show')
	}
    function guardarBanco()
    {      
        if($('#descripcion').val() == '') {
            toastr.error('Ingresa un nombre válido para el Banco')
            return;
        }
        if($('#sucursal').val() == '') {
            toastr.error('Ingresa un nombre válido para la Sucursal')
            return;
        }
        var data = JSON.stringify({
            'banco'    : $('#descripcion').val(),
            'sucursal' : $('#sucursal').val()
        });
       
        $('#modalBancos').modal('hide');
        window.livewire.emit('agregarBanco', data);

        $('#formaDePago').val('1');
        $('#num').val('');
        $('#importe').val(Number.parseFloat($('#saldo').val()).toFixed(2));
    }
    function ocultar_sp()
    {
        window.livewire.emit('ocultar_sp')
    } 
    function mostrarInput()
    {
		$('[id="nroCompPago"]').val('');
		$('[id="num"]').val('');
     
		if($('[id="formaDePago"]').val() == '2' || $('[id="formaDePago"]').val() == '3'
				|| $('[id="formaDePago"]').val() == '4') {        
            $('[id="importeComp"]').val(Number.parseFloat($('[id="saldo"]').val()).toFixed(2));        
			$('#modalNroComprobanteDePago').modal('show');
        }else if($('[id="formaDePago"]').val() == '5'){
            if($('[id=clienteId]').val() && $('[id=clienteId]').val() != $('[id=esConsFinal]').val()){
                $('#modalCheques').modal('show');
            }else{
                Swal.fire({
                    position: 'center',
                    icon: 'info',
                    title: 'Primero debes cargar un Cliente!!',
                    showConfirmButton: false,
                    timer: 1500
                });
                $('[id="formaDePago"]').val(1);
            }
        }else if($('[id="formaDePago"]').val() == '6'){
            if($('[id=clienteId]').val() && $('[id=clienteId]').val() != $('[id=esConsFinal]').val()){
                Swal.fire({
                    icon: 'question',
                    title: 'Confirmar',
                    text: '¿Deseas enviar esta factura a Cuenta Corriente?',
                    showDenyButton: true,
                    confirmButtonColor: '#3085d6',
                    denyButtonColor: '#d33',
                    confirmButtonText: 'Aceptar',
                    denyButtonText: 'Cancelar',
                    closeOnConfirm: false
                    }).then((result) => {
                        if (result.isConfirmed) {                    
                            window.livewire.emit('factura_ctacte');
                        } else if (result.isDenied) {
                            Swal.fire('Cancelado','Tu registro está a salvo :)','info')
                            resetear();
                        }
                    });
            }else{
                Swal.fire({
                    position: 'center',
                    icon: 'info',
                    title: 'Primero debes cargar un Cliente!!',
                    showConfirmButton: false,
                    timer: 1500
                });
                $('[id="formaDePago"]').val(1);
            }
		}else{
			guardarDatosPago();
		}
	}
	function guardarDatosPago()
    {
        $('[id="num"]').val($('[id="nroCompPago"]').val());
		$('[id="importe"]').val(Number.parseFloat($('[id="importeComp"]').val()).toFixed(2));        
        $('[id="saldo"]').val(Number.parseFloat($('[id="saldo"]').val()).toFixed(2));
        var formaDePago = $('[id="formaDePago"]').val();
        var nroCompPago = $('[id="nroCompPago"]').val();
        var importe     = $('[id="importe"]').val();
        var saldo       = $('[id="saldo"]').val();
        
        if(importe > saldo){
            Swal.fire('Cancelado','El importe ingresado es mayor al saldo','info');
            resetear();
        } 
		window.livewire.emit('enviarDatosPago',formaDePago,nroCompPago,importe);
	}
	function guardarDatosCheque()
    {
        $('[id="saldo"]').val(Number.parseFloat($('[id="saldo"]').val()).toFixed(2));
        $('[id="importeCheque"]').val(Number.parseFloat($('[id="importeCheque"]').val()).toFixed(2));
        var saldo           = $('[id="saldo"]').val();
        var importe         = $('[id="importeCheque"]').val();
        var terminarFactura = 1;

        if(importe > saldo){
            Swal.fire('Cancelado','El importe ingresado es mayor al saldo','info');
            resetear();
        } 
        
        if(importe != saldo) terminarFactura = 0;

        var data = JSON.stringify({
                'banco'         : $('#banco').val(),
                'numero'        : $('#numCheque').val(),
                'fechaDeEmision': $('#fechaDeEmision').val(),
                'fechaDePago'   : $('#fechaDePago').val(),
                'importe'       : $('#importeCheque').val(),
                'cuitTitular'   : $('#cuitTitular').val(),
                'terminarFactura' : terminarFactura,
            });		
		window.livewire.emit('enviarDatosCheque', data);
        resetear();
	}
    function resetear()
    {
        $('#formaDePago').val('1');
        $('#num').val('');
        $('#importe').val(Number.parseFloat($('#saldo').val()).toFixed(2));
        return;
    }
   
    /////código para prolongar la session
    var keep_alive = false;
    $(document).bind("click keydown keyup mousemove", function() {
        keep_alive = true;
    });
    setInterval(function() {
        if ( keep_alive ) {
            pingServer();
            keep_alive = false;
        }
    }, 300000 );   //5 minutos
    function pingServer() {
        $.ajax('/keepAlive');
    }
    /////
    window.onload = function() {
        if($('#forzar_arqueo').val() == 1){		
            swal({
                title: 'Caja inhabilitada!',
                text: 'Existe un Arqueo General pendiente de cierre...',
                type: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Volver',
                closeOnConfirm: false
            },
            function() {  
                window.location.href="{{ url('notify') }}";
                swal.close()
		    })
        }
        if($('#caja_abierta').val() == 0){
            swal({
                title: 'Caja inhabilitada!',
                text: '',
                type: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Volver',
                closeOnConfirm: false
            },
            function() {  
                window.location.href="{{ url('notify') }}";
                swal.close()   
            })
        }
        Livewire.on('facturaCobrada',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Factura Cobrada!!',
                showConfirmButton: false,
                timer: 1500
            });
            if($('#ultima_factura').val() == 1){
                window.location.href="{{ url('notify') }}";
            }
		})          
        Livewire.on('facturaCtaCte',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Factura enviada a Cuenta Corriente!!',
                showConfirmButton: false,
                timer: 2500
            })
            setTimeout(function(){
                if($('#ultima_factura').val() == 1){
                    window.location.href="{{ url('notify') }}";
                }else window.location.href="{{ url('facturas') }}";
            },1000);
		})
        Livewire.on('limite_10',()=>{
            Swal.fire({
                position: 'center',
                icon: 'warning',
                iconColor: 'orange',
                title: 'Has llegado al límite de filas permitido!!',
                text: 'Para seguir agregardo filas debes configurar la impresión a 1 hoja',
                showConfirmButton: true
            })
		})
        Livewire.on('limite_superado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'warning',
                iconColor: 'orange',
                title: 'Has superado el límite de filas permitido!!',
                text: 'Para imprimir o seguir agregardo filas debes configurar la impresión a 1 hoja',
                showConfirmButton: true
            })
		})
        Livewire.on('limite_20',()=>{
            Swal.fire({
                position: 'center',
                icon: 'warning',
                iconColor: 'orange',
                title: 'Has llegado al límite de filas permitido!!',
                text: 'Has llegado al límite máximo de filas por factura. Para continuar deberás iniciar una factura nueva.',
                showConfirmButton: true
            })
        })
        Livewire.on('listaNro',(nro, texto)=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'En uso Lista ' + nro,
                text: texto,
                showConfirmButton: false,
                timer: 1500
            });
            if($('#comercioTipo').val() == 11) setTimeout(function(){openModal(null)},1500);
        })
        Livewire.on('stock_no_disponible',(ubicacion_stock , stock)=>{
            var texto = 'Solo restan ';
            var unidades = ' unidades';
            if(stock == 0 || stock == null){
                texto = 'Restan ';
                stock = '0';
            }else if(stock == 1){
                texto = 'Solo resta ';
                unidades = ' unidad';  
            } 
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Stock ' + ubicacion_stock + ' no disponible',
                text: texto + stock + unidades,
                showConfirmButton: true
            })
        })
        Livewire.on('cargar_consignatario',()=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Primero debes cargar un Consignatario!!',
                showConfirmButton: false,
                timer: 1500
            })
        })
        // Livewire.on('esConsFinal',()=>{
        //     Swal.fire({
        //         position: 'center',
        //         icon: 'info',
        //         title: 'Debes elegir un Cliente!!',
        //         showConfirmButton: false,
        //         timer: 1500
        //     })
        // })
        Livewire.on('bancoCreado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'El Banco fue creado!!',
                showConfirmButton: false,
                timer: 1500
            });
        })
        Livewire.on('chequeCreado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'El Cheque se registró correctamente!!',
                showConfirmButton: false,
                timer: 1500
            });
        })
        Livewire.on('cobroRegistrado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'El cobro a cuenta fue registrado!!',
                showConfirmButton: false,
                timer: 2000
            });            
            resetear();
        })
        Livewire.on('importeMayorQueSaldo',()=>{
            Swal.fire('Cancelado','El importe ingresado es mayor al saldo','info');
            return;
        })
    } 
</script>