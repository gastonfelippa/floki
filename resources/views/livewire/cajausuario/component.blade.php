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
	@elseif($action == 2)
		@include('livewire.cajausuario.form')
		@include('livewire.cajausuario.modal')	
		@include('livewire.cajausuario.modalImporte')
	@elseif($action == 3)
		@include('livewire.cajausuario.cheques')
		@include('livewire.cajausuario.modalCheques')  
   		@include('livewire.cajausuario.modalBancos') 
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
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>


<script type="text/javascript">
	function Confirm(chequeId, id)
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
					window.livewire.emit('deleteRow', chequeId, id, comentario)
				}
			}else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire('Cancelado', 'Tu registro está a salvo :)', 'error')
            }
		})
    }
	function nuevoCheque()
	{
		$('#modalCheques').modal('show');
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
	function mostrarInput()
    {  
		if($('[id="formaDePago"]').val() == '1'){
			window.livewire.emit('doAction',4)
		}else if($('[id="formaDePago"]').val() == '2'){
			window.livewire.emit('doAction',3)
		}
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
	function openModalImporte(id,importe)
    {
		$('.modal-title').text('Editar Importe')    
		$('#importe').val(importe)
		$('#id').val(id)
		$('#modalEditImporte').modal('show')
	}
	function saveEdit()
    {
		if($('#importeEdit').val() == '') {
			toastr.error('El campo Importe no puede estar vacío')
			return;
		}
		var data = JSON.stringify({
			'id'     : $('#id').val(),
			'importe': $('#importeEdit').val()
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
