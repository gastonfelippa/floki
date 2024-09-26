<div class="row col-sm-12 col-md-6 layout-spacing ml-0">	
	<div class="widget-content-area br-4 col-12">
		<div class="widget-one">
            <h5><b>Detalle Factura : {{$nombreCliente}}</b></h5>
            <div class="table-responsive scrol">
				<table class="table table-hover table-checkable table-sm mb-4">
					<thead>
						<tr>
							<th class="text-center">CANTIDAD</th>
							<th class="text-center">DESCRIPCIÓN</th>
							<th class="text-center">P/UNITARIO</th>
							<th class="text-center">IMPORTE</th>
							<th class="text-center">ACCIONES</th>
						</tr>
					</thead>
					<tbody>
						@foreach($infoDetalle as $r)
						<tr class="table-danger">
							<td class="text-center">{{number_format($r->cantidad,2,',','.')}}</td>
							<td class="text-left">{{$r->producto}}</td>
							<td class="text-right">{{number_format($r->precio,2,',','.')}}</td>
							<td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
							<td class="text-center">
								@include('common.actionsdelivery')
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>                   
			</div>
			<h5 class="mt-2">
                <b>@if($selected_id ==0) Agregar Producto  @else Editar Producto @endif  </b>
            </h5>
            <form >
            @include('common.messages')    
                <div class="row">
                    <div class="form-group col-sm-12 col-md-2">
                        <label >Cantidad</label>
                        <input id="cantidad" wire:model.lazy="cantidadEdit" type="text" 
                            class="form-control form-control-sm">
                    </div>
                    <div class="form-group col-sm-12 col-md-6">
                        <label>Productos</label>
						<select wire:model="productoEdit" class="form-control form-control-sm text-center">
							<option value="Elegir" >Elegir</option>
							@foreach($productos as $t)
							<option value="{{$t->id}}">
								{{$t->descripcion}}                         
							</option> 
							@endforeach                               
						</select>			               
					</div>            
					<div class="form-group col-sm-12 col-md-4">
						<label >Precio Unitario</label>
						<input wire:model="precioEdit" type="text" class="form-control form-control-sm" disabled>
					</div>
				</div>
                <div class="row">
                    <div class="col-12">
                        <button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">
                            <i class="mbri-left"></i> Cancelar
                        </button>
                        <button type="button" wire:click="StoreOrUpdate()" onclick="setfocus('cantidad)" class="btn btn-primary ml-2">
                            <i class="mbri-success"></i> Guardar
                        </button>
                    </div>
                </div>
            </form>                 
        </div>
    </div>
</div>

<style type="text/css" scoped>
.scrol{
    position: relative;
    height: 170px;
    margin-top: .5rem;
    overflow: auto;
}
</style>