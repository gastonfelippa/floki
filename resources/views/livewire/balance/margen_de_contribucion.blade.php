<div class="row justify-content-center">  
	@include('common.alerts')
    <div class="col-12 layout-spacing">      
    	<div class="widget-content-area">
    		<div class="widget-one">
    			<div class="row">
    				<div class="col-sm-4 col-md-5 text-left">
    					<h3><b>Margen de Contribución</b></h3>						
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" wire:model="selector" value="1" checked>
							Por Producto</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" wire:model="selector" value="2">
							Por Categoría</div>						
    				</div> 
					<div class="col-sm-6 col-md-5">
						<p class="verde"><i class="bi bi-arrow-up-square mx-2"></i>Supera el 10% sugerido</p>
						<p class="verde-claro"><i class="bi bi-arrow-up-right-square mx-2"></i>Dentro del 10% sugerido</p>
						<p class="naranja"><i class="bi bi-arrow-down-right-square mx-2"></i>Dentro del 10% sugerido</p>
						<p class="rojo"><i class="bi bi-arrow-down-square mx-2"></i>Debajo del 10% sugerido</p>
					</div>						
					<div class="col-2 text-right">
						<button type="button" class="btn btn-dark mr-1">
							<a style="color: white" href="{{ url('/home') }}">Volver</a>                          
                        </button>
                    </div> 
    			</div>
				<div class="row">	
					<div class="table-responsive scroll px-2">
						@if($selector == '1')
						<table class="table table-hover table-checkable table-sm">
							<thead>
								<tr>
									<th class="text-left">DESCRIPCIÓN</th>
									<th class="text-center mr-4">MARGEN DESEADO L1</th>							
									<th class="text-center mr-4">MARGEN ACTUAL L1</th>							
									<th class="text-center mr-4">MARGEN DESEADO L2</th>							
									<th class="text-center mr-4">MARGEN ACTUAL L2</th>	
									<th class="text-center">ACCIONES</th>
								</tr>
							</thead>
							<tbody>
								@foreach($info as $r)
								<tr>
									<td>{{$r->descripcion}}</td>
									<td class="text-center">{{$r->margen_1}} %</td>
									@if($r->diferencia_margen_1 == '>>')
									<td class="text-center" style="background-color: rgba(8, 153, 8, 0.973);color:white;">{{$r->margen_actual_l1}} %</td>
									@elseif($r->diferencia_margen_1 == '>=')
									<td class="text-center" style="background-color: rgb(74, 212, 74);color:white;">{{$r->margen_actual_l1}} %</td>
									@elseif($r->diferencia_margen_1 == '<<')
									<td class="text-center" style="background-color: rgb(248, 7, 7);color:white;">{{$r->margen_actual_l1}} %</td>
									@else
									<td class="text-center" style="background-color: rgb(253, 167, 8);color:white;">{{$r->margen_actual_l1}} %</td>
									@endif
									<td class="text-center">{{$r->margen_2}} %</td>
									@if($r->diferencia_margen_2 == '>>')
									<td class="text-center" style="background-color: rgba(8, 153, 8, 0.973);color:white;">{{$r->margen_actual_l2}} %</td>
									@elseif($r->diferencia_margen_2 == '>=')
									<td class="text-center" style="background-color: rgb(74, 212, 74);color:white;">{{$r->margen_actual_l2}} %</td>
									@elseif($r->diferencia_margen_2 == '<<')
									<td class="text-center" style="background-color: rgb(248, 7, 7);color:white;">{{$r->margen_actual_l2}} %</td>
									@else
									<td class="text-center" style="background-color: rgb(253, 167, 8);color:white;">{{$r->margen_actual_l2}} %</td>
									@endif
									<td class="text-center">
										<ul class="table-controls">
											<li>
												<a href="javascript:void(0);" 
												onClick="openModal({{$r->id}},'{{$r->descripcion}}',{{$r->precio_venta_sug_l1}},{{$r->precio_venta_l1}},{{$r->precio_venta_sug_l2}},{{$r->precio_venta_l2}})" 
												data-toggle="tooltip" data-placement="top" title="Editar Precio de Lista Actual"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
											</li>
										</ul>									
									</td>
								</tr>
								@endforeach
							</tbody>
							<!-- <thead>
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
									<td class="text-center" style="background-color: green;color:white;">{{$r->margen_actual_1}} %</td>
									@else
									<td class="text-center" style="background-color: red;color:white;">{{$r->margen_actual_1}} %</td>
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
							</tbody> -->
						</table>
						@elseif($selector == '2')
						<table class="table table-hover table-checkable table-sm">
							<thead>
								<tr>
									<th class="text-left">DESCRIPCIÓN</th>
									<th class="text-center">MARGEN DESEADO LISTA 1</th>							
									<th class="text-center">MARGEN ACTUAL LISTA 1</th>							
									<th class="text-center">MARGEN DESEADO LISTA 2</th>							
									<th class="text-center">MARGEN ACTUAL LISTA 2</th>	
								</tr>
							</thead>
							<tbody>
								@foreach($info as $r)
								<tr>
									<td>{{$r->descripcion}}</td>									
									<td class="text-center">{{$r->margen_1}} %</td>								
									@if($r->diferencia_margen_1 == '>=')
									<td class="text-center" style="background-color: green;color:white;">{{$r->promedio_por_categoria_l1}} %</td>
									@else
									<td class="text-center" style="background-color: red;color:white;">{{$r->promedio_por_categoria_l1}} %</td>
									@endif
									<td class="text-center">{{$r->margen_2}} %</td>	
									@if($r->diferencia_margen_2 == '>=')
									<td class="text-center" style="background-color: green;color:white;">{{$r->promedio_por_categoria_l2}} %</td>
									@else
									<td class="text-center" style="background-color: red;color:white;">{{$r->promedio_por_categoria_l2}} %</td>
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

