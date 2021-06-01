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
										@include('common.actions', ['edit' => 'Gastos_edit', 'destroy' => 'Gastos_destroy']) <!-- botons editar y eliminar -->
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
