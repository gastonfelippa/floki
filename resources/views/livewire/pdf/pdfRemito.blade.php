@extends('layouts.pdf')

@section('content') 
<div>
	<div class="col-12">
		<div>	
			<p><b>Remito N°:</b>  {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Original  
				<span class="tab"></span><b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}}</p>                       
			<p><b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
				<span class="tab2"></span><b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}} - {{$info[0]->descripcion}}</p> 
		
		</div>
		
		<table class="alturaMedia" style="width:100%">
			<thead style="font-size:14px">
				<tr>
					<th class="text-center" style="width:100px;">Código</th>
					<th class="text-center" style="width:100px;">Cantidad</th>
					<th class="text-left">Descripción</th>
				</tr>
			</thead>
			<tbody style="font-size:12px">
				@foreach($infoDetalle as $r)
				<tr>
					<td class="text-center" style="width:100px;">{{$r->codigo}}</td>
					<td class="text-center" style="width:100px;">{{number_format($r->cantidad,0)}}</td>
					<td class="text-left">{{$r->producto}}</td>
				</tr>
				@endforeach
			</tbody>
		</table> 		                  
		<br/><br/>
		<div class="row">
			<div class="col-6">
				<hr style="border:1px; width:150px"/>
				<p class="ml-5 pl-5"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;P/empresa</p>
			</div>
			<div class="col-6 offset-6">
				<hr style="border:1px; width:150px" />
				<p class="ml-5 pl-5">Recibí conforme</p>
			</div>
		</div>
	
		<!-- <hr style="border:1px dotted; width:600px" /> -->
		<!-- .....................duplicado................ -->
		<div>			
			<p><b>Remito N°:</b>  {{str_pad($info[0]->numero, 6, '0', STR_PAD_LEFT)}} - Duplicado  
				<span class="tab"></span><b>Cliente:</b>  {{$info[0]->apeCli}} {{$info[0]->nomCli}}</p>                       
			<p><b>Fecha:</b>  {{\Carbon\Carbon::parse($info[0]->created_at)->format('d-m-Y')}}			
				<span class="tab3"></span><b>Dirección:</b>  {{$info[0]->calleCli}} {{$info[0]->numCli}} - {{$info[0]->descripcion}}</p> 
			
		</div>
		<table class="alturaMedia" style="width:100%">
			<thead style="font-size:14px">
				<tr>
					<th class="text-center" style="width:100px;">Código</th>
					<th class="text-center" style="width:100px;">Cantidad</th>
					<th class="text-left">Descripción</th>
				</tr>
			</thead>
			<tbody style="font-size:12px">
				@foreach($infoDetalle as $r)
				<tr>
					<td class="text-center" style="width:100px;">{{$r->codigo}}</td>
					<td class="text-center" style="width:100px;">{{number_format($r->cantidad,0)}}</td>
					<td class="text-left">{{$r->producto}}</td>
				</tr>
				@endforeach
			</tbody>
		</table> 		                  
		<br/><br/>
		<div class="row">
			<div class="col-6">
				<hr style="border:1px; width:150px"/>
				<p class="ml-5 pl-5"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;P/empresa</p>
			</div>
			<div class="col-6 offset-6">
				<hr style="border:1px; width:150px" />
				<p class="ml-5 pl-5">Recibí conforme</p>
			</div>
		</div>
	</div>
</div>
@endsection

<style type="text/css" scoped>
	.alturaMedia{
			position: relative;
			height: 300px;
			margin-top: .5rem;
			overflow: auto;
    }
	.alturaFull{
			position: relative;
			height: 700px;
			margin-top: .5rem;
			overflow: auto;
    }
	.tab {
            display: inline-block;
            margin-left: 50px;
    }
	.tab2 {
            display: inline-block;
            margin-left: 122px;
    }
	.tab3 {
            display: inline-block;
            margin-left: 138px;
    }
	.tab4 {
            display: inline-block;
            margin-left: 100px;
    }
	.tab5 {
            display: inline-block;
            margin-left: 100px;
    }
	p {line-height: 50%;}
	hr {line-height: 50%;}
</style>

            