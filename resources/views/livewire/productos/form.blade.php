<div class="col-12 layout-spacing">
    <div class="widget-content-area mb-2">
        <div class="widget-one">
            @include('common.messages') <!-- validación de campos -->
            <div class="row">
                <div class="col-sm-12 col-md-3">
                    <h4><b>
                            @if (!$selected_id)
                                Nuevo Producto:
                            @else
                                Editar Producto
                            @endif
                        </b></h4>
                </div>
                <div class="col-sm-12 col-md-9 mb-1">
                    <!-- lo uso para mostrar el texto base en el modal -->
                    <h4 class="text-capitalize text-warning" id="input_nombre"><b>{{ $nuevo_producto }}</b></h4>
                </div>
            </div>
            @include('components.nav-bar')
        </div>
    </div>
    <form>
        @csrf
        <!--  sección descripcion -->
        @if ($action_edit == 'datos')
            <div class="col-12 widget-content-area mb-2">
                <div class="widget-one">
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-3">
                            <label>Nombre</label>
                            <input id="nombre" onblur="validarProducto()" wire:model.lazy="descripcion"
                                type="text" class="form-control form-control-sm text-capitalize" maxlength="30"
                                autofocus autocomplete="off">
                        </div>
                        <div class="form-group col-sm-6 col-md-2">
                            <label>Tipo</label>
                            <select wire:model="tipo" class="form-control form-control-sm text-left">
                                <option value="Elegir">Elegir</option>
                                @foreach ($tipos as $t)
                                    <option value="{{ $t->id }}">{{ $t->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-6 col-md-2">
                            <label>Categoría</label>
                            <select wire:model="categoria" class="form-control form-control-sm text-left"
                                @if ($tipo == 'Elegir') disabled @endif>
                                <option value="Elegir">Elegir</option>
                                @foreach ($categorias as $t)
                                    <option value="{{ $t->id }}">
                                        {{ $t->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-12 col-md-3">
                            <label>Presentación/Unidad de Medida</label>
                            <i class="bi bi-info-circle icono ml-2" data-toggle="tooltip" data-placement="top"
                                title="El valor que agregues aquí corresponderá a la cantidad de Producto y Unidad de Medida a la que hace referencia el Precio de Costo. Será tenido en cuenta en el momento de confeccionar Recetas como así también para el control de Stock."></i>
                            <div class="row">
                                <div class="col-sm-6 col-md-6 mbfloki">
                                    <input wire:model.lazy="presentacion" type="text"
                                        class="form-control form-control-sm text-right" autocomplete="off">
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <select wire:model="unidad_de_medida"
                                        class="form-control form-control-sm text-left pl-2">
                                        <option value="Elegir">Elegir</option>
                                        <option value="Un">Un</option>
                                        <option value="Gr">Gr</option>
                                        <option value="Kg">Kg</option>
                                        <option value="Ml">Ml</option>
                                        <option value="Lt">Lt</option>
                                        <option value="Mt">Mt</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-sm-12 col-md-2">
                            <label>Estado</label>
                            <select wire:model="estado" class="form-control form-control-sm text-left">
                                <option value="Disponible">Disponible</option>
                                <option value="Suspendido">Suspendido</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!--  sección precios -->
        @if ($action_edit == 'precios')
            <div class="col-12 widget-content-area">
                <div class="widget-one">
                    <div class="row">
                        <div class="form-group col-sm-6 col-md-2">
                            <label>Precio/Costo</label>
                            <input id="precio_costo" wire:model.lazy="precio_costo" onblur="precioBajo()"
                                type="number" class="form-control form-control-sm" 
                                @if ($tiene_receta == 'si' || $tipo == 3 || $tipo == 4)
                                : disabled @endif autocomplete="off">
                        </div>
                        <div class="form-group col-sm-6 col-md-2">
                            <label>% Merma</label>
                            <i class="bi bi-calculator icono ml-3" onclick="openModalCalculadora()"
                                data-toggle="tooltip" data-placement="top" title="Calculadora de Mermas"></i>
                            <input wire:blur="calcular_precio_venta()" wire:model.lazy="merma" 
                                type="number" class="form-control form-control-sm" 
                                @if ($tiene_receta == 'si' || $tipo == 3 || $tipo == 4)
                                : disabled @endif autocomplete="off">
                        </div>
                        @if ($tipo != 1 && $tipo != 4)
                            <div class="form-group col-sm-6 col-md-2">
                                @if ($modDelivery == '1')
                                    <label>P/Vta Salón (Sugerido)</label>
                                @else
                                    <label>Pr/Vta Lista 1 (Suger)</label>
                                @endif
                                <input wire:model.lazy="precio_venta_sug_l1" type="number"
                                    class="form-control form-control-sm" disabled>
                            </div>
                            <div class="form-group col-sm-6 col-md-2">
                                @if ($modDelivery == '1')
                                    <label>P/Vta Deliv. (Sugerido)</label>
                                @else
                                    <label>Pr/Vta Lista 2 (Sugerido)</label>
                                @endif
                                <input wire:model.lazy="precio_venta_sug_l2" type="number"
                                    class="form-control form-control-sm" disabled>
                            </div>
                            <div class="form-group col-sm-6 col-md-2">
                                @if ($modDelivery == '1')
                                    <label>Pr/Vta Salón (Lista)</label>
                                @else
                                    <label>Pr/Vta Lista 1 (Lista)</label>
                                @endif
                                <input wire:model.lazy="precio_venta_l1" type="number"
                                    class="form-control form-control-sm"
                                    @if ($tipo == 3 && $selected_id == null
                                    || $tipo == 3 && $precio_costo == 0) : disabled @endif>
                            </div>
                            <div class="form-group col-sm-6 col-md-2">
                                @if ($modDelivery == '1')
                                    <label>Pr/Vta Delivery (Lista)</label>
                                @else
                                    <label>Pr/Vta Lista 2 (Lista)</label>
                                @endif
                                <input wire:model.lazy="precio_venta_l2" type="number"
                                    class="form-control form-control-sm"
                                    @if ($tipo == 3 && $selected_id == null
                                    || $tipo == 3 && $precio_costo == 0) : disabled @endif>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        <!--  sección stock -->
        @if ($action_edit == 'stock')
            <div class="col-12 widget-content-area">
                <div class="widget-one">
                    <div class="row">
                        <div class="form-group col-sm-6 col-md-2">
                            <label>Controlar Stock</label>
                            <select wire:model="controlar_stock" class="form-control form-control-sm text-left">
                                <option value="si">Si</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-4 col-md-2">
                            <label>Stock Actual ({{ $unidad_de_medida }})</label>
                            @if ($stock_actual >= 0 || $stock_actual == null)
                                <input id="stock_actual" wire:model.lazy="stock_actual" onblur="validar('Actual')"
                                    type="number" class="form-control form-control-sm" autocomplete="off">
                            @else
                                <input id="stock_actual" wire:model.lazy="stock_actual" type="number"
                                    class="form-control form-control-sm" style="color: red;" autocomplete="off">
                            @endif
                        </div>
                        <div class="form-group col-sm-4 col-md-2">
                            <label>Stock Ideal ({{ $unidad_de_medida }})</label>
                            <input id="stock_ideal" wire:model.lazy="stock_ideal" onblur="validar('Ideal')"
                                type="number" class="form-control form-control-sm" autocomplete="off">
                        </div>
                        <div class="form-group col-sm-4 col-md-2">
                            <label>Stock Mínimo ({{ $unidad_de_medida }})</label>
                            <input id="stock_minimo" wire:model.lazy="stock_minimo" onblur="validar('Mínimo')"
                                type="number" class="form-control form-control-sm" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!--  sección comandas -->
        @if ($action_edit == 'comandas')
            <div class="col-12 widget-content-area">
                <div class="widget-one">
                    @if ($modComandas == '1')
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-2">
                                <label>Sector Comanda</label>
                                <div class="input-group">
                                    <select wire:model="sector" class="form-control form-control-sm text-left">
                                        <option value=0>Ninguno</option>
                                        @foreach ($sectores as $t)
                                            <option value="{{ $t->id }}">{{ $t->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if ($se_imprime == '1')
                                <div class="form-group col-sm-12 col-md-3">
                                    <label>Texto Base Comanda</label>
                                    <div class="input-group">
                                        <select wire:model="texto" class="form-control form-control-sm text-left">
                                            <option value="Elegir">Elegir</option>
                                            @foreach ($textos as $t)
                                                <option value="{{ $t->id }}">
                                                    {{ $t->descripcion }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <span class="input-group-text" onclick="openModal()"><i
                                                    class="bi bi-plus-circle"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-sm-6 col-md-2">
                                    <label>¿Lleva salsa?</label>
                                    <div class="input-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="salsa_si" @if ($salsa) : checked @endif>
                                            <label class="form-check-label" for="inlineRadio1">Si</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                                id="salsa_no" @if (!$salsa) : checked @endif>
                                            <label class="form-check-label" for="inlineRadio2">No</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-md-2">
                                    <label>¿Lleva guarnición?</label>
                                    <div class="input-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions2"
                                                id="guarn_si" @if ($guarnicion) : checked @endif>
                                            <label class="form-check-label" for="inlineRadio1">Si</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions2"
                                                id="guarn_no" @if (!$guarnicion) : checked @endif>
                                            <label class="form-check-label" for="inlineRadio2">No</label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </form>
    <input type="hidden" id="selected_id" wire:model="selected_id">
    <input type="hidden" id="costo_actual" wire:model="costo_actual">
    {{-- <input type="hidden" id="tipo" wire:model="tipo"> --}}

    <div class="col-12 widget-content-area">
        <div class="widget-one">
            <div class="form-group" style="padding:0px;margin:0px;">
                <button type="button" wire:click="doAction(1)" class="btn btn-primary mr-1" id="btnCancelar">
                    <i class="mbri-left"></i> Cancelar
                </button>
                <button type="button" id="btnGuardar" onclick="guardar()" class="btn btn-success">
                    Guardar
                </button>
                @if ($tiene_receta == 'si')
                    <button type="button" id="btnReceta" wire:click="ver_receta({{ $selected_id }})"
                        class="btn btn-danger ml-1">
                        Crear/Modificar Receta
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<style type="text/css" scoped>
    .icono {
        color: green;
        cursor: pointer;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        document.getElementById("nombre").focus();
    });
</script>
