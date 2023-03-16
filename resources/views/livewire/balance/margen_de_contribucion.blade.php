<div class="row justify-content-center">  
	@include('common.alerts')
    <div class="col-11">      
    	<div class="widget-content-area">
    		<div class="widget-one">
    			<div class="row">
    				<div class="col-9 text-left">
    					<h3><b>Margen de Contribución</b></h3>
    				</div> 
						
					<div class="col-3 text-right">
                        <button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">
                            Volver
                        </button>
                    </div> 
    			</div>
				<div class="row">
					<div class="col-12 text-left">
                        <div class="form-check form-check-inline mb-1 p-1">
                            <input class="form-check-input" type="radio" wire:model="selector" value="1" checked>
                            Por Producto</div>
                        <div class="form-check form-check-inline mb-1 p-1">
                            <input class="form-check-input" type="radio" wire:model="selector" value="2">
                            Por Categoría</div>
                        <!-- <div class="form-check form-check-inline mb-1 p-1">
                            <input class="form-check-input" type="radio" wire:model="selector" value="3">
                            Por Rubro</div> -->
					</div>
				</div>
				<div class="row">	
					<div class="table-responsive scroll px-2">
						@if($selector == '1')
						<table class="table table-hover table-checkable table-sm">
							<thead>
								<tr>
									<th class="text-left">DESCRIPCIÓN</th>
									<th class="text-center">MARGEN DESEADO</th>							
									<th class="text-center">PRECIO DE COSTO</th>
									<th class="text-center">PRECIO DE LISTA SUGERIDO</th>
									<th class="text-center">PRECIO DE LISTA ACTUAL</th>
									<th class="text-center">MARGEN ACTUAL</th>
									<th class="text-center">ACCIONES</th>
								</tr>
							</thead>
							<tbody>
								@foreach($info as $r)
								<tr>
									<td>{{$r->descripcion}}</td>
									<td class="text-center">{{$r->margen_1}} %</td>
									<td class="text-center">{{number_format($r->precio_costo,2,',','.')}}</td>
									<td class="text-center">{{number_format($r->precio_venta_sug_l1,2,',','.')}}</td>
									<td class="text-center">{{number_format($r->precio_venta_l1,2,',','.')}}</td>
									@if($r->diferencia_margen == '>=')
									<td class="text-center" style="background-color: green;color:white;">{{$r->margen_actual}} %</td>
									@else
									<td class="text-center" style="background-color: red;color:white;">{{$r->margen_actual}} %</td>
									@endif
									<td class="text-center">
										<ul class="table-controls">
											<li>
												<a href="javascript:void(0);" 
												onClick="openModal({{$r->id}},'{{$r->descripcion}}',{{$r->precio_venta_sug_l1}},{{$r->precio_venta_l1}})" 
												data-toggle="tooltip" data-placement="top" title="Editar Precio de Lista Actual"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
											</li>
										</ul>									
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						@elseif($selector == '2')
						<table class="table table-hover table-checkable table-sm">
							<thead>
								<tr>
									<th class="text-left">DESCRIPCIÓN</th>
									<th class="text-center" style="width:250px;">MARGEN DESEADO</th>									
									<th class="text-center" style="width:250px;">MARGEN ACTUAL</th>
								</tr>
							</thead>
							<tbody>
								@foreach($info as $r)
								<tr>
									<td>{{$r->descripcion}}</td>									
									<td class="text-center">{{$r->margen_1}} %</td>								
									@if($r->diferencia_margen == '>=')
									<td class="text-center" style="background-color: green;color:white;">{{$r->promedio_por_categoria}} %</td>
									@else
									<td class="text-center" style="background-color: red;color:white;">{{$r->promedio_por_categoria}} %</td>
									@endif
								</tr>
								@endforeach
							</tbody>
						</table>
						@endif
					</div>
				</div>	
			</div>
    	</div> 
    </div>
</div>

