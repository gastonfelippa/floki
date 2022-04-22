@extends('layouts.pdf')

@section('content')
<div>
	<?php $rows =0;?>
	@foreach($debitos as $r) 
		<?php $rows ++;?>
		
		<div class="row" style="height:10%;margin: 3px;">
			<div class="col-4" style="border-width: 1px;border-style: solid;">
				<span><img class="mt-1" src="images/logo_lalcec.png" height="20" alt="image">&nbsp;&nbsp;&nbsp;&nbsp;LALCEC - Freyre</span> 
				<br>
				<span><b>{{$r->nomApe}}</b></span>
				<br>
				<span>Cuota Societaria&nbsp;&nbsp;<b>{{$r->mes_año}}</b></span>
				<br>
				<span> Cupón N°{{$r->numero}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><b>$ {{number_format($r->importe,2,',','.')}}</b></span>	
			</div>
			<div class="col-4 offset-5" style="border-width: 1px;border-style: solid;">
				<span><img class="mt-1" src="images/logo_lalcec.png" height="20" alt="image">&nbsp;&nbsp;&nbsp;&nbsp;LALCEC - Freyre</span> 
				<br>
				<span><b>{{$r->nomApe}}</b></span>
				<br>
				<span>Cuota Societaria&nbsp;&nbsp;<b>{{$r->mes_año}}</b></span>
				<br>
				<span> Cupón N°{{$r->numero}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><b>$ {{number_format($r->importe,2,',','.')}}</b></span>
			</div>
		</div>

		<?php if($rows == 9): ?>
			<div class="pagebreak"></div>
			<?php $rows = 0; ?>
		<?php endif; ?>
	@endforeach
</div>
@endsection

<style type="text/css" scoped>
	.pagebreak {
		clear: both;
		page-break-after: always;
	}
	b {
    	font-weight: bold;
	}
</style>