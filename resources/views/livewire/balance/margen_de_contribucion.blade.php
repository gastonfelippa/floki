<div class="col-sm-12 col-md-9 layout-spacing">      
	@include('common.alerts')
	<div class="widget-content-area">
		<div class="widget-one">
			<div class="row container">
				<div class="col-10">
					<h3><b>Margen de Contribución de Listas Actuales</b></h3>
				</div>
				<div class="col-2 text-right">
					<button type="button" class="btn btn-dark mr-1"
						onclick="doAction(1,0)">Volver       
					</button>
				</div> 
			</div>
			<div class="row mt-2">	
				<div class="col-sm-12 col-md-5 text-left">
					<div style="border: 1px solid #000000;border-radius: 5px;">
						<div class="row px-2 pt-1">
							<div class="col-8">Margen Deseado Lista Salón</div>
							<div class="col-4 text-center">{{number_format($promedio_margen_deseado_1,2)}}%</div> 
						</div>
						<div class="row px-2">
							<div class="col-8"><b>Margen Actual Lista Salón</b></div>
							<div class="col-4 text-right"><b>{{number_format($promedio_margen_l1,2)}}%</b>
								@if($margen1) <i class="bi bi-hand-thumbs-up-fill" style="color:green;"></i>
								@else  <i class="bi bi-hand-thumbs-down-fill" style="color:red;"></i>
								@endif
							</div>
						</div>							
						<div class="row px-2 mt-2">
							<div class="col-8">Margen Deseado Lista Delivery</div>
							<div class="col-4 text-center">{{number_format($promedio_margen_deseado_2,2)}}%</div>
						</div>						
						<div class="row px-2 pb-1">
							<div class="col-8"><b>Margen Actual Lista Delivery</b></div>
							<div class="col-4 text-right"><b>{{number_format($promedio_margen_l2,2)}}%</b>
								@if($margen2) <i class="bi bi-hand-thumbs-up-fill" style="color:green;"></i>
								@else  <i class="bi bi-hand-thumbs-down-fill" style="color:red;"></i>
								@endif
							</div>
						</div>
					</div>						
					<div class="form-check form-check-inline mt-2">
						<input id="por_producto" class="form-check-input" type="radio" wire:model="selector" value="1" checked>
						Por Producto
					</div>
					<div class="form-check form-check-inline mt-2">
						<input id="por_categoria" class="form-check-input" type="radio" wire:model="selector" value="2">
						Por Categoría
					</div>						
				</div> 
				<div class="col-sm-12 col-md-4">
					<p class="verde text-center"><i class="bi bi-arrow-up-square mx-2"></i>Supera el 10% sugerido</p>
					<p class="verde-claro text-center"><i class="bi bi-arrow-up-right-square mx-2"></i>Dentro del 10% sugerido</p>
					<p class="naranja text-center"><i class="bi bi-arrow-down-right-square mx-2"></i>Dentro del 10% sugerido</p>
					<p class="rojo text-center"><i class="bi bi-arrow-down-square mx-2"></i>Debajo del 10% sugerido</p>
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

