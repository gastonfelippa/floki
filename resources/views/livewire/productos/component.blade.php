<div class="row layout-top-spacing justify-content-center"> 
    @if($action == 1)  
    <div class="col-sm-12 col-md-10 layout-spacing">             
        <div class="widget-content-area">
            <div class="widget-one">
                @include('common.alerts')   
                <div class="row">
                    <div class="col-xl-9 text-center">
                        <h3><b>Productos</b></h3>
                    </div>
                    <div class="col-3 text-right mb-1">   
                        @if ($verDisponibles)
                            <button type="button" class="btn btn-info" wire:click="VerDisponibles(false)">Ver Suspendidos</button>
                        @else
                            <button type="button" class="btn btn-info" wire:click="VerDisponibles(true)">Ver Disponibles</button>
                        @endif 
                    </div>
                </div> 
                @if($recuperar_registro == 1)
				<div class="row">
                    <div class="col-8">
                        <button type="button" style="height: 175px;"
                            wire:click="RecuperarRegistro('{{$id_soft_deleted}}',1)"   
                            class="btn btn-warning btn-block">
                            <i class="mbri-success"></i> El registro: <b>{{$descripcion_soft_deleted}}</b> fué eliminado anteriormente pero existe en el sistema, para recuperarlo haga click <i>aquí</i>
                        </button>
                        </div>
                        <div class="col-4">
                        <button style="height: 75px;" type="button" wire:click="volver(1)" class="btn btn-dark mr-1 btn-block">
                            Cancelar
                        </button>
                    </div>
                </div>
                @elseif($recuperar_registro == 2)
				<div class="row">
                    <div class="col-8">
                        <button type="button" style="height: 175px;"
                            wire:click="RecuperarRegistro('{{$id_soft_deleted}}',2)"   
                            class="btn btn-warning btn-block">
                            <i class="mbri-success"></i> El registro: <b>{{$descripcion_soft_deleted}}</b> fué eliminado anteriormente pero existe en el sistema, para recuperarlo haga click <i>aquí</i>
                        </button>
                        </div>
                        <div class="col-4">
                        <button style="height: 75px;" type="button" wire:click="volver(2)" class="btn btn-dark mr-1 btn-block">
                            Cancelar
                        </button>
                    </div>
                </div>
				@else 
                    @include('common.inputBuscarBtnNuevo', ['create' => 'Productos_create']) 
                    <div class="table-responsive scroll">
                        <table class="table table-hover table-checkable table-sm">
                            <thead>
                                <tr>                                                   
                                    <th class="">ID</th>
                                    <th class="">DESCRIPCIÓN</th>
                                    {{-- @can('Productos_create')
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
                                    <th class="text-center">STOCK</th> --}}
                                    @can('Productos_create')
                                    <th class="text-left">TIPO</th>
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
                                    {{-- @can('Productos_create')
                                    <td class="text-center">{{number_format($r->precio_costo,2,',','.')}}</td>
                                    @endcan
                                    <td class="text-center">{{number_format($r->precio_venta_l1,2,',','.')}}</td>                               
                                    <td class="text-center">{{number_format($r->precio_venta_l2,2,',','.')}}</td>                               
                                    <td class="text-center">{{$r->estado}}</td>
                                    <td class="text-center">{{number_format($r->stock_actual,3,',','.')}}</td> --}}
                                    @can('Productos_create')
                                    <td>{{$r->tipo}}</td>
                                    <td>{{$r->categoria}}</td>
                                    @endcan
                                    <td class="text-center">
                                        <ul class="table-controls">
                                            @can('Productos_edit')
                                                @if($r->tiene_receta == 'si')
                                                <li>
                                                    <a href="javascript:void(0);" 
                                                    wire:click="ver_receta({{$r->id}})" 
                                                    data-toggle="tooltip" data-placement="top" title="Ver Fórmula o Receta">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-card-list" viewBox="0 0 16 16"><path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/><path d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8zm0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zM4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/></svg>
                                                </li>
                                                @endif
                                            <li>
                                                <a href="javascript:void(0);" 
                                                wire:click="edit({{$r->id}})" 
                                                data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                            </li>
                                            @endcan
                                            @can('Productos_destroy')
                                            <li>
                                                <a href="javascript:void(0);"          		
                                                onclick="Confirm('{{$r->id}}')"
                                                data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                            </li>
                                            @endcan
                                            @if($r->tiene_receta == 'no')
                                                <li>
                                                    <a href="javascript:void(0);"   
                                                    onclick="openModalProveedor({{$r->id}})"
                                                    data-toggle="tooltip" data-placement="top" title="Ver Proveedor">
                                                    @if($r->proveedor == 1)
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-truck text-success" viewBox="0 0 16 16"><path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/></svg></a>
                                                    @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-truck text-danger" viewBox="0 0 16 16"><path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/></svg></a>
                                                    @endif
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);"   
                                                    onclick="openModalHistorial({{$r->id}})"
                                                    data-toggle="tooltip" data-placement="top" title="Historial de compras">
                                                    @if($r->historial == 1)
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-clock-history text-success" viewBox="0 0 16 16"><path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/><path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/><path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/></svg>                                                
                                                    @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-clock-history text-danger" viewBox="0 0 16 16"><path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/><path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/><path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/></svg>                                               
                                                    @endif
                                                </li>
                                            @endif    
                                        </ul>
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
    @include('livewire.productos.modal_productoProveedor')			
    @include('livewire.productos.modal_productoHistorial')			
    @elseif($action == 2)
        <input type="hidden" id="cliComanda" wire:model="modComandas">
        <input type="hidden" id="habilitar_modal" wire:model="habilitar_modal"> 
        <input type="hidden" id="modificar" wire:model="selected_id"> 
        @can('Productos_create')
            @include('livewire.productos.form')			
            @include('livewire.productos.modal')
            @include('livewire.productos.calculadora_de_merma')			
        @endcan		
    @endif
