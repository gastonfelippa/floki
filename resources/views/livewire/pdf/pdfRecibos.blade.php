@extends('layouts.pdf')

@section('content') 
	<div>
		<div>			
			<b>Recibo N°:</b> {{str_pad($info_factura[0]->nro_recibo, 6, '0', STR_PAD_LEFT)}} <br>    
			<b>Cliente:</b> {{$info_factura[0]->apellido}} {{$info_factura[0]->nombre}} <br>                       
			<b>Dirección:</b> {{$info_factura[0]->calle}} {{$info_factura[0]->numero}} - {{$info_factura[0]->descripcion}}<br> 
	
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info_factura[0]->fecha_recibo)->format('d-m-Y')}}			
			<br><br>
			@if($info_factura[0]->entrega == 0) <h5>Detalle de Comprobantes </h5>
			@else <h5>Detalle de factura con pago a cuenta </h5>		
			@endif		
		</div>
		<div>
			<table class="table table-sm">
				<thead style="font-size:14px">
					<tr>
						<th>Fecha</th>
						<th>Comprobante</th>
						<th class="text-right">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($info_factura as $r)
					<tr>
						<td>{{\Carbon\Carbon::parse($r->fecha_fac)->format('d-m-Y')}}</td>
						<td>FAC-{{str_pad($r->num_factura, 6, '0', STR_PAD_LEFT)}}</td>
						<td class="text-right">{{number_format($r->importe_fac,2,',','.')}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<div class="text-right">
				<b>Total Comprobantes: $  {{number_format($total_comprobantes,2,',','.')}}</b>
			</div> 		                  
		</div><br>
		<!-- <hr style="border:1px dotted; width:300px" /> -->
		<h5>Detalle de valores</h5>
		<div>
			<table class="table table-sm">
				<thead style="font-size:14px">
					<tr>
						<th>Fecha</th>
						<th>Medio de Pago</th>
						<th class="text-right">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($info_recibo as $r)
					<tr>
						<td>{{\Carbon\Carbon::parse($r->fecha_rec)->format('d-m-Y')}}</td>
						<td>{{$r->medio_de_pago}}</td>
						<td class="text-right">{{number_format($r->importe_rec,2,',','.')}}</td>
					</tr>
					@endforeach
				</tbody>
			</table> 
			@if($mas_de_un_pago_a_cuenta == 1)		                  
			<div class="text-right">
				<b>Total Pagos a Cuenta: $  {{number_format($total_pagos_a_cuenta,2,',','.')}}</b>
			</div>
			@endif
		</div>
		<hr style="border:1px dotted; width:300px" />
		<div>
			<span style="font-size:14px">Son: {{$numeroEnLetra}}</span><br>
		</div>
		<div class="text-right">
			<h5>Total Recibo: $  {{number_format($total_recibo,2,',','.')}}</h5>
		</div>
	</div>	
@endsection
