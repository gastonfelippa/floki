<div class="row layout-top-spacing justify-content-center">  
@include('common.alerts')
	@if($action == 1)
    <div class="col-sm-12 col-md-8 layout-spacing">      
    	<div class="widget-content-area">
    		<div class="widget-one">
    			<div class="row">
    				<div class="col-xl-12 text-center">
    					<h3><b>Cajas Habilitadas</b></h3>
    				</div> 
    			</div> 
					@can('HabilitarCaja_index')
					<div class="container">
						<div class="row justify-content-end">
							<div class="col-5 mb-1">
								<button type="button" wire:click="doAction(2)" class="btn btn-danger btn-block">
									Nueva
								</button>
							</div>
						</div>
					</div>
					@endcan
					<div class="table-responsive scroll">
						<table class="table table-hover table-checkable table-sm">
							<thead>
								<tr>
									<th class="">CAJA</th>
									<th class="">OPERADOR</th>
									<th class="">USUARIO HABILITANTE</th>
									<th class="text-center">ACCIONES</th>
								</tr>
							</thead>
							<tbody>
								@foreach($info as $r)
								<tr>
									<td>{{$r->descripcion}}</td>
									<td>{{$r->apellido}} {{$r->name}}</td>
									<td>{{$r->apeNomCajaHab}}</td>
									<!-- @if($r->estado == 1) -->
									<!-- @else
									<td>Deshabilitada</td>
									@endif -->
									<td class="text-center">
										@include('common.actions', ['edit' => 'HabilitarCaja_index', 'destroy' => 'HabilitarCaja_index'])
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
			</div>
    	</div> 
    </div>
	@else
	@include('livewire.cajausuario.form')
	@include('livewire.cajausuario.modal')	
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
    		swal.close()
    	})
    }
	function openModal()
    {
		$('.modal-title').text('Agregar Caja')    
		$('#descripcion').val('')
		$('#modalAddCaja').modal('show')
	}
	function save()
    {
		if($('#descripcion').val() == '') {
			toastr.error('El campo Descripción no puede estar vacío')
			return;
		}
		var data = JSON.stringify({
			'descripcion': $('#descripcion').val()
		});

        $('#modalAddCaja').modal('hide')
        window.livewire.emit('createFromModal', data)
    }
    window.onload = function() {
        document.getElementById("descripcion").focus();
    }
</script>
