<div class="col-sm-12 col-md-10 layout-spacing">      
	@include('common.alerts')
	<div class="widget-content-area">
		<div class="widget-one">
			<div class="row">
				<div class="col-10 text-left">
					<h3>Detalle Margen Promedio Ponderado de <b>{{$productoDesc}}</b></h3>									 
					<h6>Margen de Contribución Promedio Ponderado en Pesos:<b> $ {{number_format($margen_promedio_ponderado_por_producto_en_pesos,2,',','.')}}</b>
						<i class="bi bi-info-circle ml-2 asterisco"
                        data-toggle="tooltip" data-placement="top"
                        title="Ver datos de la fórmula" onclick="ver_formula_en_pesos()"></i></h6>							
					<h6>Margen de Contribución Promedio Ponderado en Porcentaje:<b> {{number_format($margen_promedio_ponderado_por_producto_en_porcentaje,2,',','.')}} %</b>
						<i class="bi bi-info-circle ml-2 asterisco"
                        data-toggle="tooltip" data-placement="top"
                        title="Ver datos de la fórmula" onclick="ver_formula_en_porcentaje()"></i></h6>							
				</div> 
				<div class="col-2 text-right">
					<button type="button" class="btn btn-dark mr-1"
						onclick="doAction(4,0)">Volver        
					</button>
				</div> 
			</div>
			<div class="row">	
				<div class="table-responsive scroll px-2">
					<table class="table table-hover table-checkable table-sm">
						<thead>
							<tr>
								<th class="text-center">FECHA</th>
								<th class="text-center">CANTIDAD</th>				
								<th class="text-center">PR. VENTA L1</th>				
								<th class="text-center">PR. COSTO</th>							
								<th class="text-center">MARGEN</th>							
								<th class="text-center">CANT. * PR VENTA L1</th>							
								<th class="text-center">CANT. * MARGEN</th>		
							</tr>
						</thead>
						<tbody>
							@foreach($detalle_mpp_por_producto as $r)
							<tr>
								<td class="text-center">{{$r->fecha}}</td>
								<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
								<td class="text-center">{{number_format($r->precio,2,',','.')}}</td>
								<td class="text-center">{{number_format($r->costo,2,',','.')}}</td>
								<td class="text-center">{{number_format($r->margen,2,',','.')}}</td>
								<td class="text-center">{{number_format($r->cantidad_por_precio,2,',','.')}}</td>
								<td class="text-center">{{number_format($r->cantidad_por_margen,2,',','.')}}</td>
							</tr>
							@endforeach
						</tbody>						
					</table>					
				</div>
			</div>	
		</div>
	</div> 
	<input id="total_dividendo" type="hidden" wire:model="total_margen_por_producto">
	<input id="total_divisor" type="hidden" wire:model="total_cantidad_vendida_por_producto">
	<input id="total_resultado" type="hidden" wire:model="margen_promedio_ponderado_por_producto_en_pesos">

	<input id="total_divisor_porcentaje" type="hidden" wire:model="total_cantidad_por_venta">
	<input id="total_resultado_porcentaje" type="hidden" wire:model="margen_promedio_ponderado_por_producto_en_porcentaje">
</div>


