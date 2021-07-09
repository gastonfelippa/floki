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
										<ul class="table-controls">
											@can('HabilitarCaja_index')
												<li>
													<a href="javascript:void(0);" wire:click="edit({{$r->id}},1)" data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
												</li>
											@endcan
											@can('HabilitarCaja_index')
												<li>
													<a href="javascript:void(0);"          		
													onclick="Confirm('{{$r->id}}')"
													data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
												</li>
												<li>
													<button type="button"
													wire:click="edit({{$r->id}},2)"   
													class="btn btn-primary btn-sm">
													Agregar Importe
													</button>
												</li>
											@endcan
										</ul>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<input type="hidden" id="usuario_habilitado" wire:model="usuario_habilitado">  
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
    height: 285px;
    margin-top: .5rem;
    overflow: auto;
}
.scrollform{
    position: relative;
    height: 195px;
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
	window.onload = function(){
		Livewire.on('usuarioNoAutorizado',()=>{
			swal({
				title: 'INFO',
				text: 'SOLO PUEDE HABER UN SOLO USUARIO AUTORIZADO PARA HABILITAR CAJAS...',
				type: 'warning',
				confirmButtonColor: '#3085d6',
				confirmButtonText: 'Volver',
				closeOnConfirm: false
			})
		})
	}	
	window.onload = function(){
		if($('#usuario_habilitado').val() == 0){
            swal({
                title: 'Oops',
                text: 'HOY NO PODÉS HABILITAR CAJAS, OTRO USUARIO INICIALIZÓ ESA TAREA...',
                type: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Volver',
                closeOnConfirm: false
            },
            function() {  
                window.location.href="{{ url('home') }}";
                swal.close()   
            })
        }
		Livewire.on('delete',()=>{
			swal({
				title: 'LA CAJA NO SE PUDO ELIMINAR!',
				text: 'Posee movimientos registrados...',
				type: 'warning',
				confirmButtonColor: '#3085d6',
				confirmButtonText: 'Volver',
				closeOnConfirm: false
    		})
		})
	}
</script>
