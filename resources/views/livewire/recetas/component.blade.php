<div class="row layout-top-spacing justify-content-center"> 
    @if($action == 1)
	@include('common.alerts')
    <div class="col-sm-12 col-md-8 layout-spacing">      
        <div class="widget-content-area">
            <div class="widget-one">
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        <h3><b>Receta de {{$prod_receta}}</b></h3>
                    </div> 
                    <div class="col-sm-4 col-md-2 text-center">
                        <h6 class="bg-danger py-2" style="border-radius: 5px;">Porciones <br>
                            @if ($habilitar_porciones)
                                <input id="nueva_porcion" wire:model="nueva_porcion" type="number" min="1" class="my-1 text-right"
                                style="width:50px;font-weight: bold"
                                onblur="comparar_porciones()">
                            @else
                                <input id="porciones" wire:model="porciones" type="number" min="1" class="my-1 text-right"
                                style="width:50px;font-weight: bold" disabled>
                            @endif                           
                            <i class="bi bi-pencil-square" style="cursor: pointer;" wire:click="habilitar_porciones"></i>
                        </h6> 
                    </div>
                    <div class="col-sm-3 col-md-2">
                        <button type="button" wire:click="doAction(2)" class="btn btn-warning">
                            Procedimiento
                        </button>
                    </div>
                    <div class="col-sm-2 col-md-1">
                        <button type="button" class="btn btn-secondary" title="Ayuda..."
                            onclick= "ayuda()"><i class="bi bi-question-square"></i>
                        </button>
                    </div>    
                    <div class="col-sm-3 col-md-2">
                        <button type="button" onclick="volver()" class="btn btn-dark">
                            Volver
                        </button>
                    </div> 
    			</div> 
                @include('common.messages')
                <div class="row mt-2">                    
                    {{-- <div class="form-group col-sm-6 col-md-2">
                        <label>Cantidad</label>
                        <input id="cantidad" wire:model.lazy="cantidad" type="text" 
                            class="form-control form-control-sm text-center" autocomplete="off">
                    </div>                     --}}
                    {{-- <div class="form-group col-sm-6 col-md-2">
                        <label >U. Medida</label>
                        <select id="unidad" wire:model="unidad" class="form-control form-control-sm text-left" 
                            wire:change="verificar_unidades">
                            <option value="Elegir">Elegir</option>
                            <option value="Un">Un</option>
                            <option value="Gr">Gr</option>
                            <option value="Kg">Kg</option>
                            <option value="Ml">Ml</option>
                            <option value="Lt">Lt</option>
                            <option value="Mt">Mt</option>
                        </select>		
                    </div> --}}
                    <div class="form-group col-sm-6 col-md-4">
                        <label >Ingrediente</label>
                    <div class="input-group">
                        <select wire:model="producto" class="form-control form-control-sm text-left" 
                            onchange="buscar_producto()">
                            <option value="Elegir">Elegir</option>
                            @foreach($productos as $t)
                            <option value="{{ $t->id }}">
                                {{$t->descripcion}}                         
                            </option> 
                            @endforeach                         
                        </select>	
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="agregar_producto()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg></span>
                        </div>	
                        </div>	
                    </div>
                    <div class="form-group col-sm-6 col-md-2">
                        <label>Cantidad</label>
                        <input id="cantidad" wire:model.lazy="cantidad" type="text" 
                            @if($producto != 'Elegir') enabled @else disabled @endif
                            class="form-control form-control-sm text-center" autocomplete="off">
                    </div> 
                    {{-- <div class="form-group col-sm-3 col-md-2">
                        <label>Presentación</label>
                        <input wire:model.lazy="presentacion" type="text" 
                            class="form-control form-control-sm text-center" disabled>
                    </div>   --}}
                    <div class="form-group col-sm-3 col-md-2">
                        <label>U. Medida</label>
                        <input wire:model.lazy="unidad_medida_presentacion" type="text" 
                            class="form-control form-control-sm text-center" disabled>
                    </div>  
                </div>

                <div class="table-responsive scroll">
                    <table class="table table-hover table-checkable table-sm">
                        <thead>
                            <tr>
                                <th class="text-center" style="background-color:gray;color:white;">CANT. RECETA</th>
                                <th class="text-center" style="background-color:gray;color:white;">INGREDIENTE</th>
                                <th class="text-center">MERMA</th>
                                <th class="text-right">CANT. REAL</th>
                                <th class="text-center">COSTO</th>
                                <th class="text-center">IMPORTE</th>
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($info as $r)
                            <tr>
                                @if (intval($r->cantidad) == floatval($r->cantidad))
                                    <td class="text-center" style="background-color:goldenrod;">{{number_format($r->cantidad)}} {{$r->unidad_de_medida}}</td>
                                @elseif ($r->unidad_de_medida == 'Un')
                                    <td class="text-center" style="background-color:goldenrod;">{{number_format($r->cantidad,1,',','.')}} {{$r->unidad_de_medida}}</td>
                                @else
                                    <td class="text-center" style="background-color:goldenrod;">{{number_format($r->cantidad,3,',','.')}} {{$r->unidad_de_medida}}</td>
                                @endif

                                <td class="text-left" style="background-color:goldenrod;">{{$r->descripcion}}</td>
                                <td class="text-center">{{$r->merma}}%</td>

                                @if (intval($r->cantidad_real) == floatval($r->cantidad_real))
                                    <td class="text-center">{{number_format($r->cantidad_real)}} {{$r->unidad_de_medida}}</td>
                                @elseif ($r->unidad_de_medida == 'Un')
                                    <td class="text-center">{{number_format($r->cantidad_real,1,',','.')}} {{$r->unidad_de_medida}}</td>
                                @else
                                    <td class="text-center">{{number_format($r->cantidad_real,3,',','.')}} {{$r->unidad_de_medida}}</td>
                                @endif

                                <td class="text-right">{{number_format($r->precio_costo,2,',','.')}}</td>
                                <td class="text-right">{{number_format($r->importe,2,',','.')}}</td>
                                <td class="text-center">
                                    <ul class="table-controls">
                                        <li>
                                            <a href="javascript:void(0);" wire:click="edit({{$r->id}})" data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);"          		
                                            onclick="Confirm({{$r->id}})"
                                            data-toggle="tooltip" data-placement="top" title="Eliminar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                        </li>
                                        <li>
                                            @if($r->principal == 'si')
                                            <a href="javascript:void(0);" wire:click="GrabarPrincipal('no',{{$r->id}})" data-toggle="tooltip" data-placement="top" title="Es Componente Principal">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill text-warning" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg>
                                            </a>
                                            @else                  
                                            <a href="javascript:void(0);" wire:click="GrabarPrincipal('si',{{$r->id}})" data-toggle="tooltip" data-placement="top" title="No es Componente Principal">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16"><path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z"/></svg>
                                            </a>
                                            @endif
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>                   
                </div>  

                <div class="row mt-3">
                    <div class="col-sm-12 col-md-4 mb-2">
                        <button type="button" wire:click="resetInput" onclick="setfocus('cantidad')" class="btn btn-dark mr-1">
                            <i class="mbri-left"></i> Cancelar
                        </button>
                        <button type="button" onclick="calcularPrecioVenta(id)" 
                            @if($selected_id) class="btn bg-warning" id="btnModificar"
                            @else class="btn bg-primary" id="btnGuardar" 
                            @endif>
                            @if($selected_id) <span style="text-decoration: underline;">M</span>odificar 
                            @else <span style="text-decoration: underline;">G</span>uardar 
                            @endif
                        </button> 
                    </div>
                    <div class="col-sm-12 col-md-8">
                        <div class="row">
                            <div class="offset-md-3 col-3 text-center">
                                <h6 class="bg-danger" style="border-radius: 5px;">Costo Total <br>$ {{number_format($total,2,',','.')}}</h6> 
                            </div>
                         
                            <div class="col-6 text-center">
                                <h5 class="bg-danger p-2" style="border-radius: 5px;">Costo Porción $ {{number_format($total_porcion,2,',','.')}}</h5> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    	</div>
    </div>
    <input type="hidden" id="preguntarPorPrecio" wire:model="preguntarPorPrecio">
    <input type="hidden" id="preguntarPorPrecio" wire:model="cambiar_precios">
    <input type="hidden" id="total" wire:model="total">
    @include('livewire.recetas.modalAyuda')	
    @else
    <input type="hidden" id="tieneProcedimiento" wire:model="procedimiento">
	@include('livewire.recetas.procedimiento')		
	@endif
