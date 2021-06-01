<div class="main-content">
    @if($action == 1)
    <div class="layout-pc-spacing">    	
		<div class="row layout-top-spacing">	
			<div class="col-xs-12 col-lg-12 col-md-12 col-sm-12 layout-spacing">		
				<div class="widget-content-area br-4">
					<div class="widget-one">
						<h3>Movimientos De Caja</h3>
						@include('common.search')
                        @include('common.messages')
						@include('common.alerts')
						<div class="table-resposive">
							<table class="table table-bordered table-hover table-striped table-checkable table-highlight-head mb-4">
								<thead>
									<tr>
										<th class="text-center">DESCRIPCIÓN</th>
										<th class="text-center">TIPO</th>
										<th class="text-center">MONTO</th>
										<th class="text-center">FECHA</th>
										<th class="text-center">USUARIO</th>
										<th class="text-center">ACCIONES</th>
									</tr>
								</thead>
								<tbody>
									@foreach($info as $r)
									<tr>
										<td class="text-center">{{$r->concepto}}</td>
										<td class="text-center">{{$r->tipo}}</td>
										<td class="text-center">{{$r->monto}}</td>
										<td class="text-center">{{$r->created_at}}</td>
										<td class="text-center">{{$r->nombre}}</td>
										<td class="text-center">
											@include('common.actions', ['edit' => 'Movimientos_edit', 'destroy' => 'Movimientos_destroy'])
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
							{{$info->links()}}
						</div>
					</div>			
				</div>
			</div>
		</div>
    </div>
    @elseif($action > 1)
    @include('livewire.movimientos.form')
    @endif
</div>

<script>
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
			console.log('ID', id);
			window.livewire.emit('deleteRow', id)    
			toastr.success('info', 'Registro eliminado con éxito')
			swal.close()   
		})
   	}
</script>