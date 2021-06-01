@extends('layouts.pdf')

@section('content') 
	<div>
		<div>			
			<b>N° Control:</b>  {{$info[0]->id}}<br>                       
		@if($delivery)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}}<br>                       
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}}<br> 
		@endif	
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			<br><br>
		</div>
		<div>
			<table class="table table-sm">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-center">P Unit</th>
						<th class="text-center">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2)}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{$r->precio}}</td>
						<td class="text-right mr-2">{{number_format($r->importe,2)}}</td>
					</tr>
					@endforeach
				</tbody>
			</table> 		                  
		</div><br>
		<div class="text-right mr-2">
			<b>TOTAL: $  {{number_format($info[0]->importe,2)}}</b>
		</div>
		<br>
		<div class="text-center font-italic" style="font-size:14px">
        	<p>¡¡¡Muchas gracias por elegirnos!!! </p>
    	</div>
	</div>
@endsection
