<div class="row layout-top-spacing">    
    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
    	@if($action == 1)         
    	<div class="widget-content-area br-4">
    		<div class="widget-header">
    			<div class="row">
    				<div class="col-xl-12 text-center">
    					<h3><b>Usuarios y Empleados</b></h3>
    				</div> 
    			</div>
			</div>					
			@include('common.inputBuscarBtnNuevo', ['create' => 'Usuarios_create']) 
			@include('common.alerts') 
    		<div class="table-responsive scroll">
    			<table class="table table-hover table-checkable table-sm mb-2">
    				<thead>
    					<tr>                                                   
    						<th class="">APELLIDO Y NOMBRES</th>
    						<th class="">ROL</th>
    						<th class="">TELÉFONO</th>
    						<th class="">DIRECCIÓN</th>
    						<th class="">EMAIL</th>
    						<th class="text-center">ACCIONES</th>
    					</tr>
    				</thead>
    				<tbody>
    					@foreach($info as $r) <!-- iteración para llenar la tabla-->
    					<tr>
    						<td><p class="mb-0">{{$r->apellido}}, {{$r->name}}</p></td>
    						<td>{{$r->alias}}</td>
    						<td>{{$r->telefono1}}</td>
    						<td>{{$r->calle}} {{$r->numero}}</td>
    						<td>{{$r->email}}</td>
    						<td class="text-center">
								@include('common.actions', ['edit' => 'Usuarios_edit', 'destroy' => 'Usuarios_destroy']) 
							</td>
    					</tr>
    					@endforeach
    				</tbody>
    			</table>
    		</div>
		</div> 
    	@elseif($action == 2)
    	@include('livewire.usuarios.form')		
		@include('livewire.usuarios.modal')  
    	@endif  
    </div>
</div>

<style type="text/css" scoped>
.scroll{
    position: relative;
    height: 250px;
    margin-top: .5rem;
    overflow: auto;
}
</style>
<script src="{{ asset('assets/js/sweetAlert.js') }}"></script>
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
    		window.livewire.emit('deleteRow', id)    //emitimos evento deleteRow
    		toastr.success('info', 'Registro eliminado con éxito') //mostramos mensaje de confirmación 
    		swal.close()   //cerramos la modal
    	})
    }
	function Agregar()
	{
		Swal.fire({
			title: 'El nuevo Usuario es Empleado?',
			showDenyButton: true,
			showCancelButton: true,
			confirmButtonText: `Si`,
			denyButtonText: `No`,
			}).then((result) => {
			/* Read more about isConfirmed, isDenied below */
			if (result.isConfirmed) {
				Swal.fire({
					input: 'select',
					inputPlaceHolder: 'Empleado',
					inputValue: '@foreach($empleados as $e)
						{{$e->nombre}}@endforeach',
					inputOptions:'',

				})
			} else if (result.isDenied) {
				Swal.fire('Changes are not saved', '', 'info')
			}
		})
	}
	function openModal()
    {        
        $('#localidad').val('')
        $('#provincia').val('Elegir')
        $('#modalAddLocalidad').modal('show')
	}
	function save()
    {
        if($('#localidad').val() == '') {
            toastr.error('El campo Localidad no puede estar vacío')
            return;
        }
        if($('#provincia option:selected').val() == 'Elegir') {
            toastr.error('Elige una opción válida para la Provincia')
            return;
        }
        var data = JSON.stringify({
            'localidad': $('#localidad').val(),
            'provincia_id'  : $('#provincia option:selected').val()
        });

        $('#modalAddLocalidad').modal('hide')
        window.livewire.emit('createFromModal', data)
    } 
	
	
</script>
