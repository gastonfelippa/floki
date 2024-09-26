<div class="row layout-top-spacing justify-content-center">	
    <div class="col-sm-12 col-md-6 layout-spacing"> 		
		<div class="widget-content-area br-4">
			<div class="widget-one">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <h5><b>Arqueo Caja Repartidor</b></h5>  
                        @can('HabilitarCaja_index')     
                            <select wire:model="repartidor" class="form-control form-control-sm">
                                <option value="Elegir">Elegir</option>
                                @foreach($empleados as $t)
                                <option value="{{ $t->id }}">
                                    {{$t->apellido}} {{$t->name}}                        
                                </option> 
                                @endforeach                               
                            </select>
                        @else
                            <input class="form-control form-control-sm" value="{{auth()->user()->apellido}} {{auth()->user()->name}}">
                        @endcan 
                        <div class="row mt-1">
                            <div class="col-7">
                                <b>Fecha: {{$fecha_inicio->format('d-m-Y')}}</b>
                            </div>
                            @can('Facturas_index')
                                @if($info->count() > 0)
                                    <div class="col-5">
                                        <span class="badge badge-primary mb-1" 
                                        onclick="CobrarTodas('{{$repartidor}}','{{$nomRep}}')">Cobrar Todas</span>
                                    </div>
                                @endif
                            @endcan
                        </div> 
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="row mb-1">
                            <div class="col-6">
                                <b>Caja Inicial</b>
                            </div>
                            <div class="col-2 text-right">
                                <b>$</b>
                            </div>
                            <div class="col-4 text-right">
                                <b>{{number_format($totalCI,2,',','.')}}</b>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-6">
                                <b>Total Cobranzas</b> 
                            </div>
                            <div class="col-2 text-right">
                                <b>$</b>
                            </div>
                            <div class="col-4 text-right">
                                <b>{{number_format($totalCobrado,2,',','.')}}</b>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-6">            
                                @can('Facturas_index')
                                    @if($repartidor == '0')
                                    <span class="badge badge-warning mr-1">...</span>
                                    @else
                                    <span class="badge badge-warning mr-1" 
                                    onclick="openModal(0, '{{$nomRep}}')" >...</span>
                                    @endif
                                @endcan 
                                <b>Total Gastos</b>           
                            </div>
                            <div class="col-2 text-right">
                                <b>$</b>
                            </div>
                            <div class="col-4 text-right">
                                <b>-{{number_format($totalGastos,2,',','.')}}</b>
                            </div>                        
                        </div> 
                        <div class="row mb-1" style="color: #ff7f26">
                            <div class="col-6">
                                <b>CAJA FINAL</b>
                            </div>
                            <div class="col-2 text-right">
                                <b>$</b>
                            </div>
                            <div class="col-4 text-right">
                                <b>{{number_format($totalCF,2,',','.')}}</b>
                            </div>
                        </div>
                    </div>
                </div>
				@include('common.alerts')
				<div class="table-resposive scroll">
					<table class="table table-hover table-checkable table-sm">
						<thead>
							<tr>
								<th class="text-left">CLIENTE</th>
								<th class="text-center">IMPORTE</th>
								<th class="text-center">CAJA/FACTURADORA</th>
								<th class="text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($info as $r)
							<tr>
								<td class="text-left">{{$r->apeCli}} {{$r->nomCli}}</td>
								<td class="text-center">{{number_format($r->importe,2,',','.')}}</td>
                                <td class="text-left">{{$r->nombreCaja}}</td>
								<td class="text-right">
                                    <ul class="table-controls">
                                        @can('HabilitarCaja_index')
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
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"          		
                                                onclick="Cobrar('{{$r->id}}',{{$r->cliente_id}})"
        	                                    data-toggle="tooltip" data-placement="top" title="Cobrar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign text-dark"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></a>
                                        </li> -->
                                        @endcan                              
                                        <li>
                                            <a href="{{ url('pdfFactDel',array($r->id))}}" target="_blank"          		
        	                                    data-toggle="tooltip" data-placement="top" title="Imprimir">
                                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity text-warning"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg></a> 
                                        </li>
                                        <li>
                                            @if($r->comentario <> null)
                                            <a href="javascript:void(0);" 
                                                onclick="openModalComentario({{$r->id}},'{{$r->comentario}}',1)"        		
        	                                    data-toggle="tooltip" data-placement="top" title="Con Comentario">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chat-text text-danger" viewBox="0 0 16 16"><path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.807 7-6 0-3.192-3.004-6-7-6S1 4.808 1 8c0 1.468.617 2.83 1.678 3.894zm-.493 3.905a21.682 21.682 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a9.68 9.68 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105z"/><path d="M4 5.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zM4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8zm0 2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5z"/></svg></a>
                                            @else
                                            <a href="javascript:void(0);" 
                                                onclick="openModalComentario({{$r->id}},'{{$r->comentario}}',0)"        		
        	                                    data-toggle="tooltip" data-placement="top" title="Sin Comentario">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg></a>
                                            @endif
                                        </li>
                                        <li>
                                            @if($r->estado_entrega == '3')
                                            <a href="javascript:void(0);"        		
        	                                    data-toggle="tooltip" data-placement="top" title="Entregado">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck text-success"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg></a>
                                            @elseif($r->estado_entrega == '2')
                                            <a href="javascript:void(0);" 
                                                onclick="EstadoPedido({{$r->id}},2)"        		
        	                                    data-toggle="tooltip" data-placement="top" title="En Camino">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck text-danger"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg></a>
                                            @else
                                            <a href="javascript:void(0);" 
                                                onclick="EstadoPedido({{$r->id}},1)"        		
        	                                    data-toggle="tooltip" data-placement="top" title="En Espera">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg></a>
                                            @endif
                                        </li>
                                    </ul>
                                </td>  
							</tr>
							@endforeach
						</tbody>
					</table>                   
				</div>
                @include('livewire.cajarepartidor.modal')	
                @include('livewire.cajarepartidor.modalGastos')	
            </div>
            @can('HabilitarCaja_index')  
            <input type="hidden" id="caja_abierta" value="1"> 
            @else
            <input type="hidden" id="caja_abierta" wire:model="caja_abierta"> 
            @endcan
		</div>
	</div>
    @include('livewire.cajarepartidor.modalComentario')	
    @if($action == 2)
    @include('livewire.cajarepartidor.detalle')		
    @endif    
