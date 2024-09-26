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
			@if($recuperar_registro == 1)
			@include('common.alerts')
				@include('common.recuperarRegistro')
				@include('common.messages')
			@else					
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
    					@foreach($info as $r)
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
			@endif
		</div> 
    	@elseif($action == 2)
    	@include('livewire.usuarios.form')		
		@include('livewire.usuarios.modal') 
		@include('livewire.proveedores.modalCategoria') 
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
	function openModalCategoria()
    {
        $('.modal-title').text('Agregar Categoría de Egresos')    
        $('#descripcion_cat').val('')
		// $('.gastoFijo').val
        $('#modalAddCategoria').modal('show')
	}
    function saveCategoria()
    {
        if($('#descripcion_cat').val() == '') {
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
            'descripcion': $('#descripcion_cat').val(),
			'tipo': tipo,
        });

        $('#modalAddCategoria').modal('hide')
        window.livewire.emit('createCategoriaFromModal', data)
    } 
	function verificarPorDni()
	{
		var dni = document.getElementById("documento");
		var ex_regular_dni = /^\d{8}(?:[-\s]\d{4})?$/;
		if (ex_regular_dni.exec(dni.value)){
			window.livewire.emit('verificarPorDni')
		}else{
			toastr.error('DNI erróneo, formato no válido. Ingrese solo números.')
			dni.focus();
			return false;
		}		
	}
	function validarNombre()
	{
		var nombre = document.getElementById("name");
		var expRegNombre = /^[a-zA-ZÑñÁáÉéÍíÓóÚúÜü\s]+$/;
		if (!expRegNombre.exec(nombre.value)){
			toastr.error("El campo Nombre solo admite letras y espacios.")
			nombre.focus();
			return false;
		}		
	}
	function validarApellido()
	{
		var apellido = document.getElementById("apellido");
		var expRegApellido = /^[a-zA-ZÑñÁáÉéÍíÓóÚúÜü\s]+$/;
		if (!expRegApellido.exec(apellido.value)){
			toastr.error("El campo Apellido solo admite letras y espacios.")
			apellido.focus();
			return false;
		}		
	}
	window.onload = function(){
		Livewire.on('usuario_repetido',()=>{
			var dni = document.getElementById("documento");
			toastr.error('El DNI ya está registrado...', 'Verifica los datos!')
			dni.focus();
			return false;	
		})
	}
</script>
