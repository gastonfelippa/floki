@extends('layouts.pdf')

@section('content')
<div >
	<h3 class="text-center">Ventas delivery</h3>
	<table class="table table-hover table-checkable table-sm mb-4">
		<thead>
			<tr>
				<th class="text-center">Cliente</th>
				<th class="text-center">Importe</th>
			</tr>
		</thead>
		<tbody>
			@foreach($info as $r)
			<tr>
				<td class="text-left">{{$r->apeCli}}, {{$r->nomCli}}</td>
				<td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>                   
</div>
@endsection