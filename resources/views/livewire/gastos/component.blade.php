<div class="row layout-top-spacing justify-content-center">  
	@if($action == 1)
    <div class="col-sm-12 col-md-6 layout-spacing">      
    	<div class="widget-content-area">
    		<div class="widget-one">
    			<div class="row">
    				<div class="col-xl-12 text-center">
    					<h3><b>Gastos</b></h3>
    				</div> 
    			</div>    		
				@if($recuperar_registro == 1)
				@include('common.recuperarRegistro')
				@else
					@include('common.inputBuscarBtnNuevo', ['create' => 'Gastos_create'])
					<div class="table-responsive scroll">
						<table class="table table-hover table-checkable table-sm">
							<thead>
								<tr>
									<th class="">DESCRIPCIÓN</th>
									<th class="text-center">ACCIONES</th>
								</tr>
							</thead>
							<tbody>
								@foreach($info as $r)
								<tr>
									<td>{{$r->descripcion}}</td>
									<td class="text-center">
										<ul class="table-controls">
                                            @can('Gastos_edit')
											<li>
												<a href="javascript:void(0);" 
												wire:click="ver_receta({{$r->id}})" 
												data-toggle="tooltip" data-placement="top" title="Agregar datos">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg>
											</li>
                                            <!-- <li>
                                                <a href="javascript:void(0);" 
                                                wire:click="edit({{$r->id}})" 
                                                data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                            </li> -->
                                            @endcan
                                            @can('Gastos_destroy')
                                            <li>
                                                <a href="javascript:void(0);"          		
                                                onclick="Confirm('{{$r->id}}')"
                                                data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                            </li>
                                            @endcan
                                        </ul>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@endif
			</div>
		</div> 
    </div>
	@else
	@can('Gastos_create')
	@include('livewire.gastos.form')
	@include('livewire.gastos.modal')		
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
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> 

<script type="text/javascript">	
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
				// Si el valor es válido, debes regresar undefined. Si no, una cadena
				if (!comentario) {
					return "Por favor escribe un breve comentario";
				} else {
					return undefined;
				}
			}
		}).then((result) => {
			if (result.isConfirmed) {
				if (result.value) {
					let comentario = result.value;
					Swal.fire(
						'Eliminado!',
						'Tu registro se Eliminó correctamente...',
						'success'
					);
					window.livewire.emit('deleteRow', id, comentario)
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
	function guardar()
    {
		document.getElementById("nombre").focus();
        window.livewire.emit('StoreOrUpdate');
    }
	function openModal()
    {
        $('.modal-title').text('Agregar Categoría de Egresos')    
        $('#descripcion').val('')
		$('.gastoFijo').val
        $('#modalAddCategoria').modal('show')
	}
	function save()
    {
        if($('#descripcion').val() == '') {
            toastr.error('El campo Descripción no puede estar vacío')
            return;
        }
		var tipo;
		var elementos = document.getElementsByName("checks");
		for(var i=0; i<elementos.length; i++) {
			if(!elementos[i].checked) tipo = 1;
			else tipo = 2;
		}
        var data = JSON.stringify({
            'descripcion': $('#descripcion').val(),
			'tipo': tipo,
        });

        $('#modalAddCategoria').modal('hide')
        window.livewire.emit('createFromModal', data)
    }
    window.onload = function() {
		document.getElementById("search").focus();
    }
</script>
