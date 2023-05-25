<div class="row layout-top-spacing justify-content-center">	
    @if($action == 1)
    <div class="col-sm-12 col-md-6 layout-spacing"> 		
		<div class="widget-content-area br-4">
			<div class="widget-one">
            <div class="row">
    				<div class="col-xl-12 text-center">
    					<h3><b>Facturas a Cobrar</b></h3>
    				</div> 
    			</div>
				@include('common.alerts')
				<div class="table-resposive scroll">
					<table class="table table-hover table-checkable table-sm">
						<thead>
							<tr>
								<th class="text-center">CLIENTE</th>
								<th class="text-center">IMPORTE</th>
								<th class="text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($info as $r)
							<tr>
								<td class="text-left">{{$r->apeCli}} {{$r->nomCli}}</td>
								<td class="text-center">{{number_format($r->importe,2,',','.')}}</td>
								<td class="text-center">
                                    <ul class="table-controls">
                                        <li>
                                            <a href="javascript:void(0);" 
                                                wire:click="verDet({{$r->id}})" 
                                                data-toggle="tooltip" data-placement="top" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                        </li>
                                        <!-- <li>
                                            <a href="javascript:void(0);"          		
        	                                    onclick="AnularFactura('{{$r->id}}')"
        	                                    data-toggle="tooltip" data-placement="top" title="Anular">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                        </li> -->
                                        <!-- <li>
                                            <a href="javascript:void(0);"          		
                                                onclick="Cobrar('{{$r->id}}',{{$r->cliente_id}},{{$r->importe}})"
        	                                    data-toggle="tooltip" data-placement="top" title="Cobrar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign text-dark"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                        </li>                             
                                        <li>
                                            <a href="{{ url('pdfFactDel',array($r->id))}}" target="_blank"          		
        	                                    data-toggle="tooltip" data-placement="top" title="Imprimir">
                                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity text-warning"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg> 
                                        </li> -->
                                    </ul>
                                </td>  
							</tr>
							@endforeach
						</tbody>
					</table>                   
				</div>
            </div>
            <input type="hidden" id="caja_abierta" wire:model="caja_abierta"> 
		</div>
	</div>
    @elseif($action == 2)
    @include('livewire.facturasacobrar.detalle')		
    @else    
    @include('livewire.facturasacobrar.formaDePago')  
    @include('livewire.facturasacobrar.modalNroCompPago')  
	@endif 
</div>

<style type="text/css" scoped>
.scroll{
    position: relative;
    height: 250px;
    margin-top: .5rem;
    overflow: auto;
}
.scrollmodal{
    position: relative;
    height: 130px;
    margin-top: .5rem;
    overflow: auto;
}
</style>



<script>
    function ConfirmDel(id)
    {
        Swal.fire({
    		title: 'CONFIRMAR',
    		text: 'Antes de Eliminar el registro, agrega un pequeño comentario del motivo que te lleva a realizar esta acción',
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
					window.livewire.emit('eliminarRegistro', id, comentario)
				}
			}else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire('Cancelado', 'Tu registro está a salvo :)', 'error')
            }
		})
    }
    function Cobrar(id,idCli,total)
    {
        Swal.fire({
            title: 'Elige una opción...',
            showDenyButton: true,
            showCancelButton: true,
            cancelButtonText: `Cancelar`,
            confirmButtonText: `Contado`,
            denyButtonText: `Cuenta Corriente`,
        }).then((result) => {
            if(result.isConfirmed) {
                window.livewire.emit('elegirFormaDePago', id, idCli, total);
            }else if (result.isDenied) {
                var data = JSON.stringify({
                    'factura_id' : id,
                    'cliente_id' : idCli,
                    'total' : total
                });
                window.livewire.emit('factura_ctacte', data)                                  
            }
        })
    }
    function mostrarInput(){		
		$('[id="nroCompPago"]').val('');
		$('[id="num"]').val('');
		if($('[id="formaDePago"]').val() == '2' || $('[id="formaDePago"]').val() == '3'
				|| $('[id="formaDePago"]').val() == '4' || $('[id="formaDePago"]').val() == '5') {
			$('#modalNroComprobanteDePago').modal('show');
		}else{
			guardarDatosPago();
		}
	}
	function guardarDatosPago(){
		$('[id="num"]').val($('[id="nroCompPago"]').val())
        if($('[id="num"]').val() != ''){
            var formaDePago = $('[id="formaDePago"]').val();
            var nroCompPago = $('[id="nroCompPago"]').val();
        }else{
            $('[id="formaDePago"]').val(1)           
        }    
		window.livewire.emit('enviarDatosPago',formaDePago,nroCompPago);
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
    window.onload = function() {
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
            })
		})         
        Livewire.on('facturaCtaCte',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Factura enviada a Cuenta Corriente!!',
                showConfirmButton: false,
                timer: 1500
            })
		})         
        Livewire.on('eliminarRegistro',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Eliminado!',
                text: 'Tu registro se Eliminó correctamente...',
                showConfirmButton: false,
                timer: 1500
            })
		}) 
        Livewire.on('esConsFinal',()=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Debes elegir un Cliente!!',
                showConfirmButton: false,
                timer: 1500
            })
        })        
    }  

</script>