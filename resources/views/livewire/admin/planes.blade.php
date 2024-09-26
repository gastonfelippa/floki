
<div class="row layout-top-spacing">    
    <div class="col-sm-12 col-md-8 layout-spacing">      
    	<div class="widget-content-area">
    		<div class="widget-one">
    			<div class="row">
    				<div class="col-xl-12 text-center">
    					<h3><b>Planes</b></h3>
    				</div> 
    			</div>    		
				@include('common.inputBuscarBtnNuevo', ['create' => 'Planes_index'])
				@include('common.alerts') <!-- mensajes -->
				<div class="table-responsive scroll">
					<table class="table table-hover table-checkable table-sm">
						<thead>
							<tr>
								<th class="">DESCRIPCIÓN</th>
								<th class="text-right">PRECIO</th>
								<th class="text-center">DURACIÓN</th>
								<th class="text-center">ESTADO</th>
								<th class="text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($info as $r) <!-- iteración para llenar la tabla-->
							<tr>
								<td class="text-left">{{$r->descripcion}}</td>
								<td class="text-right">{{$r->precio}}</td>
								<td class="text-center">{{$r->duracion}} días</td>
								<td class="text-center">{{$r->estado}}</td>
								<td class="text-center">
									@include('common.actions', ['edit' => 'Planes_index', 'destroy' => 'Planes_index']) <!-- botons editar y eliminar -->
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
    	</div> 
    </div>
	@can('Planes_index')
	<div class="col-sm-12 col-md-4 layout-spacing">
		<div class="widget-content-area">
            <div class="widget-one mt-2">
                <h5>
                    <b>@if($selected_id ==0) Crear Nuevo Plan  @else Editar Plan @endif  </b>
                </h5>
                @include('common.messages')
                <div class="row mt-3">                               
                    <div class="col-12 layout-spacing">
                        <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></span>
                            </div>
                            <input id="nombre" type="text" class="form-control text-uppercase" placeholder="Nombre del Plan" wire:model="descripcion">
                        </div>
                    </div>
                </div>
                <div class="row">                               
                    <div class="col-12 layout-spacing">
                        <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></span>
                            </div>
                            <input type="text" class="form-control" placeholder=Precio  wire:model="precio">
                        </div>
                    </div>
                </div>
                <div class="row">                               
                    <div class="col-12 layout-spacing">
                        <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></span>
                            </div>
                            <input type="text" class="form-control" placeholder=Duración  wire:model="duracion">
                        </div>
                    </div>
                </div>
                <div class="row">                               
                    <div class="col-12 layout-spacing">
                        <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></span>
                            </div>
                            <select wire:model="estado" class="form-control text-center">
                                <option value="activo">ACTIVO</option>
                                <option value="suspendido">SUSPENDIDO</option>
                            </select>
                        </div>
                    </div>
                </div>
                @include('common.btnCancelarGuardar')
            </div>
        </div>	
	</div>
	@endcan
</div>

<style type="text/css" scoped>
.scroll{
    position: relative;
    height: 265px;
    margin-top: .5rem;
    overflow: auto;
}
</style>

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
    window.onload = function() {
        document.getElementById("search").focus();
    }
    function setfocus($id) {
        document.getElementById($id).focus();
    }
</script>