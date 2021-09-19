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
                                    <th class="text-center">P/COSTO</th>
                                    @endcan
                                    @if($modDelivery == "1")
                                    <th class="text-center">P/VENTA <br>LISTA SALÓN</th>
                                    <th class="text-center">P/VENTA <br>LISTA DELIVERY</th>
                                    @else
                                    <th class="text-center">P/VENTA <br>LISTA 1</th>
                                    <th class="text-center">P/VENTA <br>LISTA 2</th>
                                    @endif
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
                                    <td class="text-center">{{$r->codigo}}</td>
                                    <td>{{$r->descripcion}}</td>
                                    @can('Productos_create')
                                    <td class="text-center">{{number_format($r->precio_costo,2,',','.')}}</td>
                                    @endcan
                                    <td class="text-center">{{number_format($r->precio_venta_l1,2,',','.')}}</td>                               
                                    <td class="text-center">{{number_format($r->precio_venta_l2,2,',','.')}}</td>                               
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
        <input type="hidden" id="cliComanda" wire:model="modComandas">
        <input type="hidden" id="habilitar_model" wire:model="habilitar_model"> 
        @can('Productos_create')
            @include('livewire.productos.form')			
            @include('livewire.productos.modal')			
        @endcan
	@endif
</div>

<style type="text/css" scoped>
.scroll{
    position: relative;
    height: 270px;
    margin-top: .5rem;
    overflow: auto;
}
</style>

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
    function calcularPrecioVenta() {
        window.livewire.emit('calcular_precio_venta');
    }

    function validarProducto()
    {
        if($('#nombre').val() != '') window.livewire.emit('validarProducto');
    }  
    function guardar()
    {
        var salsa = false, guarn = false;
        if(document.getElementById('cliComanda').value == 1 && document.getElementById('salsa_si').checked) salsa = true;
        if(document.getElementById('cliComanda').value == 1 && document.getElementById('guarn_si').checked) guarn = true;
        window.livewire.emit('guardar',salsa,guarn);
    }
    function openModal()
    {     
        if($('#habilitar_model').val() == 'true'){
            $('#texto').val($('#nombre').val());
            $('#modal').modal('show');  
        }  
	}
	function save()
    {
        var texto = $('#texto').val();
        if(texto != '') window.livewire.emit('grabar_texto_base', texto);
        $('#modal').modal('hide'); 
    } 
    /////código para prolongar la session
    var keep_alive = false;
    $(document).bind("click keydown keyup mousemove", function() {
        keep_alive = true;
    });
    setInterval(function() {
        if ( keep_alive ) {
            pingServer();
            keep_alive = false;
        }
    }, 1200000 );
    function pingServer() {
        $.ajax('/keepAlive');
    }
    /////
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
        Livewire.on('registro_no_grabado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: '¡¡¡ATENCIÓN!!!',
                text: 'El registro no se grabó...',
                showConfirmButton: false,
                timer: 1500
            })
            $('#modal').modal('hide');
		})
    }
</script>
