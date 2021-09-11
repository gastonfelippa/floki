@extends('layouts.pdf')

@section('content') 
<div>
@include('common.alerts')
@include('common.messages')
@if($impPorHoja == 1 && $impDuplicado == '0')
	<div class="col-12">
		<div>			
			<b>N° Control:</b>  {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Original<br>                       
			@if($delivery)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}}<br>                       
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}}<br> 
			@endif	
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			<br><br>
		</div>
		<div>
			<table class="table table-sm alturaFull">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-center">P. Unit</th>
						<th class="text-center">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{$r->precio}}</td>
						<td class="text-right mr-2">{{number_format($r->importe,2,',','.')}}</td>
					</tr>
					@endforeach
				</tbody>
			</table> 		                  
		</div>
		<br>
		<div class="text-right mr-2">
			<b>TOTAL: $  {{number_format($info[0]->importe,2,',','.')}}</b>
		</div>
		<br>
		<div class="text-center font-italic" style="font-size:14px">
			<p>{{$leyendaFactura}}</p>
		</div>
	</div>
@elseif($impPorHoja == 1 && $impDuplicado == '1')
	<div class="col-12">
		<div>	
			<b>N° Control:</b>  {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Original<br>                       
			@if($delivery)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}}<br>                       
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}}<br> 
			@endif	
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			<br><br>
		</div>
		<div>
			<table class="table table-sm alturaFull">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-center">P. Unit</th>
						<th class="text-center">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{$r->precio}}</td>
						<td class="text-right mr-2">{{number_format($r->importe,2,',','.')}}</td>
					</tr>
					@endforeach
				</tbody>
			</table> 		                  
		</div><br>
		<div class="text-right mr-2">
			<b>TOTAL: $  {{number_format($info[0]->importe,2,',','.')}}</b>
		</div>
		<br>
		<div class="text-center font-italic" style="font-size:14px">
			<p>{{$leyendaFactura}}</p><br>
		</div>
		<!-- .....................duplicado................ -->
		<div>			
			<b>N° Control:</b>  {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Duplicado<br>                       
			@if($delivery)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}}<br>                       
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}}<br> 
			@endif	
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			<br><br>
		</div>
		<div>
			<table class="table table-sm alturaFull">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-center">P. Unit</th>
						<th class="text-center">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{$r->precio}}</td>
						<td class="text-right mr-2">{{number_format($r->importe,2,',','.')}}</td>
					</tr>
					@endforeach
				</tbody>
			</table> 		                  
		</div><br>
		<div class="text-right mr-2">
			<b>TOTAL: $  {{number_format($info[0]->importe,2,',','.')}}</b>
		</div>
		<div class="text-center font-italic" style="font-size:14px">
			<p>{{$leyendaFactura}}</p>
		</div>
	</div>
@elseif($impPorHoja == 2 && $impDuplicado == '0')
	<div class="col-12">
		<div>			
			<b>Comprobante: </b>F - {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Original<br>                       
			@if($delivery)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}}<br>                       
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}}<br> 
			@endif	
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			<br><br>
		</div>
		<div>
			<table class="table table-sm alturaMedia">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-right">Pr. Unitario</th>
						<th class="text-right">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-3">{{$r->precio}}</td>
						<td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
					</tr>
					@endforeach
				</tbody>
			</table> 		                  
		</div><br>
		<div class="text-right mr-2">
			<b>TOTAL: $  {{number_format($info[0]->importe,2,',','.')}}</b>
		</div>
		<br>
		<div class="text-center font-italic" style="font-size:14px">
			<p>{{$leyendaFactura}}</p>
		</div>
	</div>
