<div class="col-sm-12 col-md-8 layout-spacing">
	<div class="widget-content-area">
        <div class="widget-one">
			@include('common.alerts')
			<div class="row">
				<div class="col-xl-12 text-center">
					<h3><b>Cheques en Cartera</b></h3>
				</div> 
			</div>   		
				<!-- @include('common.inputBuscarBtnNuevo', ['create' => 'Categorias_create']) -->
			<div class="row justify-content-between">
				<div class="col-12 col-sm-3 mb-1">
					<div class="input-group">
						<div class="input-group-prepend">
						<span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
						</div>
						<input id="search" type="text" wire:model="search" class="form-control form-control-sm" placeholder="Buscar.." aria-label="notification" aria-describedby="basic-addon1" autocomplete="off">
					</div>
				</div>
				@can('Categorias_create')
				<div class="col-4 col-sm-3 mt-1">
					<button id="btnNuevo" type="button" onclick="nuevoCheque()" class="btn btn-danger btn-block">
						Nuevo
					</button>
				</div>
				@endcan
				<div class="col-4 col-sm-3 mt-1">
					<button id="btnNuevo" type="button" onclick="seleccionarCheque()" class="btn btn-success btn-block">
						Seleccionar
					</button>
				</div>
				<div class="col-4 col-sm-3 mt-1">
					<button id="btnNuevo" type="button" wire:click="doAction(2)" class="btn btn-dark btn-block">
						Volver
					</button>
				</div>
			</div>
			<div class="row p-3">
				<h6 class="mt-1 mr-4">Ordenar por:</h6> 
				<div class="form-check form-check-inline">
					<input wire:model="searchBy" class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="1">
					<label class="form-check-label" for="inlineRadio1">Fecha asc.</label>
				</div>
				<div class="form-check form-check-inline">
					<input wire:model="searchBy" class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="2">
					<label class="form-check-label" for="inlineRadio2">Fecha desc.</label>
				</div>
				<div class="form-check form-check-inline">
					<input wire:model="searchBy" class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="3">
					<label class="form-check-label" for="inlineRadio3">Importe</label>
				</div>
			</div>
			<div class="row px-3">
				<div class="table-responsive scroll">
					<table class="table table-hover table-checkable table-sm">
						<thead>
							<tr>
								<th></th>
								<th class="text-left">BANCO</th>
								<th class="text-center">NUMERO</th>
								<th class="text-center">FECHA DE PAGO</th>
								<th class="text-right">IMPORTE</th>
							</tr>
						</thead>
						<tbody>
							@foreach($infoCheques as $r)
							<tr>
								<td>
									<!-- <input class="name" name="checks" type="checkbox">                                                                          -->
									<input id="{{$r->id}}" value="{{$r->importe}}" class="name" name="checks" type="checkbox">                                                                         
									<!-- <input onclick="calcularTotal()"  id="{{$r->factura_id}}" value="{{$r->importe}}" class="name" name="checks" type="checkbox" checked>                                                                          -->
								</td>
								<td>{{$r->banco}}</td>
								<td class="text-center">{{$r->numero}}</td>
								<td class="text-center">{{\Carbon\Carbon::parse($r->fecha_de_pago)->format('d-m-Y')}}</td>
								<td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
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
thead tr th {     /* fija la cabecera de la tabla */
	position: sticky;
	top: 0;
	z-index: 10;
	background-color: #ffffff;
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
		Livewire.on('eliminarRegistro',()=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Tu registro no se puede eliminar!',
                text: 'Existen Categorías relacionadas a ese Rubro...',
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
        document.getElementById($id).focus();
    }

</script>