<div class="main-content">
    @if($action == 1)
    <div class="layout-pc-spacing"> 
		<div class="row layout-top-spacing"> 
			<div class="col-xs-12 col-lg-12 col-md-12 col-sm-12 layout-spacing">      
				<div class="widget-content-area br-4">
					<div class="widget-one">
						<div class="row">
							<div class="col-xl-12 text-center">
								<h3><b>Abonados: {{$abonados}} Activos: {{$activos}} En Mora: {{$en_mora}} </b></h3>
							</div> 
						</div> 
						<div class="row mb-3">
							<div class="col-sm-6 mb-1">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
									</div>
									<input id="search" type="text" wire:model="search" class="form-control" placeholder="Buscar.." aria-label="notification" aria-describedby="basic-addon1">
								</div>
							</div>
							<div class="col-sm-3 mb-1">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
									</div>
								<select wire:model="estadoPlan" class="form-control text-center">
									<option value="activo">ACTIVO</option>
									<option value="finalizado">FINALIZADO</option>
									<option value="notificado">NOTIFICADO</option>
									<option value="suspendido">SUSPENDIDO</option>
								</select>
								</div>
							</div>
							<!-- <div class="col-sm-3 my-1">
								<a href="{{ url('procedimientos') }}" class="btn btn-outline-danger">
									<div class="">
										<span>EJECUTAR PROCEDIMIENTOS</span>
									</div>
								</a>
							</div> -->
						</div>
						<div class="table-responsive scroll">
							<table class="table table-hover table-checkable table-sm">
								<thead>
									<tr>
										<th class="text-left">NOMBRE</th>
										<th class="text-center">EMAIL</th>
										<th class="text-center">TELÉFONO</th>
										<th class="text-center">COMERCIO</th>
										<th class="text-center">PLAN</th>
										<th class="text-center">ESTADO</th>
										<th class="text-center">FECHA_FIN</th>
										<th class="text-center">FECHA_VTO</th>
										<th class="text-center">ESTADO_PAGO</th>
										<th class="text-center">ACCIONES</th>
									</tr>
								</thead>
								<tbody>
									@foreach($info as $r) <!-- iteración para llenar la tabla-->
									<tr>
										<td class="text-left">{{$r->apellido}}, {{$r->name}} </td>
										<td class="text-center">{{$r->email}}</td>
										<td class="text-center">{{$r->telefono1}}</td>
										<td class="text-center">{{$r->nombrecomercio}}</td>
										<td class="text-center">{{$r->descripcion}}</td>
										
										@if($r->estado_plan == 'suspendido')
										<td class="text-center text-white bg-danger">{{$r->estado_plan}}</td>						
										@else 									
										<td class="text-center">{{$r->estado_plan}}</td>
										@endif

										<td class="text-center"> 
											{{\Carbon\Carbon::parse($r->fecha_fin)->format('d-m-Y')}}</td>
										<td class="text-center"> 
											{{\Carbon\Carbon::parse($r->fecha_vto)->format('d-m-Y')}}</td>
											
										@if($r->estado_pago == 'en mora')
										<td class="text-center text-white bg-warning">{{$r->estado_pago}}</td>
										@elseif($r->estado_pago == 'impago')
										<td class="text-center text-white bg-danger">{{$r->estado_pago}}</td>
										@else
										<td class="text-center">{{$r->estado_pago}}</td>
										@endif

										<td class="text-center">
											<ul class="table-controls">
												<li>
													<a href="javascript:void(0);" 
													wire:click="edit({{$r->id}})" 
													data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
												</li>
												<li>
													<a href="javascript:void(0);"          		
													onclick="Confirm('{{$r->id}}')"
													data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
												</li>
												<li>
													<a href="javascript:void(0);" 
													wire:click="doAction(2,{{$r->id}})" 
													data-toggle="tooltip" data-placement="top" title="Generar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign text-dark"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
												</li>
											</ul>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div> 
			</div>
		</div> 
	</div>
	@elseif($action > 1)
	@include('livewire.admin.nuevo_plan_abonado')
	@endif
</div>

<style type="text/css" scoped>
 thead tr th { 
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #ffffff;
        }
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
	function ConfirmCobro()
    {
    	let me = this
    	swal({
    		title: 'CONFIRMAR',
    		text: '¿DESEAS COBRAR ESTE PLAN?',
    		type: 'warning',
    		showCancelButton: true,
    		confirmButtonColor: '#3085d6',
    		cancelButtonColor: '#d33',
    		confirmButtonText: 'Aceptar',
    		cancelButtonText: 'Cancelar',
    		closeOnConfirm: false
    	},
    	function() {
    		window.livewire.emit('cobrarPlan')    //emitimos evento cobrarPlan
    		toastr.success('info', 'El cobro se efectuó correctamente!!!') //mostramos mensaje de confirmación 
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