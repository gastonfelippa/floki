<div class="row layout-top-spacing justify-content-center">  
<div class="col-12 layout-spacing">
    <!-- encabezado -->
    <div class="widget-content-area mb-2">
        <div class="widget-one">
            @include('common.alerts')
            @include('common.messages')
            <div class="col-12 mb-3">
                <h3 class="text-center"><b>Configuraciones Varias</b></h3>
            </div>
            @include('components.nav-bar-config')
        </div>
    </div>
    <form>
        @csrf
        <!--  sección datos -->
        @if ($action_edit == 'datos')
            <div class="col-12 widget-content-area">
                <div class="widget-one">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-2">
                            <h6>Leyenda Pie de Factura (máximo 43 caracteres)</h6>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-edit-2">
                                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                        </svg></span>
                                </div>
                                <input id="leyenda" type="text" maxlength="43" class="form-control"
                                    wire:model.lazy="leyenda_factura" autofocus autocomplete="off">
                            </div>
                        </div>
                            <div class="col-sm-12 col-md-4 mb-2">
                                <h6>Período de Arqueo (en días)</h6>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><svg xmlns="http://www.w3.org/2000/svg"
                                                width="16" height="16" fill="currentColor"
                                                class="bi bi-calendar3" viewBox="0 0 16 16">
                                                <path
                                                    d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857V3.857z" />
                                                <path
                                                    d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" />
                                            </svg></span>
                                    </div>
                                    <input type="text" class="form-control" wire:model.lazy="periodo_arqueo"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-2">
                                <h6>Horario Apertura</h6>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><svg xmlns="http://www.w3.org/2000/svg"
                                                width="22" height="22" fill="currentColor"
                                                class="bi bi-stopwatch" viewBox="0 0 16 16">
                                                <path
                                                    d="M8.5 5.6a.5.5 0 1 0-1 0v2.9h-3a.5.5 0 0 0 0 1H8a.5.5 0 0 0 .5-.5V5.6z" />
                                                <path
                                                    d="M6.5 1A.5.5 0 0 1 7 .5h2a.5.5 0 0 1 0 1v.57c1.36.196 2.594.78 3.584 1.64a.715.715 0 0 1 .012-.013l.354-.354-.354-.353a.5.5 0 0 1 .707-.708l1.414 1.415a.5.5 0 1 1-.707.707l-.353-.354-.354.354a.512.512 0 0 1-.013.012A7 7 0 1 1 7 2.071V1.5a.5.5 0 0 1-.5-.5zM8 3a6 6 0 1 0 .001 12A6 6 0 0 0 8 3z" />
                                            </svg></span>
                                    </div>
                                    <input type="text" wire:model.lazy="hora_apertura"
                                        class="form-control flatpickrTime flatpickr-input active" autocomplete="off">
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        @endif
        <!--  sección opciones -->
        @if ($action_edit == 'opciones')
            <div class="col-12 widget-content-area">
                <div class="widget-one">
                    <div class="row">
                        <div class="col-12">
                            <h6>Opciones de Guardado al Cargar Compras</h6>
                            <div class="form-check form-check-inline @if($opcion_de_guardado_compra == 0) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions12"
                                    id="inlineRadio10" value="0" wire:model="opcion_de_guardado_compra">
                                <label class="form-check-label" for="inlineRadio10">NO DESEO MODIFICAR NINGÚN PRECIO...
                                    ni de Costo, ni Sugeridos ni de Lista</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-check-inline @if($opcion_de_guardado_compra == 1) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions12"
                                    id="inlineRadio11" value="1" wire:model="opcion_de_guardado_compra">
                                <label class="form-check-label" for="inlineRadio11">Deseo que solo se modifiquen los
                                    Precios de Costo y de Venta Sugeridos</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-check-inline @if($opcion_de_guardado_compra == 2) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions12"
                                    id="inlineRadio12" value="2" wire:model="opcion_de_guardado_compra">
                                <label class="form-check-label" for="inlineRadio12">Deseo modificar los Precios de Costo
                                    y de Venta Sugeridos como así también los Precios de Lista</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-12 col-xs-12">
                        <hr>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h6>Opciones de Guardado al "Modificar Manualmente el Costo" de un Producto</h6>
                            <div class="form-check form-check-inline @if($opcion_de_guardado_producto == 1) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions13" id="inlineRadio14" 
                                    value="1" wire:model="opcion_de_guardado_producto">
                                <label class="form-check-label" for="inlineRadio14">Deseo que solo se modifiquen los
                                    Precios de Costo y de Venta Sugeridos</label>
                            </div>
                            <div class="form-check form-check-inline @if($opcion_de_guardado_producto == 2) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions13" id="inlineRadio15"
                                    value="2" wire:model="opcion_de_guardado_producto">
                                <label class="form-check-label" for="inlineRadio15">Deseo modificar los Precios de
                                    Costo y de Venta Sugeridos como así también los Precios de Lista</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!--  sección cálculo de precios -->
        @if ($action_edit == 'precios')
            <div class="col-12 widget-content-area">
                <div class="widget-one">
                    <div class="row">
                        <div class="col-sm-12 col-md-4">
                            <h6>Cálculo del Precio de Venta</h6>
                            <div class="form-check form-check-inline @if($calcular_precio_de_venta == 0) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="inlineRadio" value="0" wire:model="calcular_precio_de_venta">
                                <label class="form-check-label" for="inlineRadio">Sumar Margen al Costo del
                                    producto</label>
                            </div>
                            <div class="form-check form-check-inline @if($calcular_precio_de_venta == 1) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                    id="inlineRadio1" value="1" wire:model="calcular_precio_de_venta" checked>
                                <label class="form-check-label" for="inlineRadio1">Obtener Margen sobre el Precio de
                                    Venta</label>
                            </div>
                        </div>
                        <div class="form-group col-sm-12 col-xs-12">
                            <hr>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <h6>Redondear Precio de Venta</h6>
                            <div class="form-check form-check-inline @if($redondear_precio_de_venta == 1) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions1"
                                    id="inlineRadio2" value="1" wire:model="redondear_precio_de_venta" checked>
                                <label class="form-check-label" for="inlineRadio2">Si</label>
                            </div>
                            <div class="form-check form-check-inline @if($redondear_precio_de_venta == 0) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions1"
                                    id="inlineRadio3" value="0" wire:model="redondear_precio_de_venta">
                                <label class="form-check-label" for="inlineRadio3">No</label>
                            </div>
                        </div>
                        <div class="form-group col-sm-12 col-xs-12">
                            <hr>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <h6>Permitir ventas sin stock</h6>
                            <div class="form-check form-check-inline @if($venta_sin_stock == 1) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions2"
                                    id="inlineRadio4" value="1" wire:model="venta_sin_stock">
                                <label class="form-check-label" for="inlineRadio4">Si</label>
                            </div>
                            <div class="form-check form-check-inline @if($venta_sin_stock == 0) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions2"
                                    id="inlineRadio5" value="0" wire:model="venta_sin_stock" checked>
                                <label class="form-check-label" for="inlineRadio5">No</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!--  sección impresiones -->
        @if ($action_edit == 'impresiones')
            <div class="col-12 widget-content-area">
                <div class="widget-one">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <h6>Impresiones en Hoja A4</h6>
                            <div class="form-check form-check-inline @if($imp_por_hoja == 1) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions3"
                                    id="inlineRadio6" value="1" wire:model="imp_por_hoja" checked>
                                <label class="form-check-label" for="inlineRadio6">1 hoja</label>
                            </div>
                            <div class="form-check form-check-inline @if($imp_por_hoja == 2) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions3"
                                    id="inlineRadio7" value="2" wire:model="imp_por_hoja">
                                <label class="form-check-label" for="inlineRadio7">1/2 hoja</label>
                            </div>
                            <div class="form-check form-check-inline @if($imp_por_hoja == 4) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions3"
                                    id="inlineRadio8" value="4" wire:model="imp_por_hoja">
                                <label class="form-check-label" for="inlineRadio8">1/4 hoja</label>
                            </div>
                        </div>
                        <div class="form-group col-sm-12 col-xs-12">
                            <hr>
                        </div>
                        <div class="col-12 col-md-4">
                            <h6>Imprimir Duplicados</h6>
                            <div class="form-check form-check-inline @if($imp_duplicado == 1) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions4"
                                    id="inlineRadio9" value="1" wire:model="imp_duplicado">
                                <label class="form-check-label" for="inlineRadio9">Si</label>
                            </div>
                            <div class="form-check form-check-inline @if($imp_duplicado == 0) bg-dark @endif">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions4"
                                    id="inlineRadio10" value="0" wire:model="imp_duplicado" checked>
                                <label class="form-check-label" for="inlineRadio10">No</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </form>
    <!--  sección datos de prueba -->
    @if ($action_edit == 'prueba')
        <div class="col-12 widget-content-area">
            <div class="widget-one">
                <div class="form-group" style="padding:0px;margin:0px;">  
                    <button type="button" onclick="borrarDatos()" class="btn btn-warning mb-1">
                        Borrar Datos de Prueba</button>
                    <button type="button" onclick="recuperarDatos()" class="btn btn-warning mb-1">
                        Recuperar Datos de Prueba</button>
                    <button type="button" onclick="" class="btn btn-success mb-1">
                        Restaurar a valores preestablecidos</button>
                    <button type="button" wire:click="truncate_peps()" class="btn btn-success mb-1">
                        Borrar Datos de Prueba sin posibilidad de recuperarlos</button>
                </div>
            </div>
        </div>
    @else
        <div class="col-12 widget-content-area">
            <div class="widget-one">
                <div class="form-group" style="padding:0px;margin:0px;">
                    <button type="button" onclick="salir()" class="btn btn-dark">Cancelar</button>
                    <button type="button" wire:click.prevent="StoreOrUpdate" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    @endif
</div>
</div>

<script type="text/javascript">
    function salir() {
        window.location.href = "{{ url('home') }}";
    }

    function borrarDatos() {
        //¿DESEAS ELIMINAR TODOS LOS REGISTROS DE PRUEBA?
        Swal.fire({
            position: 'center',
            icon: 'question',
            title: 'CONFIRMAR',
            html: '<div class="text-left">Estás a punto de eliminar: <br>' +
                'Todas las Compras y sus detalles <br>' +
                'Todas las Ventas, sus detalles y formas de pago <br>' +
                'Todas las Comandas y sus detalles <br>' +
                'Todas las Auditorías <br>' +
                'Todas el sistema de Stock</div> <br>' +
                '<b>ATENCIÓN!!! No podrás deshacer esta acción...</b>',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.livewire.emit('borrarDatos')
                swal.close()
            }
        })
    }

    function recuperarDatos() {
        Swal.fire({
            position: 'center',
            icon: 'question',
            title: 'CONFIRMAR',
            text: '¿DESEAS RECUPERAR TODOS LOS REGISTROS DE PRUEBA?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.livewire.emit('recuperarDatos')
                swal.close()
            }
        })
    }
</script>
