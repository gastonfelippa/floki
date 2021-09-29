<div class="row layout-top-spacing justify-content-center">
    @if($action == 1)	
    @include('common.alerts')
    @include('common.messages')
    <div class="col-md-12 col-lg-6 layout-spacing"> 		
		<div class="widget-content-area br-4">
			<div class="widget-one widget-h">
                <div class="row">
                    <div class="col-md-6 text-left">
                        <h3>Remito N°: {{str_pad($numRemito, 6, '0', STR_PAD_LEFT)}}</h3>
                    </div>
                    
                </div>  
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="btn-group mb-2" role="group" aria-label="Basic mixed styles example">            
                        @if($inicio_remito) 
                         <button type="button" onclick="openModal({{$remito_id}})" 
                        class="btn btn-dark" enabled>
                        Clientes   
                    </button>           
                        @else       
                                    <button type="button" onclick="openModal({{$remito_id}})"
                                        class="btn btn-dark" enabled>
                                        Mod Cli/Rep                                         
                                    </button>
                                    <button type="button" onclick="dejar_pendiente()"
                                        class="btn btn-warning" enabled>
                                        Terminar
                                    </button>
                             
                                <button type="button" class="btn btn-success" enabled>
                                    <a href="{{url('pdfRemito',array($remito_id))}}" target="_blank">
                                    Imprimir</a>
                                </button>
                                <button type="button" onclick="AnularRemito({{$remito_id}})" 
                                    class="btn btn-info" enabled>
                                    Anular Remito  
                                </button>
                        @endif
                        </div>
                    </div>
                </div>
                <!-- si es delivery --> 
                @if($delivery == 1)          
                    @if($inicio_remito)   <!-- si es inicio de factura -->
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
                <!-- muestra datos del modal, no de la BD -->
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
                <div class="table-responsive scroll">
                    <table class="table table-hover table-checkable table-sm mb-4">
                        <thead>
                            <tr>
                                <th class="text-center">CÓDIGO</th>
                                <th class="text-center">CANTIDAD</th>
                                <th class="text-left">DESCRIPCIÓN</th>
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($info as $r)
                            <tr class="">
                                <td class="text-center">{{$r->codigo}}</td>
                                <td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
                                <td class="text-left">{{$r->producto}}</td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                        <li>
                                            <a href="javascript:void(0);" wire:click="edit({{$r->id}})" data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"          		
                                            onclick="Confirm('{{$r->id}}',{{$r->producto_id}},{{$r->cantidad}})"
                                            data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                        </li>
                                    </ul>
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
                       
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-2">
                            <label>Cantidad</label>
                            <input id="cantidad" wire:model.lazy="cantidad" onclick.keydown.enter="setfocus('barcode')" type="text" 
                                class="form-control form-control-sm text-center">
                        </div> 
                        <div class="form-group col-sm-12 col-md-3">
                            <label >Código</label>
                            <input id="barcode" wire:model.lazy="barcode"  type="text" 
                                onclick.keydown.enter="setfocus('guardar')" class="form-control form-control-sm">
                        </div>
                        <div class="form-group col-sm-12 col-md-4">
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
                       
                        <div class="form-group col-sm-12 col-md-2 mt-2" >
                            <button id="guardar" type="button" wire:click="StoreOrUpdateButton(0)" class="btn btn-primary mt-4">
                            Guardar</button>
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
                                <button style="width: 30%;height: 75px;" wire:click="StoreOrUpdateButton({{$a->id}})" type="button" class="btn btn-primary mb-1">{{$a->descripcion}}</button>
                            @endforeach 
                            @endif                   
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="caja_abierta" wire:model="caja_abierta">  
            <input type="hidden" id="forzar_arqueo" wire:model="forzar_arqueo">  
            <input type="hidden" id="ultima_factura" wire:model="ultima_factura"> 
        </div> 
    </div>
    @include('livewire.remitos.modal')       
    @else
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
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> 

<script type="text/javascript">
 	function Confirm(id, producto_id, cantidad)
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
                window.livewire.emit('deleteRow', id, producto_id, cantidad)  
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
                window.livewire.emit('elegirFormaDePago');
            } else if (result.isDenied) {
                if(delivery == 0) {
                    modalCtacte()
                }else {
                    var data = JSON.stringify({
                        'cliente_id' : idCli
                    });
                    window.livewire.emit('factura_ctacte', data)
                }
            }
        })
    }
    function factura_contado()
    { 
        if($('[id="formaDePago"]').val() != 1 && $('[id="nroCompPago"]').val() == ''){ 
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: 'Faltan datos, se cobrará como efectivo!!',
                showConfirmButton: false,
                timer: 1500
            })
            $('[id="formaDePago"]').val(1)
        }else{
            window.livewire.emit('factura_contado')
        }    
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
    } 
    function openModal(id)
    {
        $('#remitoId').val(id)
        $('#remitoId').hide()
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
            'remito_id'   : $('#remitoId').val(),
            'cliente_id'   : $('#cliente option:selected').val(),
            'empleado_id'  : $('#empleado option:selected').val()
        });
        $('#modal').modal('hide')
        window.livewire.emit('modCliRep', data)
    } 
    function mostrarInput()
    {		
		$('[id="nroCompPago"]').val('');
		$('[id="num"]').val('');
		if($('[id="formaDePago"]').val() == '2' || $('[id="formaDePago"]').val() == '3'
				|| $('[id="formaDePago"]').val() == '4' || $('[id="formaDePago"]').val() == '5') {
			$('#modalNroComprobanteDePago').modal('show');
		}else{
			guardarDatosPago();
		}
	}
	function guardarDatosPago()
    {
		$('[id="num"]').val($('[id="nroCompPago"]').val())
        if($('[id="num"]').val() != ''){
            var formaDePago = $('[id="formaDePago"]').val();
		    var nroCompPago = $('[id="nroCompPago"]').val();
        }else{
            $('[id="formaDePago"]').val(1)           
        }		
		window.livewire.emit('enviarDatosPago',formaDePago,nroCompPago);
	}
    window.onload = function() {
        Livewire.on('limite_10',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Tabla sobrepasada!!',
                text: 'Para seguir agregardo filas debes configurar la impresión a 1 hoja',
                showConfirmButton: true
            })
                //window.location.href="{{ url('notify') }}";
          
		})
        Livewire.on('limite_20',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Tabla sobrepasada!!',
                text: 'Has llegado al límite máximo de filas por factura. Para continuar deberás iniciar una factura nueva.',
                showConfirmButton: true
            })
        })
        Livewire.on('stock_no_disponible',(ubicacion_stock , stock)=>{
            var texto = 'Solo restan ';
            if(stock == 0) texto = 'Restan ';
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Stock ' + ubicacion_stock + ' no disponible',
                text: texto + stock + ' unidades',
                showConfirmButton: true
            })
        })
        Livewire.on('remitoTerminado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Remito grabado!!',
                showConfirmButton: false,
                timer: 1500
            })
        })
    } 
</script>