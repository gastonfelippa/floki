<div class="col-sm-12 col-md-6 layout-spacing">      
	@include('common.alerts')
	<div class="widget-content-area">
		<div class="widget-one">
			<div class="row">
				<div class="col-6">
					<h3><b>Matriz BCG</b></h3>
				</div>	
				<div class="col-6 text-right">
					<button type="button" class="btn btn-warning" 
						@if($habilitar_botones) enabled @else disabled @endif
						onclick="doAction(4,0)">Ver detalles...
					</button>
					<button type="button" class="btn btn-dark"
						onclick="doAction(1,0)">Volver
					</button>								
				</div> 
			</div> 
			<div class="row mt-2">
				<div class="col-sm-7 col-md-7 text-left">
					<h5><b>Popularidad Media: {{number_format($mix_ideal_corregido,2,',','.')}} %</b></h5>							
					<h5><b>Margen Medio: {{number_format($total_mpp_por_producto,2,',','.')}} % </b></h5>							
				</div> 
				<div class="form-group col-sm-5 col-md-5">
					<div class="input-group">
						<select id="categoria" wire:model="categoria" class="form-control form-control-sm text-left">
							<option value="Elegir">Elegir Categoría</option>
							@foreach($categorias as $t)
							<option value="{{ $t->id }}">
								{{$t->descripcion}}                         
							</option> 
							@endforeach                         
						</select>	
					</div>	
				</div>
			</div>
			<div class="row">	
				<div class="table-responsive scroll px-2">
					@if($selector == '1')
					<table class="table table-hover table-checkable table-sm">
						<thead>
							<tr>
								<th class="text-left">DESCRIPCIÓN</th>				
								<th class="text-center">POPULARIDAD</th>									
								<th class="text-center">MARGEN</th>							
								<th class="text-left">CLASIFICACIÓN</th>							
								{{-- <th class="text-center">ACCIONES</th> --}}
							</tr>
						</thead>
						<tbody>
							@foreach($detalle_mpp_por_categoria as $r)
							<tr>
								<td>{{$r->descripcion}}</td>
								@if($r->popularidad)
								<td class="text-center" style="color: green;">{{number_format($r->participacion_en_ventas_por_cantidad,2,',','.')}} %</td>
								@else
								<td class="text-center" style="color: red;">{{number_format($r->participacion_en_ventas_por_cantidad,2,',','.')}} %</td>
								@endif
								@if($r->alto_margen)
								<td class="text-center" style="color: green;">{{number_format($r->margen_real_por_producto,2,',','.')}} %</td>	
								@else
								<td class="text-center" style="color: red;">{{number_format($r->margen_real_por_producto,2,',','.')}} %</td>	
								@endif
								<td>{{$r->clasificacion}}</td>
								{{-- <td class="text-center">
									<ul class="table-controls">
										<li>											
											<i class="bi bi-eye" wire:click= "doAction(4,0)" title="Ver detalle..."></i>
										</li>
									</ul>									
								</td> --}}
							</tr>
							@endforeach
						</tbody>						
					</table>
					@elseif($selector == '2')
					{{-- <table class="table table-hover table-checkable table-sm">
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
					</table> --}}
					@endif
				</div>
			</div>	
		</div>
	</div> 
</div>

