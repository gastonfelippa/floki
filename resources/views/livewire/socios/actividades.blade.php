
    <div class="col-sm-12 col-md-6 layout-spacing">      
        @include('common.alerts')
    	<div class="widget-content-area">
    		<div class="widget-one">
    			<div class="row">
    				<div class="col-xl-12 text-center">
    					<h3>Actividades de<b> {{$socio}}</b></h3>
    				</div>
    			</div>
                <div class="row">
                    <div class="col-12 text-right layout-spacing">
                    @can('Clientes_create')
                        <button id="btnNuevo" type="button" onclick="openModalActividades()" class="btn btn-danger">
                            Agregar
                        </button>
                    @endcan
                        <button id="btnVolver" type="button" wire:click="doAction(1)" class="btn btn-dark">
                            Volver
                        </button>
                    </div> 
    			</div> 
				@if($recuperar_registro == 1)
				@include('common.recuperarRegistro')
				@else  	
					<div class="table-responsive scroll">
						<table class="table table-hover table-checkable table-sm">
							<thead>
								<tr>
									<th class="text-left">DESCRIPCIÓN</th>
									<th class="text-right">IMPORTE</th>
									<th class="text-center">ACCIONES</th>
								</tr>
							</thead>
							<tbody>
								@foreach($socio_actividad as $r)
								<tr>
									<td>{{$r->descripcion}}</td>
									<td class="text-right">{{$r->importe}}</td>
									<td class="text-center">
                                    @include('common.actions', ['edit' => 'Clientes_edit', 'destroy' => 'Clientes_destroy']) 
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
    window.onload = function() {
        document.getElementById("search").focus();
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
    function setfocus($id) {
        document.getElementById($id).focus();
    }
</script>