</div>

<style type="text/css" scoped>
.scroll{
    position: relative;
    height: 255px;
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
    function Cobrar(id,idCli)
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
                    window.livewire.emit('factura_contado',id)
                    Swal.fire('Factura Cobrada!', '', 'success')
                }else if (result.isDenied) {
                    window.livewire.emit('factura_ctacte',id,idCli)
                    Swal.fire('Factura Cuenta Corriente', '', 'success')                   
                }
            })
    } 
    function CobrarTodas(repId, nomRep)
    {
       let me = this
        swal({
        title: 'CONFIRMAR',
        text: '¿Deseas COBRAR todas las facturas del Repartidor \n'+ nomRep +'?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        closeOnConfirm: false
        },
		function() {
			window.livewire.emit('cobrarTodas', repId)    
			//toastr.success('info', 'Facturas cobradas con éxito!! Caja Cerrada...')
			swal.close()   
        })
    } 
    function EstadoPedido(id,estado)    //1- verde    2-rojo
    {
        if(estado == 1){
            swal({
            title: 'CONFIRMAR',
            text: '¿Deseas marcar este Pedido como "EN CAMINO"?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false
            },
            function() {
                window.livewire.emit('marcarEstadoPedido', id, '2')    
                toastr.success('info', 'Marcado...')
                swal.close()   
            })
        }else{
            swal({
            title: 'CONFIRMAR',
            text: '¿Deseas marcar este Pedido como "ENTREGADO"?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false
            },
		    function() {
			    window.livewire.emit('marcarEstadoPedido', id, '3')    
			    toastr.success('info', 'Marcado...')
			    swal.close()   
            })
        }
    }     
	function setfocus($id) 
    {
        document.getElementById($id).focus();
    } 
    function openModal(caja, nomRep)
    {
        $('#importe').val('')
        $('#gasto').val('Elegir')
        if(caja == 1){
            $('#concepto').hide()
            $('#modalGastos').hide()
            $('#modalCaja').show()
            $('.modal-title').text('Agregar Caja')
        }else{
            $('#modalCaja').hide() 
            $('#concepto').show()  
            $('#modalGastos').show()
            $('.modal-title').text('Agregar Gastos a Caja: ' + nomRep)
        }
        $('#modalCajaRep').modal('show')
    }
    function save()
    {
        if($.trim($('#importe').val()) == '')
        {
            toastr.error('Ingresa un importe válido')
            return;
        }
        if($('#gasto option:selected').val() == 'Elegir')
        {
            toastr.error('Elige una opción válida para el Gasto')
            return;
        }
        var data = JSON.stringify({
            'importe'   : $('#importe').val(),            
            'gasto'     : $('#gasto option:selected').val()
        });
        window.livewire.emit('grabarGastosModal', data)
    } 
    function openModal2(ing_egr, editar, mov_id, ing_egr_id, importe, nomRep)
    {
        if(editar == 1){
            $('.modal-title').text('Editar Egreso')
            $('#egreso').val(ing_egr_id)
            $('#mov_importe').val(importe)
            $('#mov_id').val(mov_id)
            $('#edit_ing_egr').val(1)
        }else if(editar == 2){
            $('.modal-title').text('Editar Otro Ingreso')
            $('#egreso').val(ing_egr_id)
            $('#mov_importe').val(importe)
            $('#mov_id').val(mov_id)
            $('#edit_ing_egr').val(2)
        }else{
            if(ing_egr == 1){
                $('.modal-title').text('Agregar Egreso: Caja '+ nomRep)
            }else{
                $('.modal-title').text('Agregar Otro Ingreso')
            } 
            $('#egreso').val('Elegir')
            $('#mov_importe').val('')
            $('#mov_id').val(0)
            $('#edit_ing_egr').val(0)
        }
        $('#modalAddMov').modal('show')
	}
	function save2()
    {
        if($('#egreso option:selected').val() == 'Elegir') {
            toastr.error('Elige una opción válida para el Egreso')
            return;
        }
        if($('#mov_importe').val() == '') {
            toastr.error('El campo Importe no puede estar vacío')
            return;
        }
        var data = JSON.stringify({
            'ing_egr_id'  : $('#egreso option:selected').val(),
            'mov_importe': $('#mov_importe').val(),
            'mov_id': $('#mov_id').val(),
            'edit_ing_egr': $('#edit_ing_egr').val(),
        });

        $('#modalAddMov').modal('hide')
        window.livewire.emit('createFromModal', data)
    }           
    function openModalComentario(id,comentario,edit)
    {
        //console.log(id,comentario,edit)
        if(edit == 0){
            $('.modal-title').text('Agregar Comentario')
            $('#btnEliminar').hide()
            $('#btnGuardar').text('Guardar')
        }else{
            $('.modal-title').text('Ver/Editar Comentario')
            $('#btnEliminar').show()
            $('#btnGuardar').text('Modificar')
        } 
        $('#factura_id').val(id)
        $('#comentario').val(comentario)
        $('#modalComentario').modal('show')
    } 
    function saveComentario(accion)
    {
        if($.trim($('#comentario').val()) == '')
        {
            toastr.error('Ingresa un Texto válido')
            return;
        }
        var data = JSON.stringify({
            'factura_id' : $('#factura_id').val(),
            'comentario' : $('#comentario').val(),
            'accion'     : accion     
        });
        window.livewire.emit('grabarComentarioModal', data)
    }   
    document.addEventListener('DOMContentLoaded', function(){
        window.livewire.on('msgok', dataMsg => {
            $('#modalCajaRep').modal('hide')
        })
        window.livewire.on('msgerror', dataMsg => {
            $('#modalCajaRep').modal('hide')
        })
    });
    window.onload = function(){
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
		Livewire.on('facturas_cobradas',()=>{
            Swal.fire(
                'Facturas Cobradas!',
                'Para Cerrar la Caja debes ir al Arqueo de Caja Usuarios...',
                'success'
            ).then((result) => {
                if (result.isConfirmed) {
                    window.location.href="{{ url('arqueodecaja') }}";
                }
            });
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
    }
</script>