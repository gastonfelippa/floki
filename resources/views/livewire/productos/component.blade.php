<div class="row layout-top-spacing justify-content-center"> 
    @include('common.alerts')
    @if($action == 1)  
    <div class="col-sm-12 col-md-8 layout-spacing">             
        <div class="widget-content-area">
            <div class="widget-one">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h3><b>Productos</b></h3>
                    </div> 
                </div> 
                @if($recuperar_registro == 1)
				@include('common.recuperarRegistro')
				@else   		
				    @include('common.inputBuscarBtnNuevo', ['create' => 'Productos_create']) 
                    <div class="table-responsive scroll">
                        <table class="table table-hover table-checkable table-sm">
                            <thead>
                                <tr>                                                   
                                    <th class="">ID</th>
                                    <th class="">DESCRIPCIÓN</th>
                                    @can('Productos_create')
                                    <th class="text-right">P/COSTO</th>
                                    @endcan
                                    <th class="text-right">P/VENTA</th>
                                    <th class="text-center">ESTADO</th>
                                    <th class="text-center">STOCK</th>
                                    @can('Productos_create')
                                    <th class="text-left">CATEGORIA</th>
                                    <th class="text-center">ACCIONES</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($info as $r)
                                <tr>                     
                                    <td class="text-center"><p class="mb-0">{{$r->codigo}}</p></td>
                                    <td>{{$r->descripcion}}</td>
                                    @can('Productos_create')
                                    <td class="text-right">{{$r->precio_costo}}</td>
                                    @endcan
                                    <td class="text-right">{{$r->precio_venta}}</td>                               
                                    <td class="text-center">{{$r->estado}}</td>
                                    <td class="text-center">{{$r->stock}}</td>
                                    @can('Productos_create')
                                    <td>{{$r->categoria}}</td>
                                    @endcan
                                    <td class="text-center">
                                        @include('common.actions', ['edit' => 'Productos_edit', 'destroy' => 'Productos_destroy'])
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div> 
    </div>
    @else
	@can('Productos_create')
	@include('livewire.productos.form')			
	@endif
	@endcan
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
        window.livewire.emit('validarProducto');
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
    }
</script>