<div class="row layout-top-spacing justify-content-center">  
	@include('common.alerts')
	@if($action == 1)  
    <div class="col-sm-12 col-md-6 layout-spacing">      
    	<div class="widget-content-area">
    		<div class="widget-one">
    			<div class="row">
    				<div class="col-xl-12 text-center">
    					<h3><b>Categorías</b></h3>
    				</div> 
    			</div> 
				@if($recuperar_registro == 1)
				@include('common.recuperarRegistro')
				@else  		
					@include('common.inputBuscarBtnNuevo', ['create' => 'Categorias_create'])
					<div class="table-responsive scroll">
						<table class="table table-hover table-checkable table-sm">
							<thead>
								<tr>
									<th class="text-left">DESCRIPCIÓN</th>
                                    @if($otorgarCategoriaSegunLaEdad == 1)
                                        <th class="text-center">EDAD MÍNIMA</th>
                                        <th class="text-center">EDAD MÁXIMA</th>
                                    @endif
									<th class="text-right">IMPORTE</th>
									<th class="text-center">ACCIONES</th>
								</tr>
							</thead>
							<tbody>
								@foreach($info as $r)
								<tr>
									<td>{{$r->descripcion}}</td>
                                    @if($otorgarCategoriaSegunLaEdad == 1)
                                        <td class="text-center">{{$r->edad_minima}}</td>
                                        <td class="text-center">{{$r->edad_maxima}}</td>
                                    @endif
                                    <td class="text-center">{{$r->importe}}</td>
									<td class="text-center">
										@include('common.actions', ['edit' => 'Categorias_edit', 'destroy' => 'Categorias_destroy'])
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
        @can('Categorias_create')
        @include('livewire.categoriasclub.form')		
        @endif
	@endcan
</div>

<style type="text/css" scoped>
.scroll{
    position: relative;
    height: 270px;
    margin-top: .5rem;
    overflow: auto;
}
</style>

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
    window.onkeydown = PulsarTecla;
	function PulsarTecla(e)
    {
        tecla = e.keyCode;
        if(e.altKey == 1 && tecla == 78) document.getElementById("btnNuevo").click();
        else if(e.altKey == 1 && tecla == 71) document.getElementById("btnGuardar").click();
        else if(tecla == 27) document.getElementById("btnCancelar").click();
    }

    window.onload = function() {
        document.getElementById("search").focus();
		Livewire.on('eliminarRegistro',()=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Tu registro no se puede eliminar!',
                text: 'Existen Productos relacionados a esa Categoría...',
                showConfirmButton: false,
                timer: 3500
            })
		}) 
		Livewire.on('registroEliminado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Registro Eliminado!',
                text: 'Tu registro se eliminó correctamente...',
                showConfirmButton: false,
                timer: 1500
            })
		}) 
    }
    function setfocus(id) {
        document.getElementById(id).focus();
    }

</script>