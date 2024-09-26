<div class="row layout-top-spacing justify-content-center"> 
	@if($action == 1)
	<div class="col-sm-12 col-md-8 layout-spacing">
		<div class="widget-content-area">
			<div class="widget-one">
				@include('common.alerts')
				<div class="row">
					<div class="col-xl-12 text-center">
						<h3><b>Cheques</b></h3>
					</div> 
				</div> 
				@if($recuperar_registro == 1)
				@include('common.recuperarRegistro')
				@else  		
					@include('common.inputBuscarBtnNuevo', ['create' => 'Categorias_create'])  
				<!-- <div class="row">
					<div class="col-12 col-sm-4 mb-1">
						<div class="input-group">
							<div class="input-group-prepend">
							<span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
							</div>
							<input id="search" type="text" wire:model="search" class="form-control form-control-sm" placeholder="Buscar.." aria-label="notification" aria-describedby="basic-addon1" autocomplete="off">
						</div>
					</div>
					@can('Categorias_create')
					<div class="col-12 col-sm-8">
						<div class="row">
							<div class="col-4 mt-1">
								<button id="btnNuevo" type="button" wire:click="doAction(2)" class="btn btn-danger btn-block">
									Nuevo
								</button>
							</div>
							@endcan
							<div class="col-4 mt-1">
								<button id="btnNuevo" type="button" onclick="seleccionarCheque()" class="btn btn-success btn-block">
									Seleccionar
								</button>
							</div>
							<div class="col-4 mt-1">
								<button id="btnNuevo" type="button" wire:click="doAction(1)" class="btn btn-dark btn-block">
									Volver
								</button>
							</div>
						</div>
					</div>
				</div> -->
				<div class="row px-3">
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
									<th class="text-left">BANCO</th>
									<th class="text-center">NUMERO</th>
									<th class="text-center">FECHA DE PAGO</th>
									<th class="text-right">IMPORTE</th>
									<th class="text-center">ESTADO</th>
									<th class="text-left">ACCIONES</th>
								</tr>
							</thead>
							<tbody>
								@foreach($infoCheques as $r)
								<tr>
									<td>{{$r->banco}}</td>
									<td class="text-center">{{$r->numero}}</td>
									<td class="text-center">{{\Carbon\Carbon::parse($r->fecha_de_pago)->format('d-m-Y')}}</td>
									<td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
									<td class="text-center">{{$r->estadoCheque}}</td>
									<td class="text-left">
										<ul class="table-controls">
											<li>
												<a href="javascript:void(0);" 
												wire:click="edit({{$r->id}})" 
												data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
											</li>
											@if($r->estado != 'en_caja')
											<li>
												<a href="javascript:void(0);"          		
												onclick="Confirm({{$r->id}})"
												data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
											</li>
											@endif
										</ul>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				@endif
			</div> 
		</div>
	</div>
	@else
		@include('livewire.cheques.form')  
   		@include('livewire.cheques.modalBancos') 
	@endif
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
	function guardarDatosCheque()
    {
        $('[id="saldo"]').val(Number.parseFloat($('[id="saldo"]').val()).toFixed(2));
        $('[id="importeCheque"]').val(Number.parseFloat($('[id="importeCheque"]').val()).toFixed(2));
        var saldo           = $('[id="saldo"]').val();
        var importe         = $('[id="importeCheque"]').val();
        var terminarFactura = 1;

        if(importe > saldo){
            Swal.fire('Cancelado','El importe ingresado es mayor al saldo','info');
            resetear();
        } 
        
        if(importe != saldo) terminarFactura = 0;

        var data = JSON.stringify({
                'banco'         : $('#banco').val(),
                'numero'        : $('#numCheque').val(),
                'fechaDeEmision': $('#fechaDeEmision').val(),
                'fechaDePago'   : $('#fechaDePago').val(),
                'importe'       : $('#importeCheque').val(),
                'cuitTitular'   : $('#cuitTitular').val(),
                'terminarFactura' : terminarFactura,
            });		
		window.livewire.emit('enviarDatosCheque', data);
        // resetear();
	}
	function seleccionarCheque()
	{
		//creo un array con los Id de las facturas que se cobran y luego lo paso a Json 
		var arrId = $('[name="checks"]:checked').map(function(){
			return this.id;
		}).get();  
		var dataId = JSON.stringify(arrId);

		//creo un array con los Importes de las facturas que se cobran
		var arrImporte = $('[name="checks"]:checked').map(function(){
			return this.value;
		}).get();
		var dataImporte = JSON.stringify(arrImporte);
		
		var cantidad = 0;
		var total = 0;  //calculo el total a cobrar
		for(var i of arrImporte) {
			total = parseInt(total) + parseInt(i);
			cantidad ++; 
		}
		window.livewire.emit('chequeSeleccionado',dataId,dataImporte,total.toFixed(2),cantidad);
	}
    function openModalBancos()
    {
        $('#modalCheques').modal('hide')
        $('#banco').val('')
        $('#sucursal').val('')
        $('#modalBancos').modal('show')
	}
	function guardarBanco()
    {      
        if($('#descripcion').val() == '') {
            toastr.error('Ingresa un nombre válido para el Banco')
            return;
        }
        if($('#sucursal').val() == '') {
            toastr.error('Ingresa un nombre válido para la Sucursal')
            return;
        }
        var data = JSON.stringify({
            'banco'    : $('#descripcion').val(),
            'sucursal' : $('#sucursal').val()
        });
       
        $('#modalBancos').modal('hide');
        window.livewire.emit('agregarBanco', data);

        $('#formaDePago').val('1');
        $('#num').val('');
        $('#importe').val(Number.parseFloat($('#saldo').val()).toFixed(2));
    }
	function agregarCliente()
	{
		window.location.href="{{ url('clientes') }}";
	}

    window.onload = function() {
        document.getElementById("search").focus();
		Livewire.on('bancoCreado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'El Banco fue creado!!',
                showConfirmButton: false,
                timer: 1500
            });
        })
        Livewire.on('chequeCreado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'El Cheque se registró correctamente!!',
                showConfirmButton: false,
                timer: 1500
            });
        })
        Livewire.on('chequeModificado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'El Cheque se modificó correctamente!!',
                showConfirmButton: false,
                timer: 1500
            });
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
		Livewire.on('eliminarRegistro',()=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Tu registro no se puede eliminar!',
                text: 'El Cheque ya fue entregado como medio de pago...',
                showConfirmButton: false,
                timer: 3500
            })
		}) 
    }
</script>