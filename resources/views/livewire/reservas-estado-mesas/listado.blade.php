
    <div class="col-sm-12 col-md-6 layout-spacing">      
    	<div class="widget-content-area">
    		<div class="widget-one">
    			<div class="row">
    				<div class="col-xl-12 text-center">
    					<h3><b>Reservas</b></h3>
    				</div> 
    			</div> 
				@if($recuperar_registro == 1)
				@include('common.recuperarRegistro')
				@else 
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
							<span style="text-decoration: underline;">N</span>ueva
						</button>
					</div>
					@endcan
				</div> 		
					<!-- @include('common.inputBuscarBtnNuevo', ['create' => 'Categorias_create']) -->
					<div class="table-responsive scroll">
						<table class="table table-hover table-checkable table-sm">
							<thead>
								<tr>
                                    <th class="">CLIENTE</th>
                                    <th class="text-center">CANTIDAD</th>
                                    <th class="text-center">MESA</th>
                                    @can('Clientes_edit')
                                    <th class="text-center">ACCIONES</th>
                                    @endcan
                                </tr>
							</thead>
							<tbody>
								@foreach($reservas as $r)
								<tr>
									<td>{{$r->apellido}} {{$r->nombre}}</td>
									<td class="text-center">{{$r->cantidad}}</td>
									<td class="text-center">{{$r->mesa}}</td>
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
    function setfocus($id) {
        document.getElementById($nombre).focus();
    }

</script>