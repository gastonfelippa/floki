<div class="row layout-top-spacing justify-content-center">  
    <div class="col-sm-12 col-md-10 layout-spacing">      
    	<div class="widget-content-area">
    		<div class="widget-one">
    			<div class="row">
    				<div class="col-xl-12 text-center">
    					<h3><b>Auditoría de Items Eliminados/Recuperados</b></h3>
    				</div> 
    			</div>   		
				@include('common.inputBuscarBtnNuevo', ['create' => ''])
				<div class="table-responsive scroll">
					<table class="table table-hover table-checkable table-sm">
						<thead>
							<tr>
								<th class="text-center">FECHA/HORA</th>
								<th class="text-center">ITEM</th>
								<th class="text-center">TABLA</th>
								<th class="text-center">ACCION</th>
								<th class="text-center">USUARIO</th>
								<th class="text-center">COMENTARIO</th>
							</tr>
						</thead>
						<tbody>
							@foreach($info as $r)
							<tr>
								<td class="text-left">{{$r->created_at->format('d-m-Y')}} / {{$r->created_at->format('H:i')}}</td>
								<td class="text-left">{{$r->item}}</td>
								<td class="text-left">{{$r->tabla}}</td>
								@if($r->estado == 0)
								<td class="text-center" title="Eliminado">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-thumbs-down text-danger"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"></path></svg>
								</td>
								@else
								<td class="text-center" title="Recuperado">
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-thumbs-up text-success"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>									
								</td>
								@endif
								<td class="text-left">{{$r->apeUser}} {{$r->nomUser}}</td>
								<td class="text-left">{{$r->comentario}}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
    	</div> 
    </div>
</div>

<style type="text/css" scoped>
	.scroll{
		position: relative;
		height: 270px;
		margin-top: .5rem;
		overflow: auto;
	}
	thead tr th {     /* fija la cabecera de la tabla */
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #ffffff;
    }
</style>
