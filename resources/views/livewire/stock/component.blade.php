<div class="row layout-top-spacing justify-content-center"> 
    @include('common.alerts')
    <div class="col-sm-12 col-md-8 layout-spacing">             
        <div class="widget-content-area">
            <div class="widget-one">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h3><b>Stock</b></h3>
                    </div> 
                </div>  		
                <div class="row justify-content-between mb-3">
                    <div class="col-6 mb-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                            </div>
                            <input id="search" type="text" wire:model="search" class="form-control form-control-sm" placeholder="{{$placeHolderSearch}}" aria-label="notification" aria-describedby="basic-addon1" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-3">
                        @if($action == 1)
                        <button id="btnNuevo" type="button" wire:click="doAction(2)" class="btn btn-danger btn-block">
                            <span style="text-decoration: underline;">V</span>er Stock Por Consignatario</button> 
                        @else
                        <button id="btnNuevo" type="button" wire:click="doAction(1)" class="btn btn-danger btn-block">
                            <span style="text-decoration: underline;">V</span>er Stock Local</button>
                        @endif
                    </div>
                    <div class="col-3">
                        @if($valorizar)
                        <button type="button" wire:click="valorizar()" class="btn btn-danger btn-block">
                            <span style="text-decoration: underline;">N</span>o valorizar</button>
                            @else
                            <button type="button" wire:click="valorizar()" class="btn btn-danger btn-block">
                            V<span style="text-decoration: underline;">a</span>lorizar</button> 
                        @endif   
                    </div>
                </div>
                @if($action == 1)
                    <div class="table-responsive scroll">
                        <table class="table table-hover table-checkable table-sm">
                            <thead>
                                <tr>                                                   
                                    <th class="text-center">CÓDIGO</th>
                                    <th class="">DESCRIPCIÓN</th>
                                    <th class="text-center">LOCAL</th>
                                    <th class="text-center">EN CONSIGNACIÓN</th>
                                    <th class="text-center">TOTAL</th>
                                    @can('Productos_create')
                                    <th class="text-center">ACCIONES</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($info as $r)
                                <tr>                     
                                    <td class="text-center">{{$r->codigo}}</td>
                                    <td>{{$r->descripcion}}</td>
                                    <td class="text-center">{{$r->stock}}</td>
                                    <td class="text-center">
                                        <ul class="table-controls">
                                            <li>
                                                {{$r->stock_en_consignacion}} 
                                            </li>
                                            @if($r->stock_en_consignacion != null)    
                                            <li>
                                                <a href="javascript:void(0);"
                                                    wire:click="verStockEnConsignacion({{$r->id}})"  
                                                    data-toggle="tooltip" data-placement="top" title="Ver stock por Consignatario">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>                                 
                                            </li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td class="text-center">{{$r->stock_total}}</td>
                                    @can('Productos_create')
                                    <td class="text-center">
                                        @include('common.actions', ['edit' => 'Productos_edit', 'destroy' => 'Productos_destroy'])
                                    </td>
                                    @endcan
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @include('livewire.stock.modal')
                    @include('livewire.stock.modalHistorial')
                    @else
                    <div class="table-responsive scroll">
                        <table class="table table-hover table-checkable table-sm">
                            <thead>
                                <tr>                               
                                    <th class="text-center">CODIGO</th>
                                    <th class="text-center">CANTIDAD</th>
                                    <th class="text-left">DESCRIPCION</th>
                                    @if($valorizar)
                                    <th class="text-right">PRECIO UNITARIO</th>
                                    <th class="text-right">IMPORTE</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($info as $r)
                                <tr>                     
                                    <td class="text-center">{{$r->codigo}}</td>
                                    <td class="text-center">{{$r->cantidad}}</td>
                                    <td class="text-left">{{$r->descripcion}}</td>
                                    @if($valorizar)
                                    <td class="text-right">{{$r->precio_venta_l2}}</td>
                                    <td class="text-right">{{$r->precio_venta_l2}}</td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <!-- <tfoot>
                                <tr>            colspan ocupa una cant determinada de columnas
                                    <th class="text-right" colspan="8">TOTAL STOCK:</th>
                                    <th class="text-center" colspan="2">$ {{number_format($r->precio_venta_l2,2)}}</th>
                                </tr>
                            </tfoot> -->
                        </table>
                    </div>
                    <h5><b>Total Stock:</b> $ {{number_format($totalStock,2)}}</h5>

                    @include('livewire.stock.modal')
                    @endif
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
</style>

@section('content_script_head')   
<script>
    function calcularPrecioVenta() {
        window.livewire.emit('calcular_precio_venta');
    }
</script>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script type="text/javascript">
    function Confirm(id)
    {
        Swal.fire({
    		title: 'CONFIRMAR',
    		text: 'Antes de Eliminar el registro, agrega un pequeño comentario del motivo que te lleva a realizar esta acción',
    		icon: 'warning',
			input: 'text',
    		showCancelButton: true,
    		confirmButtonText: 'Aceptar',
    		cancelButtonText: 'Cancelar',
    		closeOnConfirm: false,
			inputValidator: comentario => {
				if (!comentario) return "Por favor escribe un breve comentario";
				else return undefined;
			}
		}).then((result) => {
			if (result.isConfirmed) {
				if (result.value) {
					let comentario = result.value;
					window.livewire.emit('deleteRow', id, comentario)
				}
			}else if (result.dismiss === Swal.DismissReason.cancel) {
				Swal.fire('Cancelado', 'Tu registro está a salvo :)', 'error')
            }
		})
    }
    function validarProducto()
    {
        if($('#nombre').val() != '') window.livewire.emit('validarProducto');
    }
    function recargarPagina()
    {
        window.location.href="{{ url('stock') }}";
    }  
    window.onload = function() {
        document.getElementById("search").focus();
        Livewire.on('registroEliminado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Registro Eliminado!',
                text: 'Tu registro se eliminó correctamente...',
                showConfirmButton: false,
                timer: 1500
            })
		}) 
        Livewire.on('registroRepetido',()=>{
            var producto = document.getElementById("nombre");
			toastr.error('El Producto ya existe!', 'Info')
			producto.focus();
			return false;
		})
        Livewire.on('texto_creado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Texto creado!',
                showConfirmButton: false,
                timer: 1500
            })
            $('#modal').modal('hide');
		})
        Livewire.on('abrirModal',()=>{
            $('#modalStock').modal('show');
		})
        Livewire.on('abrirModalHistorial',()=>{
            $('#modalStock').modal('hide');
            $('#modalHistorialStock').modal('show');
		})
    }
</script>
