@extends('layouts.pdf')

@section('content') 
<div>
@include('common.alerts')
@include('common.messages')
@if($impPorHoja == '1' && $impDuplicado == 0)
	<div class="col-12">
		<div>			
			<b>Comprobante: </b>F - {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Original<br>                       
			@if($cliente)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}} <br> 
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}} - {{$info[0]->localidad}}<br> 
			@endif
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			@if($repartidor)
			<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rep:</b> {{$info[0]->repartidor_id}}<br>
			@endif	
			<br>
		</div>
		<div>
			<table class="table table-sm alturaFull">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-right mr-2">P. Unit</th>
						<th class="text-right mr-2">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{number_format($r->precio,2,',','.')}}</td>
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
@elseif($impPorHoja == '1' && $impDuplicado == 1)
	<div class="col-12">
		<div>	
			<b>Comprobante: </b>F - {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Original<br>                       
			@if($cliente)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}} <br> 
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}} - {{$info[0]->localidad}}<br> 
			@endif
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			@if($repartidor)
			<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rep:</b> {{$info[0]->repartidor_id}}<br>
			@endif				
			<br>
		</div>
		<div>
			<table class="table table-sm alturaFull">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-right mr-2">P. Unit</th>
						<th class="text-right mr-2">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{number_format($r->precio,2,',','.')}}</td>
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
		<div class="pagebreak"> </div>
		<!-- .............................................. -->
		<div>			
			<b>Comprobante: </b>F - {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Duplicado<br>                       
			@if($cliente)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}} <br> 
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}} - {{$info[0]->localidad}}<br> 
			@endif
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			@if($repartidor)
			<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rep:</b> {{$info[0]->repartidor_id}}<br>
			@endif				
			
		</div>
		<div>
			<table class="table table-sm alturaFull">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-right mr-2">P. Unit</th>
						<th class="text-right mr-2">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{number_format($r->precio,2,',','.')}}</td>
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
@elseif($impPorHoja == '2' && $impDuplicado == 0)
	<div class="col-12">
		<div>			
			<b>Comprobante: </b>F - {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Original<br>                       
			@if($cliente)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}} <br> 
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}} - {{$info[0]->localidad}}<br> 
			@endif
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			@if($repartidor)
			<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rep:</b> {{$info[0]->repartidor_id}}<br>
			@endif				
			<br><br>
		</div>
		<div>
			<table class="table table-sm alturaMedia">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-right mr-2">P. Unit</th>
						<th class="text-right mr-2">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-3">{{number_format($r->precio,2,',','.')}}</td>
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
@elseif($impPorHoja == '2' && $impDuplicado == 1)
	<div class="col-12">
		<div>	
			<b>Comprobante: </b>F - {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Original<br>                       
			@if($cliente)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}} <br> 
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}} - {{$info[0]->localidad}}<br> 
			@endif
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			@if($repartidor)
			<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rep:</b> {{$info[0]->repartidor_id}}<br>
			@endif				
			<br>
		</div>
		<div>
			<table class="table table-sm alturaMedia">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-right mr-2">P. Unit</th>
						<th class="text-right mr-2">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{number_format($r->precio,2,',','.')}}</td>
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
		<!-- .....................duplicado................ -->
		<div class="pagebreak"> </div>
		<!-- .............................................. -->
		<div>			
		<b>Comprobante: </b>F - {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Duplicado<br>                       
			@if($cliente)
			<b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}} <br> 
			<b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}} - {{$info[0]->localidad}}<br> 
			@endif
			<b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			@if($repartidor)
			<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rep:</b> {{$info[0]->repartidor_id}}<br>
			@endif				
			<br>
		</div>
		<div>
			<table class="table table-sm alturaMedia">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-right mr-2">P. Unit</th>
						<th class="text-right mr-2">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{number_format($r->precio,2,',','.')}}</td>
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
@elseif($impPorHoja == '4' && $impDuplicado == 0)
	<div class="col-7 offset-2">
		<div>			
			<b style="font-size:14px">Comprobante: </b>F - {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Original<br>                       
			@if($cliente)
			<b style="font-size:14px">Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}} <br> 
			<b style="font-size:14px">Dir:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}} - {{$info[0]->localidad}}<br> 
			@endif
			<b style="font-size:14px">Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			@if($repartidor)
			<b style="font-size:14px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rep:</b> {{$info[0]->repartidor_id}}<br>
			@endif	
			@if($mesa)
			<br><b style="font-size:14px">Mesa N°:</b> {{$mesa}}&nbsp;&nbsp;&nbsp;&nbsp;<b style="font-size:14px">Mozo N°:</b> {{$mozo}}<br>
			@endif	
		</div>
		<div>
			<table class="table table-sm alturaMedia">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-right mr-2">P. Unit</th>
						<th class="text-right mr-2">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{number_format($r->precio,2,',','.')}}</td>
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
	<div class="col-7 offset-2">
		<div>			
			<b style="font-size:14px">Comprobante: </b>F - {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Original<br>                       
			@if($cliente)
			<b style="font-size:14px">Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}} <br> 
			<b style="font-size:14px">Dir:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}} - {{$info[0]->localidad}}<br> 
			@endif
			<b style="font-size:14px">Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			@if($repartidor)
			<b style="font-size:14px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rep:</b> {{$info[0]->repartidor_id}}<br>
			@endif	
			@if($mesa)
			<br><b style="font-size:14px">Mesa N°:</b> {{$mesa}}&nbsp;&nbsp;&nbsp;&nbsp;<b style="font-size:14px">Mozo N°:</b> {{$mozo}}<br>
			@endif	
		</div>
		<div>
			<table class="table table-sm alturaMedia">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-right mr-2">P. Unit</th>
						<th class="text-right mr-2">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{number_format($r->precio,2,',','.')}}</td>
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
		<!-- .....................duplicado................ -->
		<div class="pagebreak"> </div>
		<!-- .............................................. -->
		<div>			
			<b style="font-size:14px">Comprobante: </b>F - {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Duplicado<br>                       
			@if($cliente)
			<b style="font-size:14px">Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}} <br> 
			<b style="font-size:14px">Dir:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}} - {{$info[0]->localidad}}<br> 
			@endif
			<b style="font-size:14px">Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
			@if($repartidor)
			<b style="font-size:14px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rep:</b> {{$info[0]->repartidor_id}}<br>
			@endif	
			@if($mesa)
			<br><b style="font-size:14px">Mesa N°:</b> {{$mesa}}&nbsp;&nbsp;&nbsp;&nbsp;<b style="font-size:14px">Mozo N°:</b> {{$mozo}}<br>
			@endif	
		</div>
		<div>
			<table class="table table-sm alturaMedia">
				<thead style="font-size:14px">
					<tr>
						<th class="text-center">Cant</th>
						<th class="text-left">Descripción</th>
						<th class="text-right mr-2">P. Unit</th>
						<th class="text-right mr-2">Importe</th>
					</tr>
				</thead>
				<tbody style="font-size:12px">
					@foreach($infoDetalle as $r)
					<tr>
						<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right mr-2">{{number_format($r->precio,2,',','.')}}</td>
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
	.pagebreak {
		clear: both;
		page-break-after: always;
	}
</style>