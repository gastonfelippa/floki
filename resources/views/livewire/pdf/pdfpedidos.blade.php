@extends('layouts.pdf')

@section('content')
<div class="centrar">
	<h3 class="text-center">Pedidos</h3>
		<div class="col">
			<p><b>{{$empresa}}&nbsp;&nbsp;&nbsp; Total: $ {{number_format($total,2,',','.')}}</b></p>
		</div>
		<div class="table-responsive">
			<table class="table table-sm">
				<thead>
					<tr>
						<th class="text-center">Cantidad</th>
						<th class="text-left">Producto</th>
						<th class="text-right">Pr. Unitario</th>
					</tr>
				</thead>
				<tbody>
				@foreach($infoDetPedido as $r)
					<tr>
						<td class="text-center">{{$r->cantidad}}</td>
						<td class="text-left">{{$r->producto}}</td>
						<td class="text-right">{{number_format($r->precio_costo,2,',','.')}}</td>
					</tr>
					
					@endforeach
				</tbody>
			</table>                   
		</div>
	
</div>
<style type="text/css" scoped>
	@media print (max-width: 640px) {
		.centrar {
				float: left;
			background:blue
		}
	}
	@media print (min-width: 640px) and (max-width: 1280px) {
		.centrar {
		margin: 0 auto;
		}
	}
</style>
@endsection