</div>


<style type="text/css" scoped>
    .scroll{
        position: relative;
        height: 170px;
        margin-top: .3rem;
        overflow: auto;
    }
    thead tr th {     /* fija la cabecera de la tabla */
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #ffffff;
    }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script type="text/javascript">
    function calcularPrecioVenta(id) {
        if(id == 'btnGuardar'){
            if($('#total').val() > 0){
                if($('#porciones').val() == 0 || !$('#porciones').val()){
                    Swal.fire('Cancelado','La cantidad de Porciones debe ser un número válido','info');
                    return;
                }
            }else{
                if($('#nueva_porcion').val() == 0 || !$('#nueva_porcion').val()){
                    Swal.fire('Cancelado','La cantidad de Porciones debe ser un número válido','info');
                    return;
                }  
            }
        }   
        if(id == 'btnModificar'){
            if($('#total').val() > 0){
                if($('#porciones').val() == 0 || !$('#porciones').val()){
                    Swal.fire('Cancelado','La cantidad de Porciones debe ser un número válido','info');
                    return;
                }
            }else{
                if($('#nueva_porcion').val() == 0 || !$('#nueva_porcion').val()){
                    Swal.fire('Cancelado','La cantidad de Porciones debe ser un número válido','info');
                    return;
                }  
            }
        }  
    
        // if($('[id="preguntarPorPrecio"]').val() == 'si'){
        //     Swal.fire({
        //         icon: 'question',
        //         title: 'Elige una opción para ACTUALIZAR el PRODUCTO FINAL...',
        //         showDenyButton: true,
        //         confirmButtonColor: '#3085d6',
        //         denyButtonColor: '#d33',
        //         confirmButtonText: 'Deseo que solo se modifiquen el Precio de Costo y los Precios de Venta Sugeridos',
        //         denyButtonText: 'Deseo modificar el Precio de Costo, los Precios de Venta Sugeridos como así también los Precios de Venta de Lista',
        //         closeOnConfirm: false
        //     }).then((result) => {
        //         if (result.isConfirmed) {  
        //             window.livewire.emit('calcular_precio_venta', 'solo_costos', 'agregar', null, null);
        //         } else if (result.isDenied) {
        //             window.livewire.emit('calcular_precio_venta', 'cambiar_todo', 'agregar', null, null);
        //         }
        //     }); 
        // }else              
       window.livewire.emit('calcular_precio_venta', null, 'agregar', null, null); 
   
    }
    function Confirm(id)
    {
        var data_cambios = null;
        if($('#preguntarPorPrecio').val() == 'si'){
            Swal.fire({
                icon: 'question',
                title: 'Elige una opción para ACTUALIZAR el PRODUCTO FINAL...',
                showDenyButton: true,
                confirmButtonColor: '#3085d6',
                denyButtonColor: '#d33',
                confirmButtonText: 'Deseo que solo se modifiquen el Precio de Costo y los Precios de Venta Sugeridos',
                denyButtonText: 'Deseo modificar el Precio de Costo, los Precios de Venta Sugeridos como así también los Precios de Venta de Lista',
                closeOnConfirm: false
            }).then((result) => {
                if (result.isConfirmed) {  
                    data_cambios = 'solo_costos';
                    Confirm2(id, data_cambios);
                } else if (result.isDenied) {
                    data_cambios = 'cambiar_todo';
                    Confirm2(id, data_cambios);
                }
            });
        }else Confirm2(id, data_cambios);
    } 
    function Confirm2(id, data_cambios)
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
                    window.livewire.emit('calcular_precio_venta', data_cambios, 'eliminar', id, comentario);
                }
            }else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire('Cancelado', 'Tu registro está a salvo :)', 'error')
            }
        })
    }
    function volver()
    {
        window.location.href="{{url('productos')}}";
    }
    function agregar_producto()
    {
        window.location.href="{{url('productos')}}";
    }
    function buscar_producto()
    {
        window.livewire.emit('buscar_producto');
    }
    // function verificar_unidades()
    // {
    //     window.livewire.emit('verificar_unidades');
    // }
    function comparar_porciones()
    {
        if($('#nueva_porcion').val() == 0 || $('#nueva_porcion').val() == ''){
            Swal.fire('Cancelado','La cantidad de Porciones debe ser un número válido','info');    
        }else window.livewire.emit('comparar_porciones');
    }
    function ayuda()
    {
        $('#modalAyuda').modal('show');
    }

    window.onkeydown = PulsarTecla;
	function PulsarTecla(e)
    {
        tecla = e.keyCode;
        if(e.altKey == 1 && tecla == 77) document.getElementById("btnModificar").click();
        else if(e.altKey == 1 && tecla == 71) document.getElementById("btnGuardar").click();
        else if(tecla == 27) document.getElementById("btnCancelar").click();
    }

    function setfocus(id) {
        document.getElementById(id).focus();
    }

    window.onload = function() {
        $(document).ready(function() {
            setfocus('cantidad')
        });
        Livewire.on('cambiarPrecioDetalle',(cantidad)=>{
            setfocus('cantidad');
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
        Livewire.on('unidadesDeMedidaDiferentes',()=>{           
            setfocus('unidad');
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: '¡¡¡ATENCIÓN!!!',
                text: 'Las Unidades de Medida correspondientes a Cantidad y Presentación deben coincidir...',
                showConfirmButton: true
            })
		})
        Livewire.on('actualizar_porciones',()=>{ 
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Deseas cambiar la cantidad de Porciones?',
                text: 'Debes tener en cuenta que se producirán cambios en los Costos relacionados a este Producto...',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Continuar',
                showCancelButton: true,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value) {
                        window.livewire.emit('actualizarPorciones')
                    }
                }else if (result.dismiss === Swal.DismissReason.cancel) {
                    window.livewire.emit('resetPorciones');
                    Swal.fire('Cancelado', 'Tu registro está a salvo :)', 'error')
                }
            })
        })
        Livewire.on('focus',()=>{ 
            setfocus('cantidad');
        })
    }    
</script>