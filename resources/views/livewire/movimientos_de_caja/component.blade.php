<div class="row layout-top-spacing justify-content-center">  
	@include('common.alerts')
	@include('common.messages')
    <div class="col-sm-12 col-md-5 layout-spacing">      
    	<div class="widget-content-area">
    		<div class="widget-one">
    			<div class="row">
    				<div class="col-xl-12 text-center">
    					<h3><b>Movimientos De Caja</b></h3>
    				</div> 
    			</div> 
                <div class="row">
                    <div class="col text-center my-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" value="1" wire:model="estado" checked>
                            <label class="form-check-label">Egresos</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" value="2" wire:model="estado">
                            <label class="form-check-label">Otros Ingresos</label>
                        </div>
                    </div>
                    <div class="col-4 mt-1">
                    @if($estado == 1) <button onclick="openModal(1,0,0,0,0)" type="button" class="btn btn-danger btn-block">Nuevo</button>
                    @else <button onclick="openModal(2,0,0,0,0)" type="button" class="btn btn-danger btn-block">Nuevo</button>
                    @endif
                    </div>
                </div>  			
                <div class="table-responsive scroll">
					<table class="table table-hover table-checkable table-sm">
						<thead>
							<tr>
								<th class="">DESCRIPCIÓN</th>
								<th class="text-right">IMPORTE</th>
								<th class="text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($info as $r)
							<tr>
								<td>{{$r->descripcion}}</td>
                                <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
								<td class="text-center">
                                    <ul class="table-controls">
                                    @can('MovimientosDiarios_index')
                                    <li>
                                        @if($estado == 1)
                                        <a href="javascript:void(0);"
                                        onclick="openModal(0,1,'{{$r->id}}','{{$r->egreso_id}}','{{$r->importe}}')"
                                        data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                        @else
                                        <a href="javascript:void(0);"
                                        onclick="openModal(0,2,'{{$r->id}}','{{$r->ingreso_id}}','{{$r->importe}}')"
                                        data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                        @endif
                                    </li>
                                    @endcan
                                    @can('MovimientosDiarios_index')
                                    <li>
                                        <a href="javascript:void(0);"          		
                                        onclick="Confirm('{{$r->id}}')"
                                        data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                    </li>
                                    @endcan
                                    </ul>
                                </td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>            
			</div>
            <input type="hidden" id="caja_abierta" wire:model="caja_abierta"> 
    	</div>
        @include('livewire.movimientos_de_caja.modal')
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
    		window.livewire.emit('deleteRow', id)
    		swal.close()
    	})
    }

    function openModal(ing_egr, editar, mov_id, ing_egr_id, importe)
    {
        if(editar == 1){
            $('.modal-title').text('Editar Egreso')
            $('#proveedor').val(ing_egr_id)
            $('#mov_importe').val(importe)
            $('#mov_id').val(mov_id)
            $('#edit_ing_egr').val(1)
        }else if(editar == 2){
            $('.modal-title').text('Editar Otro Ingreso')
            $('#proveedor').val(ing_egr_id)
            $('#mov_importe').val(importe)
            $('#mov_id').val(mov_id)
            $('#edit_ing_egr').val(2)
        }else{
            if(ing_egr == 1){
                $('.modal-title').text('Agregar Egreso')
            }else{
                $('.modal-title').text('Agregar Otro Ingreso')
            } 
            $('#proveedor').val('Elegir')
            $('#mov_importe').val('')
            $('#mov_id').val(0)
            $('#edit_ing_egr').val(0)
        }
        $('#modalAddMov').modal('show')
	}
	function save()
    {
        if($('#proveedor option:selected').val() == 'Elegir') {
            toastr.error('Elige una opción válida para el Egreso')
            return;
        }
        if($('#mov_importe').val() == '') {
            toastr.error('El campo Importe no puede estar vacío')
            return;
        }
        var data = JSON.stringify({
            'ing_egr_id'  : $('#proveedor option:selected').val(),
            'mov_importe': $('#mov_importe').val(),
            'mov_id': $('#mov_id').val(),
            'edit_ing_egr': $('#edit_ing_egr').val(),
        });

        $('#modalAddMov').modal('hide')
        window.livewire.emit('createFromModal', data)
    }
    function redireccionar(data){
        if(data == 1) window.location.href="{{ url('gastos') }}";
        else window.location.href="{{ url('otroingreso') }}";
    }
    
    window.onload = function() {
        if($('#caja_abierta').val() == 0){
            swal({
                title: 'Caja inhabilitada!',
                text: '',
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
        
		Livewire.on('agregarDetalle',()=>{
	        swal({
                title: 'CONFIRMAR',
                text: '¿Deseas agregar el detalle del Egreso?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
                closeOnConfirm: false
            },
            function() {
				window.location.href="{{ url('compras') }}";
                //window.livewire.emit('deleteRow', id, es_producto)    
                //toastr.success('info', 'Registro eliminado con éxito')
                swal.close()   
            })
        })
    }
</script>