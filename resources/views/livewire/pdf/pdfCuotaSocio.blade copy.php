@extends('layouts.pdf')

@section('content')
<div>
	@foreach($debitos as $r) 
	<div class="row" style="height:10%">
		<div class="col-3" style="border-width: 1px;border-style: solid;">
			<font><img src="images/logo_lalcec.png" height="20" alt="image">&nbsp;&nbsp;&nbsp;&nbsp;LALCEC - Freyre</font> 
			<br>
			<font size=2 face="Comic Sans MS,arial">{{$r->apellido}} {{$r->nombre}}</font>
			<br>
			<font size=1 face="Comic Sans MS,arial">Cuota Societaria {{$r->mes_año}}</font>
			<br>
			<font size=2 face="Comic Sans MS,arial">{{$r->mes_año}}&nbsp;&nbsp;&nbsp;$ {{number_format($r->importe,2,',','.')}}</font>	
		</div>
		<div class="col-3 offset-4" style="border-width: 1px;border-style: solid;">
			<font><img src="images/logo_lalcec.png" height="20" alt="image">&nbsp;&nbsp;&nbsp;&nbsp;LALCEC - Freyre</font> 
			<br>
			<font size=2 face="Comic Sans MS,arial">{{$r->apellido}} {{$r->nombre}}</font>
			<br>
			<font size=1 face="Comic Sans MS,arial">Cuota Societaria {{$r->mes_año}}</font>
			<br>
			<font size=2 face="Comic Sans MS,arial">{{$r->mes_año}}&nbsp;&nbsp;&nbsp;$ {{number_format($r->importe,2,',','.')}}</font>	
		</div>
	</div>
	@endforeach
</div>
@endsection
