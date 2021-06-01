@extends('layouts.pdf')

@section('content') 
	<div>
		<div>			
			<b>Recibo N°:</b> {{str_pad($info[0]->nro_recibo, 6, '0', STR_PAD_LEFT)}} <br>    
			<b>Cliente:</b> {{$info[0]->apellido}} {{$info[0]->nombre}} <br>                       
			<b>Dirección:</b> {{$info[0]->calle}} {{$info[0]->numero}} - {{$info[0]->descripcion}}<br> 
	
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->fecha_recibo)->format('d-m-Y')}}			
			<br><br>
			@if($info[0]->entrega == 0) <p>Detalle de facturas canceladas </p>
			@else <p>Detalle de factura parcialmente cancelada </p>		
			@endif		
		</div>
		<div>
			<table class="table table-sm">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Fecha</th>
						<th class="text-left">N° Factura</th>
						<th class="text-right">Importe</th>
					</tr>
				</thead>

				<tbody style="font-size:12px">
					@foreach($info as $r)
					<tr>
						<td class="text-center">{{\Carbon\Carbon::parse($r->fecha)->format('d-m-Y')}}</td>
						<td class="text-left">FAC-{{str_pad($r->num_factura, 6, '0', STR_PAD_LEFT)}}</td>
						<td class="text-right mr-2">{{number_format($r->importe,2)}}</td>
					</tr>
					@endforeach
				</tbody>
			</table> 		                  
		</div><br>
		<div class="text-right mr-2">
			<b>Total Recibo: $  {{number_format($info[0]->total,2)}}</b>
		</div>
	</div>
@endsection
