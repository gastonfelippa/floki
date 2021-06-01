<div class="row layout-top-spacing">	
    <div class="col-md-12 col-lg-6 layout-spacing"> 		
		<div class="widget-content-area br-4">
			<div class="widget-one widget-h">
                <div class="row">
                    <div class="col-md-6">
                        <h3>Factura N°: {{str_pad($numFactura, 6, '0', STR_PAD_LEFT)}}</h3>
                    </div>
                    <div class="col-md-6 text-center">
                        <h3 class="bg-danger" style="border-radius: 5px;">Total : $ {{number_format($total,2,',','.')}}</h3> 
                    </div>
                </div>  
                <div class="row">
                    <div class="col-md-3">
                        <p style="font-size:14px;">Fecha {{\Carbon\Carbon::now()->format('d-m-Y')}} <p>
                    </div>                    
                    <div class="col-md-9 text-right">
                        <div class="btn-group mb-2" role="group" aria-label="Basic mixed styles example">            
                            @if($total == 0)
                                <button type="button" onclick="openModal({{$factura_id}})" onclick="setfocus('barcode')" 
                                    class="btn btn-dark" enabled>
                                    Delivery   
                                </button>        
                                <button type="button" class="btn btn-warning" disabled>Dejar Pendiente</button>                    
                                <button type="button" class="btn btn-primary" disabled>Cobrar</button>
                                <button type="button" class="btn btn-success" disabled>Imprimir</button>
                            @else
                                @if($delivery == 0)
                                    <button type="button" onclick="openModal({{$factura_id}})" onclick="setfocus('barcode')" 
                                        class="btn btn-dark" enabled>
                                        Delivery   
                                    </button>
                                    <button type="button" class="btn btn-warning" disabled>
                                        Dejar Pendiente
                                    </button>
                                @else
                                    <button type="button" onclick="openModal({{$factura_id}})"
                                        class="btn btn-dark" enabled>
                                        Mod Cli/Rep                                         
                                    </button>
                                    <button type="button" wire:click.prevent="dejar_pendiente()"
                                        class="btn btn-warning" enabled>
                                        Dejar Pendiente
                                    </button>
                                @endif 
                                <button type="button" onclick="Cobrar({{$delivery}},{{$clienteId}})" 
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
                <!-- si es delivery y es inicio de factura -->
            @if($delivery == 1)          
                @if($total == 0)   
                    <div class="row mt-2">
                        <div class="col-6">
                            <h6>Cliente:  {{$apeNomCli}}</h6>
                        </div>
                        <div class="col-6">
                            <h6>Rep:  {{$apeNomRep}}</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h6>Dirección:  {{$dirCliente}}</h6>
                        </div>
                        <div class="col-6">
                            @if($saldoCtaCte < 0)
                                <h6 style="color:red">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6>   
                            @else
                                <h6 style="color:green">Saldo Cta. Cte.:<b> {{number_format($saldoCtaCte,2,',','.')}}</b></h6> 
                            @endif
                        </div>
                    </div>       
                @else
                <!-- si muestra datos en BD de la factura -->
                    <div class="row mt-2">
                        <div class="col-6">
                            <h6>Cliente:  {{$encabezado[0]->apeCli}} {{$encabezado[0]->nomCli}}</h6>
                        </div>
                        <div class="col-6">
                            <h6>Rep:  {{$encabezado[0]->apeRep}} {{$encabezado[0]->nomRep}}</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h6>Dirección:  {{$encabezado[0]->calle}} {{$encabezado[0]->numero}}</h6>
                        </div>
                        <div class="col-6">
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
                        <div class="col-6">
                            <h6>Cliente:  {{$apeNomCli}}</h6>
                        </div>
                        <div class="col-6">
                            <h6>Rep:  {{$apeNomRep}}</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h6>Dirección:  {{$dirCliente}}</h6>
                        </div>
                        <div class="col-6">
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
								<th class="text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($info as $r)
							<tr class="">
								<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
								<td class="text-left">{{$r->producto}}</td>
								<td class="text-right">{{$r->precio}}</td>
								<td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
								<td class="text-center">
									@include('common.actions', ['edit' => 'Facturas_edit_item', 'destroy' => 'Facturas_destroy_item'])
								</td>
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
                <form>
                    @include('common.messages')    
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-2">
                            <label>Cantidad</label>
                            <input id="cantidad" wire:model.lazy="cantidad" onclick.keydown.enter="setfocus('barcode')" type="text" 
                                class="form-control form-control-sm text-center">
                        </div> 
                        <div class="form-group col-sm-12 col-md-2">
                            <label >Código</label>
                            <input id="barcode" wire:model.lazy="barcode"  type="text" 
                                onclick.keydown.enter="setfocus('guardar')" class="form-control form-control-sm">
                        </div>
                        <div class="form-group col-sm-12 col-md-3">
                            <label>Producto</label>
                            <select id="producto" wire:model="producto" class="form-control form-control-sm text-center">
                                <option value="Elegir" >Elegir</option>
                                @foreach($productos as $t)
                                <option value="{{ $t->id }}">
                                    {{$t->descripcion}}                         
                                </option> 
                                @endforeach                               
                            </select>			               
                        </div>            
                        <div class="form-group col-sm-12 col-md-3">
                            <label>P/Unitario</label>
                            <input wire:model.lazy="precio" type="text" class="form-control form-control-sm text-right" disabled>
                        </div>
                        <div class="form-group col-sm-12 col-md-2 mt-2">
                            <label></label>
                            <button id="guardar" type="button" wire:click="StoreOrUpdate('0')" onclick="setfocus('barcode')" class="btn btn-primary">
                                 Guardar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-sm-12 col-lg-4">
                <div class="widget-content-area">
                    @if($categorias->count() > 6)
                        <div class="widget-one scrollb"> 
                            <div class="scrollContent"> 
                                @foreach($categorias as $c)                    
                                    <button style="width: 100%;"  wire:click.prevent="buscarArticulo({{$c->id}})" type="button" class="btn btn-warning mb-1">{{$c->descripcion}}</button>
                                @endforeach
                            </div>
                        </div>
                    @else                   
                        @foreach($categorias as $c)                    
                            <button style="width: 100%;"  wire:click.prevent="buscarArticulo({{$c->id}})" type="button" class="btn btn-warning mb-1">{{$c->descripcion}}</button>
                        @endforeach                       
                    @endif
                </div>
            </div>
            <div class="col-sm-12 col-lg-8">
                <div class="widget-content-area">
                    <div class="widget-one scrollb"> 
                        <div class="scrollContent"> 
                            @if($articulos != null)
                            @foreach($articulos as $a)                    
                                <button style="width: 30%;height: 75px;" wire:click="StoreOrUpdate('{{$a->id}}')" type="button" class="btn btn-primary mb-1">{{$a->descripcion}}</button>
                            @endforeach 
                            @endif                   
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="caja_abierta" wire:model="caja_abierta">  
        </div>         
    @include('livewire.facturas.modal')  
    @include('livewire.facturas.modalCtacte')  
    </div> 
