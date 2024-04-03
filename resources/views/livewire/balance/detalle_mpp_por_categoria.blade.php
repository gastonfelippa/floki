<div class="col-12 layout-spacing">      
	@include('common.alerts')
	<div class="widget-content-area">
		<div class="widget-one">
			<div class="row">
				<div class="col-sm-10 col-md-10 text-left">
					<h3>Detalle Margen Promedio Ponderado de la Categoría <b>{{$categoriaDesc}}</b></h3>		
					<h6>Popularidad Promedio<b>: {{number_format($mix_ideal_corregido,2,',','.')}} %</b>
						<i class="bi bi-info-circle ml-2 asterisco"
                        data-toggle="tooltip" data-placement="top"
                        title="Ver datos de la fórmula" onclick="ver_formula_popularidad()"></i></h6>								
					<h6>Margen de Contribución Promedio Ponderado:<b> {{number_format($total_mpp_por_producto,2,',','.')}} %</b>
						<i class="bi bi-info-circle ml-2 asterisco"
                        data-toggle="tooltip" data-placement="top"
                        title="Ver datos de la fórmula" onclick="ver_formula_margen()"></i></h6>
				</div> 
				<div class="col-2 text-right">
					<button type="button" class="btn btn-dark mr-1"
						onclick="doAction(3,0)">Volver                 
					</button>
				</div> 
			</div>
			<div class="row">	
				<div class="table-responsive scroll px-2">
					<table class="table table-hover table-checkable table-sm">
						<thead>
							<tr>
								<th class="text-left">DESCRIPCIÓN</th>
								<th class="text-center">CANTIDAD VENDIDA</th>				
								<th class="text-center">MCPP $</th>				
								<th class="text-center">CANT * MCPP $</th>							
								<th class="text-center">TOTAL VENTAS</th>							
								<th class="text-center">MARGEN REAL P/PROD</th>							
								<th class="text-center">PARTICIP EN VENTAS</th>							
								<th class="text-center">MCPP P/PROD</th>							
								<th class="text-center">VER DETALLES...</th>
							</tr>
						</thead>
						<tbody>
							@foreach($detalle_mpp_por_categoria as $r)
							<tr>
								<td>{{$r->descripcion}}</td>
								<td class="text-center">{{number_format($r->cantidad_vendida,2,',','.')}}</td>
								<td class="text-center">{{number_format($r->mpp_en_pesos,2,',','.')}}</td>
								<td class="text-center">{{number_format($r->cantidad_por_mpp_en_pesos,2,',','.')}}</td>
								<td class="text-center">{{number_format($r->total_venta_por_producto,2,',','.')}}</td>
								<td class="text-center">{{number_format($r->margen_real_por_producto,2,',','.')}} %</td>
								<td class="text-center">{{number_format($r->participacion_en_ventas_por_importe,2,',','.')}} %</td>
								<td class="text-center">{{number_format($r->mpp_por_producto,2,',','.')}} %</td>
								<td class="text-center">
									<ul class="table-controls">
										<li>											
											<i class="bi bi-eye text-success" wire:click= "doAction(6,{{$r->id}})" 
											style="font-size: 25px;" title="Ver detalle..."></i>
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
	<input id="cantidadProductos" type="hidden" wire:model="cantidad_productos_por_categoria">
	<input id="mixIdeal" type="hidden" wire:model="mix_ideal">
	<input id="mixIdealCorregido" type="hidden" wire:model="mix_ideal_corregido">
	<input id="totalMcpp" type="hidden" wire:model="total_mpp_por_producto">
</div>

