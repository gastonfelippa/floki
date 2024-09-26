@extends('layouts.pdf')

@section('content')
<div>
	<h5 class="text-center">Resumen de Cuenta Corriente</h5>
	<div><span class="text-left" style="font-weight: bold;font-size:14px;">Cliente: {{$info[0]->apellido}} {{$info[0]->nombre}}</span></div>
	<div><span class="text-left" style="font-size:14px;">Domicilio: {{$info[0]->calle}} {{$info[0]->numero}} - {{$info[0]->localidad}}</span></div>
	<span style="font-size:12px;">Fecha: {{\Carbon\Carbon::now()->format('d-m-Y')}}</span><br>
		
	@if($importeEntrega > 0)
	<div class="row">
		<div class="col-12 text-left" style="font-size:12px;">		
			<span>Facturas en cuenta corriente......$ {{number_format($totalCli,2,',','.')}}</span>
		</div>
	</div>
	<div class="row">
		<div class="col-12 text-left" style="font-size:12px;">		
			<span>Pagos a cuenta...........................$ ({{number_format($importeEntrega,2,',','.')}})</span>
		</div>
	</div>
	@endif
	<div class="row">
		<div class="col-12 text-left" style="font-weight: bold;font-size:14px;">	
			<span>Saldo...................................$ {{number_format($saldo,2,',','.')}}</span>
		</div>
	</div>
    <div class="table-responsive mt-2">
		<table class="table table-sm">
			<thead style="font-size:12px">
				<tr>
					<th class="text-left">FECHA</th>
					<th class="text-left">COMPROBANTE</th>
                    <th class="text-right">IMPORTE</th>
				</tr>
			</thead>
			<tbody style="font-size:12px">
				@foreach($info as $r)
				<tr>
				    <!-- <td class="text-left" style="width: 80px;">{{\Carbon\Carbon::parse(strtotime($r->fecha))->format('d-m-Y')}}</td> -->
				    <td class="text-left" style="width: 80px;">{{\Carbon\Carbon::parse($r->fecha)->format('d-m-Y')}}</td>
					@if($r->importe_factura == 1)
						<td class="text-center" style="width: 100px;">FAC-{{str_pad($r->numero, 6, '0', STR_PAD_LEFT)}}</td>
					@elseif( $r->importe_factura == 2)
						<td class="text-center" style="width: 100px;font-weight: bold;">FAC-{{str_pad($r->numero, 6, '0', STR_PAD_LEFT)}} (resto $ {{number_format($r->resto,2)}})</td>
					@endif
					<td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
                </tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endsection