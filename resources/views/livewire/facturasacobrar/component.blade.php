<div class="row layout-top-spacing justify-content-center">	
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
								<td class="text-center">{{number_format($r->importe,2)}}</td>
								<td class="text-center">
                                    <ul class="table-controls">
                                        <li>
                                            <a href="javascript:void(0);" 
                                                wire:click="verDet('{{$r->id}}','{{$r->nomCli}}','{{$r->apeCli}}')" 
                                                data-toggle="tooltip" data-placement="top" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"          		
        	                                    onclick="Confirm('{{$r->id}}')"
        	                                    data-toggle="tooltip" data-placement="top" title="Eliminar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"          		
                                                onclick="Cobrar('{{$r->id}}',{{$r->cliente_id}})"
        	                                    data-toggle="tooltip" data-placement="top" title="Cobrar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign text-dark"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                        </li>                             
                                        <li>
                                            <a href="{{ url('pdfFactDel',array($r->id))}}"          		
        	                                    data-toggle="tooltip" data-placement="top" title="Imprimir">
                                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity text-warning"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg> 
                                        </li>
                                        <li>
                                            @if($r->comentario <> null)
                                            <a href="javascript:void(0);" 
                                                onclick="openModalComentario({{$r->id}},'{{$r->comentario}}',1)"        		
        	                                    data-toggle="tooltip" data-placement="top" title="Con Comentario">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle text-danger"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                            @else
                                            <a href="javascript:void(0);" 
                                                onclick="openModalComentario({{$r->id}},'{{$r->comentario}}',0)"        		
        	                                    data-toggle="tooltip" data-placement="top" title="Sin Comentario">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                            @endif
                                        </li>
                                        <li>
                                            @if($r->estado_entrega == '3')
                                            <a href="javascript:void(0);"        		
        	                                    data-toggle="tooltip" data-placement="top" title="Entregado">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck text-success"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                                            @elseif($r->estado_entrega == '2')
                                            <a href="javascript:void(0);" 
                                                onclick="EstadoPedido({{$r->id}},2)"        		
        	                                    data-toggle="tooltip" data-placement="top" title="En Camino">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck text-danger"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                                            @else
                                            <a href="javascript:void(0);" 
                                                onclick="EstadoPedido({{$r->id}},1)"        		
        	                                    data-toggle="tooltip" data-placement="top" title="En Espera">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                                            @endif
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
    function ConfirmDel(id)
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
			window.livewire.emit('deleteRowDel', id)    
			toastr.success('info', 'Registro eliminado con éxito')
			swal.close()   
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

</script>