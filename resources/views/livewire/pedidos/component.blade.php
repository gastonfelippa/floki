<div class="row layout-top-spacing justify-content-center">  
	@include('common.alerts')
	@if($action == 1)  
    <div class="col-sm-12 col-md-8 layout-spacing">      
    	<div class="widget-content-area">
    		<div class="widget-one">
				<div class="row layout-content-between">
                	<div class="col-md-6 col-sm-12">
    					<h3><b>Pedidos</b></h3>
					</div> 
					<div class="col-md-6 col-sm-12 mt-1">
						<div class="form-check form-check-inline p-1">
							<input class="form-check-input" type="radio" wire:model="estadoPedido" value="cargado" checked>
							A realizar</div>
						<div class="form-check form-check-inline p-1">
							<input class="form-check-input" type="radio" wire:model="estadoPedido" value="pedido">
							A recibir</div>
						<div class="form-check form-check-inline p-1">
							<input class="form-check-input" type="radio" wire:model="estadoPedido" value="recibido">
							Recibidos</div>
					</div> 
    			</div>
				<div class="row justify-content-between mb-3">
					<div class="col-8 mb-1">
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
							</div>
							<input id="search" type="text" wire:model="search" class="form-control form-control-sm" placeholder="Buscar.." aria-label="notification" aria-describedby="basic-addon1" autocomplete="off">
						</div>
					</div>
					@can('Clientes_create')
					<div class="col-4 mt-1">
						<button id="btnNuevo" type="button" wire:click="doAction(3)" class="btn btn-danger btn-block">
							<span style="text-decoration: underline;">N</span>uevo
						</button>
					</div>
					@endcan
				</div> 		
				<div class="table-responsive scroll">
					@if($pedidoPor == 'producto')
					<table class="table table-hover table-checkable table-sm">
						<thead>
							<tr>
								<th class="text-center">REPONER</th>
								<th class="text-left">PRODUCTO</th>
								<th class="text-center">PRECIO</th>
								<th class="text-center">PROVEEDOR</th>
								<th class="text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($infoStock as $r)
							<tr>
								<!-- @if($r->diferencia < 6) -->
								<td class="text-center" style="background:#F09F8F;">{{$r->cantidad_pedido}}</td>
                                <td style="background:#F09F8F;">{{$r->descripcion}}</td>
                                <td style="background:#F09F8F;">{{$r->precio}}</td>
								<td>{{$r->nombre_empresa}}</td>
								<td class="text-center">
									<ul class="table-controls">
										<li>
											<a href="javascript:void(0);"          		
											data-toggle="tooltip" data-placement="top" title="Item pedido">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" style="font-weight: bold;" class="bi bi-check2 text-danger" viewBox="0 0 16 16"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg></a>
											
										</li>
									</ul>
								</td>
								<!-- @endif -->
							</tr>
							@endforeach
						</tbody>
					</table>
					@else
					<table class="table table-hover table-checkable table-sm">
						<thead>
							<tr>
								<th class="text-left">PROVEEDOR</th>
								<th class="text-center">FECHA CONFECCIÓN</th>
								<th class="text-center">IMPORTE</th>
								<th class="text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($info as $r)
							<tr>
								<td>{{$r->nombre_empresa}}</td>
								<td class="text-center">{{\Carbon\Carbon::parse(strtotime($r->created_at))->format('d-m-Y')}}</td>
								<td class="text-center">{{number_format($r->importe,2)}}</td>
								<td class="text-center">
									<ul class="table-controls">
										@if($estadoPedido == "pedido" || $estadoPedido == "recibido")
										<li>
											<a href="javascript:void(0);" 
											wire:click="edit({{$r->proveedor_id}})" 
											data-toggle="tooltip" data-placement="top" title="Ver">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>                                 
										</li>
										@else
										<li>
											<a href="javascript:void(0);" 
											wire:click="edit({{$r->proveedor_id}})" 
											data-toggle="tooltip" data-placement="top" title="Ver/Editar">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>                                 
											</a>
											<!-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a> -->
										</li>
										<li>
											<a href="javascript:void(0);"          		
											onclick="Confirm('{{$r->id}}')"
											data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
										</li>
										@endif
									</ul>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					@endif
				</div>
			</div>
    	</div> 
    </div>
	@else
	@can('Categorias_create')
	@include('livewire.pedidos.form')	
	@include('livewire.pedidos.modal')	
	@endcan
	@endif
</div>

<style type="text/css" scoped>
	.scroll{
		position: relative;
		height: 270px;
		margin-top: .5rem;
		overflow: auto;
	}
	.scrollHistorial{
		position: relative;
		height: 120px;
		margin-top: .5rem;
		overflow: auto;
	}
	.scrollHistorialCorto{
		position: relative;
		height: 0px;
		margin-top: .5rem;
		overflow: auto;
	}
	.encabezado{
		background:#B0B8B5;
	}
	.contenido{
		background:#BEC9C5;
	}
</style>

<script type="text/javascript">
	function cambiar_color(celda){
		celda.style.backgroundColor="#B7F08F"
	}
	function openModal(id,producto,cantidad,modificar)
    {    
		$('#producto_id').val(id);
		$('#cantidad_a_reponer').val(cantidad);
		if(modificar == 1){
			$('#selected_id').val(id);
			$('.modal-title').text('Modificar: ' + producto);
		}else{
			$('#selected_id').val('');
			$('.modal-title').text('Producto: ' + producto);
		}
		$('#modal').modal('show');  
	}
	function save()
    {
        var id = $('#producto_id').val();
        var cantidad = $('#cantidad_a_reponer').val();
        var selectedId = $('#selected_id').val();
        if(cantidad > 0) window.livewire.emit('verificarProducto',id,cantidad,selectedId);

        $('#modal').modal('hide'); 
    } 
    function Confirm(id)
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
					window.livewire.emit('deleteRow', id, comentario)
				}
			}else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire('Cancelado', 'Tu registro está a salvo :)', 'error')
            }
		})
    }
	function ConfirmItem(id)
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
                window.livewire.emit('deleteItem', id)    
                swal.close()   
            })
    }
	function HacerPedido()
    {
        let me = this
        swal({
            title: 'Solo debes hacer click en Aceptar si ya realizaste el pedido al proveedor',
            text: '¿DESEAS CONFIRMAR ESTA ACCIÓN?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false
            },
            function() {
                window.livewire.emit('hacerPedido')    
                swal.close()   
            })
    }
	function RecibirPedido()
    {
        let me = this
        swal({
            title: 'Solo debes hacer click en Aceptar si ya recibiste el pedido',
            text: '¿DESEAS CONFIRMAR ESTA ACCIÓN?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false
            },
            function() {
                window.livewire.emit('recibirPedido')    
                swal.close()   
            })
    }
 
    window.onload = function() {
        document.getElementById("search").focus(); 
		Livewire.on('productosPedido',(cantidad,producto,proveedor,fecha,estado)=>{
			Swal.fire({
			title: 'El producto ' + producto + ' ya fue pedido!',
			text: 'Se pidieron ' + cantidad + ' unidades/kg a ' + proveedor + ' el día ' + fecha,
			icon: 'question',
			showDenyButton: true,
			confirmButtonText: `Confirmar item pedido`,
			denyButtonText: `Cancelar item pedido`,
			}).then((result) => {
				if (result.isConfirmed) {
					window.livewire.emit('realizarPedidoProducto', true);
				} else if (result.isDenied) {
					window.livewire.emit('realizarPedidoProducto', false);
				}
			})
		})
    }

</script>