@elseif($impPorHoja == 2 && $impDuplicado == '1')
	<div class="col-12">
		<div>	
			<b>N° Control:</b>  {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Original<br>                       
			@if($delivery)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}}<br>                       
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}}<br> 
			@endif	
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			<br>
		</div>
		<div>
			<table class="table table-sm alturaMedia">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-center">P. Unit</th>
						<th class="text-center">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{$r->precio}}</td>
						<td class="text-right mr-2">{{number_format($r->importe,2,',','.')}}</td>
					</tr>
					@endforeach
				</tbody>
			</table> 		                  
		</div><br>
		<div class="text-right mr-2">
			<b>TOTAL: $  {{number_format($info[0]->importe,2,',','.')}}</b>
		</div>
		<div class="text-center font-italic" style="font-size:14px">
			<p>{{$leyendaFactura}}</p>
		</div>
		<hr style="border:1px dotted; width:600px" />
		<!-- .....................duplicado................ -->
		<div>			
			<b>N° Control:</b>  {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Duplicado<br>                       
			@if($delivery)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}}<br>                       
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}}<br> 
			@endif	
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			<br>
		</div>
		<div>
			<table class="table table-sm alturaMedia">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-center">P. Unit</th>
						<th class="text-center">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{$r->precio}}</td>
						<td class="text-right mr-2">{{number_format($r->importe,2,',','.')}}</td>
					</tr>
					@endforeach
				</tbody>
			</table> 		                  
		</div><br>
		<div class="text-right mr-2">
			<b>TOTAL: $  {{number_format($info[0]->importe,2,',','.')}}</b>
		</div>
		<div class="text-center font-italic" style="font-size:14px">
			<p>{{$leyendaFactura}}</p>
		</div>
	</div>
@elseif($impPorHoja == 4 && $impDuplicado == '0')
	<div class="col-5 offset-3">
		<div>			
			<b>N° Control:</b>  {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Original<br>                       
			@if($delivery)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}}<br>                       
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}}<br> 
			@endif	
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			<br>
		</div>
		<div>
			<table class="table table-sm alturaMedia">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-center">P. Unit</th>
						<th class="text-center">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{$r->precio}}</td>
						<td class="text-right mr-2">{{number_format($r->importe,2,',','.')}}</td>
					</tr>
					@endforeach
				</tbody>
			</table> 		                  
		</div>
		<br>
		<div class="text-right mr-2">
			<b>TOTAL: $  {{number_format($info[0]->importe,2,',','.')}}</b>
		</div>
		<div class="text-center font-italic" style="font-size:14px">
			<p>{{$leyendaFactura}}</p>
		</div>
	</div>
@elseif($impPorHoja == 4 && $impDuplicado == '1')
	<div class="col-5 offset-3">
		<div>			
			<b>N° Control:</b>  {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Original<br>                       
			@if($delivery)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}}<br>                       
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}}<br> 
			@endif	
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			<br>
		</div>
		<div>
			<table class="table table-sm alturaMedia">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-center">P. Unit</th>
						<th class="text-center">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{$r->precio}}</td>
						<td class="text-right mr-2">{{number_format($r->importe,2,',','.')}}</td>
					</tr>
					@endforeach
				</tbody>
			</table> 		                  
		</div>
		<br>
		<div class="text-right mr-2">
			<b>TOTAL: $  {{number_format($info[0]->importe,2,',','.')}}</b>
		</div>
		<div class="text-center font-italic" style="font-size:14px">
			<p>{{$leyendaFactura}}</p>
		</div>	
		<!-- </div><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br> -->
		<!-- .....................duplicado................ -->
		<div>			
			<b>N° Control:</b>  {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Duplicado<br>                       
			@if($delivery)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}}<br>                       
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}}<br> 
			@endif	
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			<br>
		</div>
		<div>
			<table class="table table-sm alturaMedia">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-right">P. Unit</th>
						<th class="text-right">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right">{{$r->precio}}</td>
						<td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
					</tr>
					@endforeach
				</tbody>
			</table> 		                  
		</div>
		<br>
		<div class="text-right mr-2">
			<b>TOTAL: $  {{number_format($info[0]->importe,2,',','.')}}</b>
		</div>
		<div class="text-center font-italic" style="font-size:14px">
			<p>{{$leyendaFactura}}</p>
		</div>
	</div>
@endif
</div>
@endsection

<style type="text/css" scoped>
	.alturaMedia{
			position: relative;
			height: 290px;
			margin-top: .5rem;
			overflow: auto;
    }
	.alturaFull{
			position: relative;
			height: 700px;
			margin-top: .5rem;
			overflow: auto;
    }
</style>