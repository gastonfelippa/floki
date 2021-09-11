<div class="row layout-top-spacing justify-content-center"> 
    @include('common.alerts')
    <div class="col-sm-12 col-md-6 layout-spacing">             
        <div class="widget-content-area">
            <div class="widget-one">
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h3><b>Lista De Precios</b></h3>
                    </div> 
                </div>  		
                <div class="row justify-content-between mb-3">
                    <div class="col-7 mb-1">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                            </div>
                            <input id="search" type="text" wire:model="search" class="form-control form-control-sm" placeholder="" aria-label="notification" aria-describedby="basic-addon1" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-5 text-right mt-1">
                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                            @if($lista == 1)
                                <button id="btn1" type="button" class="btn btn-danger"
                                    wire:click="verLista(1)">L1</button>                    
                                <button id="btn2" type="button" class="btn btn-outline-danger"
                                    wire:click="verLista(2)">L2</button>
                                <button id="btn3" type="button" class="btn btn-outline-danger">
                                    <a href="{{url('pdfListaDePrecios',array($lista))}}" target="_blank">
                                    Imprimir</a>
                                </button>
                            @elseif($lista == 2)
                                <button id="btn1" type="button" class="btn btn-outline-danger"
                                    wire:click="verLista(1)">L1</button>                    
                                <button id="btn2" type="button" class="btn btn-danger"
                                    wire:click="verLista(2)">L2</button>
                                <button id="btn3" type="button" class="btn btn-outline-danger">
                                    <a href="{{url('pdfListaDePrecios',array($lista))}}" target="_blank">
                                    Imprimir</a>
                                </button>
                            @else
                                <button id="btn1" type="button" class="btn btn-outline-danger"
                                    wire:click="verLista(1)">L1</button>                    
                                <button id="btn2" type="button" class="btn btn-outline-danger"
                                    wire:click="verLista(2)">L2</button>
                                <button id="btn3" type="button" class="btn btn-danger">
                                    <a href="{{url('pdfListaDePrecios',array($lista))}}" target="_blank">
                                    Imprimir</a>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
              
                    <div class="table-responsive scroll">
                        <table class="table table-hover table-checkable table-sm">
                            <thead>
                                <tr>                               
                                    <th class="text-center">CODIGO</th>
                                    <th class="text-left">DESCRIPCION</th>
                                    <th class="text-right mr-3">PRECIO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($info as $r)
                                <tr>                     
                                    <td class="text-center">{{$r->codigo}}</td>
                                    <td class="text-left">{{$r->descripcion}}</td>
                                    <td class="text-right mr-3">{{$r->precio}}</td>
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