</div>

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
    function ConfirmProductoProveedor(id)
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
			window.livewire.emit('deleteProductoProveedor', id)    
			swal.close()   
		})
    }
    function validar(tipo)
    {
        var stock = new Number($('[id="stock_actual"]').val());
        var stock_ideal = new Number($('[id="stock_ideal"]').val());
        var stock_minimo = new Number($('[id="stock_minimo"]').val());
        if (stock != null && stock < 0 || stock_ideal != null && stock_ideal < 0 || stock_minimo != null && stock_minimo < 0){
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: '¡¡¡ATENCIÓN!!!',
                text: 'El Stock '+ tipo +' no puede ser negativo...',
                showConfirmButton: true
            })
            window.livewire.emit('validarStockNegativo', tipo);
        };
    }
    function precioBajo()
    {   
        var costo_actual = new Number($('[id="costo_actual"]').val());   //costo_en_bd
        var costo_nuevo = new Number($('[id="precio_costo"]').val());    //costo_nuevo
        if(costo_nuevo > 0){
            var selected = document.getElementById("selected_id");
            if(selected){         
                //solo cuando estoy modificando...
                if(costo_nuevo > 0){
                    if(costo_nuevo < costo_actual){
                        Swal.fire({
                            position: 'center',
                            icon: 'warning',
                            title: 'Atención!!',
                            text: 'El nuevo Precio de Costo del Producto que estás cargando ES MENOR que el Precio de Costo actual que tiene dicho Producto.',
                            showConfirmButton: true,
                            showCancelButton: true,
                            confirmButtonText: 'Continuar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) { 
                                opcionCambiarPrecios();
                            }else if (result.dismiss === Swal.DismissReason.cancel) {
                                $('[id="precio_costo"]').val($('[id="costo_actual"]').val());
                                Swal.fire('Cancelado', 'Tu registro está a salvo :)', 'error')
                            }
                        });  
                    }else if(costo_nuevo >= costo_actual) window.livewire.emit('calcular_precio_venta');
                } 
            }else window.livewire.emit('calcular_precio_venta');
        }
    }
    function opcionCambiarPrecios() 
    {          
        Swal.fire({
            icon: 'question',
            title: 'Elige una opción de Guardado...',
            text: 'Tener en cuenta que los cambios también afectarán a todos los Productos que contengan a éste como parte de sus recetas',
            showDenyButton: true,
            confirmButtonColor: '#3085d6',
            denyButtonColor: '#d33',
            confirmButtonText: 'Deseo que al Modificar el Precio de Costo solo se efectúen cambios en éste y en los Precios de Venta Sugeridos',
            denyButtonText: 'Deseo que al Modificar el Precio de Costo, se efectúen cambios en éste, en los Precios de Venta Sugeridos como así también en los Precios de Lista',
            showCancelButton: true,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) { 
                window.livewire.emit('opcionCambiarPrecios', 'solo_costos');
            } else if (result.isDenied) {
                window.livewire.emit('opcionCambiarPrecios', 'cambiar_todo');
            }
        });       
    }
    function validarProducto()
    {
        if($('#nombre').val() != ''){
            window.livewire.emit('validarProducto');
        }
    }  
    function guardar()
    {
        var solo_precios_listas = 0;
        var selected = document.getElementById("selected_id");
        
        if(selected){ //si estoy modificando un producto y no cambio el costo, habilito para modificar solo los precios de listas
            var costo_actual = $('[id="costo_actual"]').val();   //costo_en_bd
            var costo_nuevo = $('[id="precio_costo"]').val();    //costo_nuevo
            if(costo_actual == costo_nuevo) solo_precios_listas = 1;
        }  
       
        var salsa = 0, guarnicion = 0;
        if ($('#salsa_si').is(':checked')) salsa = 1;   
        if ($('#guarn_si').is(':checked')) guarnicion = 1;

        window.livewire.emit('guardar', salsa, guarnicion, solo_precios_listas);
    }
    function openModalProveedor(id)
    { 
        $('#proveedor').val('Elegir');
        window.livewire.emit('productoProveedor', id);
    }
    function openModalHistorial(id)
    { 
        window.livewire.emit('productoHistorialCompras', id);
    }
    function openModal()
    {     
        if($('#habilitar_modal').val() == 'true'){
            $('#texto').val($('#input_nombre').text());
            $('#modal').modal('show');  
        }  
	}
	function saveProductoProveedor()
    {
        var proveedor = $('#proveedor').val();
        window.livewire.emit('grabarProductoProveedor', proveedor);
        $('#proveedor').val('Elegir');
        $('#modal_productoProveedor').modal('hide'); 
    } 
	function save()
    {
        var texto = $('#texto').val();
        if(texto != '') window.livewire.emit('grabar_texto_base', texto);
        $('#modal').modal('hide'); 
    }
    function openModalCalculadora()
    {
        $('#modal_calculadora').modal('show');
	} 
    function openModalStockInicial()
    {
        $('#modal_stock_inicial').modal('show');
	} 
	function calcular_merma()
    {
        var peso_bruto = $('#peso_bruto').val();
        var peso_neto = $('#peso_neto').val();
        var rendimiento = ((peso_neto / peso_bruto) * 100).toFixed(0);
        var merma = (100 - rendimiento).toFixed(0);
        $('#merma').val(merma);
        $('#rendimiento').val(rendimiento);
    } 
    function foco(idElemento){
        document.getElementById(idElemento).focus();
    }
    function agregarProveedor()
    {
        window.location.href="{{ url('proveedores') }}";
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
    }, 120000 );
    function pingServer() {
        $.ajax('/keepAlive');
    }
    /////
    window.onload = function() {
        Livewire.on('eliminarRegistro',()=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Tu registro no se puede eliminar!',
                text: 'Existen Recetas relacionadas a este Producto...',
                showConfirmButton: false,
                timer: 3500
            })
		}) 
        Livewire.on('registroEliminado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Registro Eliminado!',
                text: 'Tu registro se eliminó correctamente...',
                showConfirmButton: false,
                timer: 1500
            })
            $('#modal_productoProveedor').modal('hide'); 
		}) 
        Livewire.on('registroRepetido',()=>{
            var producto = document.getElementById("nombre");
			toastr.error('El Producto ya existe!', 'Info')
			producto.focus();
			return false;
		})
        Livewire.on('texto_existe',()=>{
            var producto = document.getElementById("texto_sp");
			toastr.error('El Texto ya existe!', 'Info')
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
        Livewire.on('subproducto_creado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Subproducto creado!',
                showConfirmButton: false,
                timer: 1500
            })
            $('#modal').modal('hide');
		})
        Livewire.on('subproducto_modificado',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Subproducto actualizado!',
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
        Livewire.on('agregarProveedor',()=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: '¡¡¡ATENCIÓN!!!',
                text: 'Debes seleccionar algún proveedor...',
                showConfirmButton: true
            })
		})
        Livewire.on('cambiarPrecioDetalle',(cantidad)=>{
            var existe = 'Existen ';
            var factura = ' facturas abiertas y/o pendientes ';
            if(cantidad == 1){
                existe = 'Existe ';
                factura = ' factura abierta y/o pendiente ';
            } 
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: '¡¡¡ATENCIÓN!!!',
                text: existe + cantidad + factura + 'en donde tenés cargado este producto con el precio anterior..., ¿Qué acción deseas realizar?',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Actualizar los precios de todas las facturas...',
                showCancelButton: true,
                cancelButtonText: 'Continuar sin modificar las facturas...'
            }).then((result) => {
                if (result.isConfirmed) { 
                    window.livewire.emit('actualizarPreciosCargados');
                }
            });       
		})
        Livewire.on('abrirModalProveedor',()=>{
            $('#modal_productoProveedor').modal('show'); 
		})
        Livewire.on('abrirModalHistorial',()=>{
            $('#modal_productoHistorial').modal('show'); 
		})
        Livewire.on('search_focus',()=>{
            document.getElementById("search").focus();
		})
        Livewire.on('crear_receta',(id,descripcion)=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: '¡¡ATENCIÓN!!',
                text: '¿Deseas crear ahora la receta para ' + descripcion + '?',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Si',
                showDenyButton: true,
                denyButtonColor: '#E36C44',
                denyButtonText: 'Dejar para otro momento...',
            }).then((result) => {
                if (result.isConfirmed) { 
                    window.livewire.emit('ver_receta',id);
                    } else if (result.isDenied) {
                        Swal.close();
                        window.livewire.emit('doAction', 1);
                }
            });
		})
        Livewire.on('existencia_inicial',()=>{  
            Swal.fire('Actualizado','La Existencia Inicial se grabó correctamente...','success');
        }) 
        Livewire.on('stock_no_disponible_sin_opcion',(stock, producto)=>{
            var texto = 'Solo restan ';
            var unidades = ' unidades';
            if(stock == 0 || stock === null){
                texto = 'Restan ';
                stock = '0';
            }else if(stock == 1){
                texto = 'Solo resta ';
                unidades = ' unidad';  
            }
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Stock no disponible',
                html: texto + stock + unidades + ' de ' + producto + '.<br><br>' +
                'Para continuar con su carga deberá modificar su STOCK dirigiéndose a la pestaña <br> ABM ->> PRODUCTOS...',
                showConfirmButton: true
            })
        })
        Livewire.on('stock_no_disponible_con_opcion',(stock, producto, id)=>{
            var texto = 'Solo restan ';
            var unidades = ' unidades';
            if(stock == 0 || stock == null){
                texto = 'Restan ';
                stock = '0';
            }else if(stock == 1){
                texto = 'Solo resta ';
                unidades = ' unidad';  
            }else if(stock < 1){
                texto = 'Tienes stock negativo';
                stock = '';
                unidades = '';  
            } 
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Stock no disponible',
                text: texto + stock + unidades + ' de ' + producto,
                showDenyButton: true,
                confirmButtonText: `Permitir cargar sin stock`,
                denyButtonText: `Anular carga`,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('permitirCargaSinStock', 'si', id);
                } else if (result.isDenied) {
                    window.livewire.emit('permitirCargaSinStock', 'no', id);
                }
            })
        })
        Livewire.on('stock_receta_no_disponible_sin_opcion',(stock, id)=>{
            texto = '';
            for (var clave in stock) {
                texto = texto + 'Restan ' + stock[clave].stock + stock[clave].unidadDeMedida + ' de ' + stock[clave].descripcion + '<br>'
            };
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Stock de Materia Prima no disponible',
                html: texto +
                '<br> Para continuar con su carga deberá modificar su STOCK dirigiéndose a la pestaña <br> ABM ->> PRODUCTOS...',
                showConfirmButton: true
            })
        })
        Livewire.on('stock_receta_no_disponible_con_opcion',(stock, id)=>{
            texto = '';
            for (var clave in stock) {
                texto = texto + 'Restan ' + stock[clave].stock + stock[clave].unidadDeMedida + ' de ' + stock[clave].descripcion + '<br>'
            };
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Stock de Materia Prima no disponible',
                html: texto,
                showDenyButton: true,
                confirmButtonText: `Permitir cargar sin stock`,
                denyButtonText: `Anular carga`,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('permitirCargaSinStock', 'si', id);
                } else if (result.isDenied) {
                    window.livewire.emit('permitirCargaSinStock', 'no', id);
                }
            })
        })
        Livewire.on('receta_sin_detalle',(producto)=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Debe agregar un detalle a la receta de '+ producto + ' para poder descontar su stock.',
                text: 'O simplemente indicar que no tiene receta, o que no se controla stock para este producto desde la pestaña ABM ->> PRODUCTOS...',
                showConfirmButton: true
            })
		})
        Livewire.on('receta_sin_principal',(producto)=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Debe designar algún componente de la receta de '+ producto + ' como "principal" para poder descontar su stock.',
                text: 'O simplemente indicar que este producto no tiene receta, o que no se controla stock para el mismo desde la pestaña ABM ->> PRODUCTOS...',
                showConfirmButton: true
            })
		}) 
    }
</script>
