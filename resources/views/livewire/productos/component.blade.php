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
	@include('livewire.productos.modal')		
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
            swal.close()   
        })       
    }    
 
    function openModal()
    { 
        console.log(open)       
        $('#descripcion').val('')
        $('#margen').val('')
        $('#modalCategorias').modal('show')
	}
	function saveCategoria()
    {
        if($('#descripcion').val() == '') {
            toastr.error('El campo Descripción no puede estar vacío')
            return;
        }
        var data = JSON.stringify({
            'descripcion': $('#descripcion').val(),
            'margen': $('#margen').val()
        });

        $('#modalCategorias').modal('hide')
        window.livewire.emit('createCategoriaFromModal', data)
    } 
    
    window.onload = function() {
        document.getElementById("search").focus();
    }
</script>