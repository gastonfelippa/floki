<div class="row layout-top-spacing justify-content-center">  
	@if($action == 1)
    <div class="col-sm-12 col-md-8 layout-spacing">      
		@include('common.alerts')
    	<div class="widget-content-area">
    		<div class="widget-one">
    			<div class="row">
    				<div class="col-xl-12 text-center">
    					<h3><b>Cajas Habilitadas</b></h3>
    				</div> 
    			</div> 
				<div class="container">
					<div class="row justify-content-end">
						<div class="col-5 mb-1">
							<button type="button" wire:click="doAction(2)" class="btn btn-danger btn-block">
								Nueva
							</button>
						</div>
					</div>
				</div>
				<div class="table-responsive scroll">
					<table class="table table-hover table-checkable table-sm">
						<thead>
							<tr>
								<th class="">CAJA</th>
								<th class="">OPERADOR</th>
								<th class="">IMPORTE</th>
								<th class="">USUARIO HABILITANTE</th>
								<th class="text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($info as $r)
							<tr>
								<td>{{$r->descripcion}}</td>
								<td>{{$r->apellido}} {{$r->name}}</td>
								<td class="text-center">{{number_format($r->importeCaja,2,',','.')}}</td>
								<td>{{$r->apeNomCajaHab}}</td>
								@if($r->user_id == auth()->user()->id)
								<td class="text-center">
									<ul class="table-controls">
										<li>
											<a href="javascript:void(0);" wire:click="edit({{$r->id}},1)" data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
										</li>
										<li>
											<button type="button"
											wire:click="edit({{$r->id}},2)"   
											class="btn btn-primary btn-sm">
											Agregar Importe
											</button>
										</li>
									</ul>
								</td>
								@endif
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
	@include('livewire.cajausuario.modalImporte')	
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
	function openModalImporte(id,importe)
    {
		$('.modal-title').text('Editar Importe')    
		$('#importe').val(importe)
		$('#id').val(id)
		$('#modalEditImporte').modal('show')
	}
	function saveEdit()
    {
		if($('#importe').val() == '') {
			toastr.error('El campo Importe no puede estar vacío')
			return;
		}
		var data = JSON.stringify({
			'id'     : $('#id').val(),
			'importe': $('#importe').val()
		});
        $('#modalEditImporte').modal('hide')
        window.livewire.emit('editFromModal', data)
    }	
	window.onload = function(){
		if($('#usuario_habilitado').val() == 0){
            swal({
                title: 'Oops!',
                text: 'Hoy no podés habilitar Cajas, otro usuario inicializó esa tarea...',
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
	}
</script>