</div>

<style type="text/css" scoped>
    .widget-h{
        position: relative;
        height:375px;
        overflow: hidden;
    }
    .scroll{
        position: relative;
        max-height: 220px;
        margin-top: .5rem;
        overflow: auto;
    }
    .scrollb {
        width: 100%;
        height:240px;
        overflow:hidden;
    }
    .scrollContent{
        width: 108%;
        height:240px;
        overflow-y:auto;
        overflow-x:hidden;
    }
</style>



<script type="text/javascript">
 	function Confirm(id)
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
                window.livewire.emit('deleteRow', id)    
                toastr.success('info', 'Registro eliminado con éxito')
                swal.close()   
            })
    }
    function Cobrar(delivery, idCli)
    {
        Swal.fire({
            title: 'Elige una opción...',
            showDenyButton: true,
            showCancelButton: true,
            cancelButtonText: `Cancelar`,
            confirmButtonText: `Contado`,
            denyButtonText: `Cuenta Corriente`,
        }).then((result) => {
            if (result.isConfirmed) {
                window.livewire.emit('factura_contado')
                Swal.fire('Factura Cobrada!', '', 'success')
            } else if (result.isDenied) {
                if(delivery == 0) {
                    modalCtacte()
                }else {
                    var data = JSON.stringify({
                        'cliente_id' : idCli
                    });
                    window.livewire.emit('factura_ctacte', data)
                    Swal.fire('Factura Cuenta Corriente', '', 'success')
                }
            }
        })
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
    function modalCtacte()
    {
        $('#cliente2').val('Elegir')
        $('#modalCtacte').modal('show')
	}
	function saveCtacte()
    {     
        if($('#cliente2 option:selected').val() == 'Elegir') {
            toastr.error('Elige una opción válida para el Cliente')
            return;
        }
        var data = JSON.stringify({
            'cliente_id'   : $('#cliente2 option:selected').val()
        });
        $('#modalCtacte').modal('hide')
        window.livewire.emit('factura_ctacte', data)
        Swal.fire('Factura Cuenta Corriente', '', 'success')
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
        if($('#cliente option:selected').val() == 'Elegir') {
            toastr.error('Elige una opción válida para el Cliente')
            return;
        }
        if($('#empleado option:selected').val() == 'Elegir') {
            toastr.error('Elige una opción válida para el Repartidor')
            return;
        }
        var data = JSON.stringify({
            'factura_id'   : $('#facturaId').val(),
            'cliente_id'   : $('#cliente option:selected').val(),
            'empleado_id'  : $('#empleado option:selected').val()
        });
        $('#modal').modal('hide')
        window.livewire.emit('modCliRep', data)
    } 

    window.onload = function() {
        if($('#caja_abierta').val() == 0){
            swal({
                title: 'Oops',
                text: 'Para registrar una Venta, primero debés habilitar una Caja. Para ello dirigite a: Caja->Habilitar Cajas',
                type: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Aceptar',
                closeOnConfirm: false
            },
            function() {  
                window.location.href="{{ url('home') }}";
                swal.close()   
            })
        }
    }
  
</script>