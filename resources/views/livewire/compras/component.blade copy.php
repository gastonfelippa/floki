<div class="row layout-top-spacing">	
    <div class="col-md-12 col-lg-6 layout-spacing"> 
		<div class="widget-content-area br-4">
			<div class="widget-one">
                <div class="row">
                    <div class="form-group col-12 col-sm-7">
                        <h3>Factura</h3>
                        <div class="row">
                            <div class="col-2 col-sm-3">
                                <input wire:model.lazy="letra" type="text" class="form-control form-control-sm text-center">
                            </div>
                            <div class="col-3 col-sm-4">
                                <input wire:model.lazy="sucursal" type="text" class="form-control form-control-sm text-center">
                            </div>
                            <div class= "col-4 col-sm-5">
                                <input wire:model.lazy="numFact" type="text" class="form-control form-control-sm text-center">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-5 text-center">
                        <h3 class="bg-danger">Total : $ {{number_format($total,2)}}</h3> 
                    </div>
                </div>  
                <div class="row">
                    <div class="col-md-4">
                        <h6>Fecha {{\Carbon\Carbon::now()->format('d-m-Y')}} </h6>
                    </div>                    
                    <div class="col-md-8 text-right"> 
                        @if($total == 0)                       
                            <button type="button" wire:click="pagar_factura()" onclick="setfocus('barcode')" 
                                class="btn btn-primary" disabled>
                                Cobrar   
                            </button>
                            <button type="button" class="btn btn-success" disabled>                                
                                Imprimir
                            </button>
                        @else
                            <button type="button" wire:click="pagar_factura()" onclick="setfocus('barcode')" 
                                class="btn btn-primary" enabled>
                                Pagar   
                            </button>
                            <button type="button" class="btn btn-success" enabled>
                                <a href="{{url('pdfFactDel',array($compra_id))}}" target="_blank">
                                Cuenta Corriente</a>
                            </button>
                        @endif
                    </div>
                </div>
            @if($grabar_encabezado)
                <form> 
                    <div class="row mt-1">
                        <div class="form-group col-md-5 col-sm-12 ">                       
                            <label>
                                Proveedor
                                @if($habilitar_botones)
                                    <span class="badge badge-primary ml-4" 
                                    wire:click.prevent="grabarModEncabezado({{$compra_id}})">Grabar</span>
                                    <span class="badge badge-dark" 
                                    wire:click.prevent="cancelarModEncabezado()">Cancelar</span>
                                @else                               
                                    <span class="badge badge-primary ml-4" >
                                    <a href="{{ url('proveedores') }}" style="color: white">Agregar</a></span>                              
                                @endif
                            </label>   
                            <select wire:model="proveedor" class="form-control form-control-sm text-center">
                                <option value="Elegir">Elegir</option>
                                @foreach($proveedores as $p)
                                    <option value="{{ $p->id }}">
                                    {{$p->nombre_empresa}}                         
                                    </option> 
                                @endforeach                               
                            </select>                      		               
                        </div> 
                    </div>
                </form>
            @else
                <div class="row mt-2">
                    <div class="col-sm-7">
                        <h6>Proveedor:  {{$encabezado[0]->nombre_empresa}}</h6>
                        <h6>Dirección:  {{$encabezado[0]->calle}} {{$encabezado[0]->numero}} 
                                          - {{$encabezado[0]->descripcion}}</h6>
                    </div>
                    <div class="col-sm-5 mt-4">
                        <span class="badge badge-primary ml-4"
                            wire:click.prevent="modificarEncabezado()">Modificar Proveedor</span>
                    </div>
                </div>                
            @endif
			@include('common.alerts')
				<div class="table-responsive scroll">
					<table class="table table-hover table-checkable table-sm mb-4">
						<thead>
							<tr>
								<th class="text-center">CANTIDAD</th>
								<th class="text-center">DESCRIPCIÓN</th>
								<th class="text-center">PRECIO UNITARIO</th>
								<th class="text-center">IMPORTE</th>
								<th class="text-center">ACCIONES</th>
							</tr>
						</thead>
						<tbody>
							@foreach($info as $r)
							<tr class="table-danger">
								<td class="text-center">{{number_format($r->cantidad,2)}}</td>
								<td class="text-left">{{$r->producto}}</td>
								<td class="text-right">{{$r->precio}}</td>
								<td class="text-right">{{number_format($r->importe,2)}}</td>
								<td class="text-center">
									@include('common.actions', ['edit' => 'Compras_edit_item', 'destroy' => 'Compras_destroy_item'])
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>                   
				</div>
			</div>			
		</div>
	</div>
    <div class="col-md-12 col-lg-6 layout-spacing">
        <div class="widget-content-area">
            <div class="widget-one">
                <h5>
                    <b>@if($selected_id ==0) Agregar Item  @else Editar Item @endif  </b>
                </h5>
                <form>
                    @include('common.messages')    
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-2">
                            <label>Cantidad</label>
                            <input id="cantidad" wire:model.lazy="cantidad" onclick.keydown.enter="setfocus('barcode')" type="text" 
                                class="form-control form-control-sm text-center">
                        </div> 
                        <div class="form-group col-sm-12 col-md-3">
                            <label >Código</label>
                            <input id="barcode" wire:model.lazy="barcode"  type="text" 
                                onclick.keydown.enter="setfocus('guardar')" class="form-control form-control-sm">
                        </div>
                        <div class="form-group col-sm-12 col-md-4">
                            <label>Producto</label>
                            @can('Compras_create_producto')
                            <span class="badge badge-primary ml-4" >
                                    <a href="{{ url('productos') }}" style="color: white">Agregar</a></span>                            
                            @endcan
                            <select id="producto" wire:model="producto" class="form-control form-control-sm text-center">
                                <option value="Elegir" >Elegir</option>
                                @foreach($productos as $t)
                                <option value="{{ $t->id }}" wire:click.prevent="buscarProducto({{$t->codigo}})"
                                    wire:tab="buscarProducto({{$t->id}})">
                                    {{$t->descripcion}}                         
                                </option> 
                                @endforeach                               
                            </select>			               
                        </div>            
                        <div class="form-group col-sm-12 col-md-3">
                            <label>Precio Unitario</label>
                            <input wire:model.lazy="precio" type="text" class="form-control form-control-sm" disabled>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-12 mt-1 text-left">
                            <button type="button" wire:click="resetInput()" onclick="setfocus('barcode')" class="btn btn-dark mr-1">
                                <i class="mbri-left"></i> Cancelar
                            </button>
                            <button id="guardar" type="button" wire:click="StoreOrUpdate()" onclick="setfocus('barcode')" class="btn btn-primary ml-2">
                                <i class="mbri-success"></i> Guardar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="widget-content-area mt-3">
            <div class="widget-one"> 
                <div class="row m-1 rounded">
                    <img src="images/helados.jpg" class="img-fluid" 
                        alt="Responsive image" style="height:150px;width:585px">
                </div>
            </div>
        </div>         
    </div>   
</div>

<style type="text/css" scoped>
.scroll{
    position: relative;
    height: 200px;
    margin-top: .5rem;
    overflow: auto;
}
</style>

<script type="text/javascript">
 	function Confirm(id)
    {
       let me = this
       swal({
        title: 'CONFIRMAR',
        text: '¿DESEAS ELIMINAR EL REGISTRO?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        closeOnConfirm: false
    },
		function() {
			window.livewire.emit('deleteRow', id)    
			toastr.success('info', 'Registro eliminado con éxito')
			swal.close()   
        })
    }

    function buscarProducto(id)
    { 
        window.livewire.emit(buscarProducto(id))
    }

    window.onload = function() {
        document.getElementById("barcode").focus();
        document.getElementById("cantidad").value="1";
    }

    function setfocus($id) {
        document.getElementById($id).focus();
    }

</script>