<div class="col-12 layout-spacing"> 
    <div class="widget-content-area ">
        <div class="widget-one">
            @include('common.alerts')   
            @include('common.messages') <!-- validación de campos -->
            <div class="row">
                <div class="col-3">
                    <h4><b>@if(!$selected_id) Nuevo Producto  @else Editar Producto @endif </b></h4> 
                </div>
                <div class="col-9">
                    @if($selected_id)
                        @if($cambiar_precios == 'solo_costos')
                            <p><b>(Al Modificar el COSTO del Producto SOLO SE ACTUALIZARÁN éste y los Precios de Venta Sugeridos)</b>
                        @else
                            <p><b>(Al Modificar el Costo del Producto SE ACTUALIZARÁN TODOS LOS PRECIOS... el de Costo, los Sugeridos y los de Lista)</b>
                        @endif
                        <!-- <span class="badge bg-danger" onClick="opcionCambiarPrecios()">Cambiar Opción de Guardado</span></p>                 -->
                    @endif
                </div>
            </div>
            <form class="">
                @csrf 
                <!--  sección descripcion --> 
                <div class="row">
                    <div class="form-group col-sm-12 col-md-3">
                        <label >Nombre</label>
                        <input id="nombre" onblur="validarProducto()" wire:model.lazy="descripcion" 
                            type="text" class="form-control form-control-sm text-capitalize" maxlength="30" autofocus autocomplete="off">
                    </div>
                    <div class="form-group col-sm-6 col-md-3">
                        <label >Categoría</label>
                        <select wire:model="categoria" class="form-control form-control-sm text-left">
                            <option value="Elegir">Elegir</option>
                            @foreach($categorias as $t)
                            <option value="{{ $t->id }}">
                                {{$t->descripcion}}
                            </option>                                       
                            @endforeach                              
                        </select>		
                    </div>
                    <div class="form-group col-sm-6 col-md-3">
                        <label >Tipo</label>
                        <select wire:model="tipo" class="form-control form-control-sm text-left">
                            <option value="Art. Compra/Venta">Art. Compra/Venta</option>
                            <option value="Art. Compra">Art. Compra</option>
                            <option value="Art. Venta c/receta">Art. Venta c/Receta</option>
                            <option value="Art. Elaborado">Art. Elaborado c/Receta</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-12 col-md-3">
                        <label>Presentación/Unidad de Medida</label>
                        <i class="bi bi-info-circle icono ml-2"
                        data-toggle="tooltip" data-placement="top"
                        title="El valor que agregues aquí corresponderá a la cantidad de Producto y Unidad de Medida a la que hace referencia el Precio de Costo. Será tenido en cuenta en el momento de confeccionar Recetas como así también para el control de Stock."></i>
                        <div class="row">
                            <div class="col-sm-6 col-md-6">
                                <input wire:model.lazy="presentacion" type="text" class="form-control form-control-sm text-right">
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <select wire:model="unidad_de_medida" class="form-control form-control-sm text-left pl-2">
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
                </div>
                <!--  sección precios -->
                <div class="row">
                    @if($tiene_receta == 'si' || $tipo == "Art. Venta c/receta" || $tipo == "Art. Elaborado")
                        <div class="form-group col-sm-6 col-md-2">
                            <label >Precio/Costo</label>
                            <input id="precio_costo" wire:model.lazy="precio_costo" type="text" class="form-control form-control-sm" disabled>
                        </div>
                        <div class="form-group col-sm-6 col-md-2">
                            <label >% Merma</label>
                            <i class="bi bi-calculator icono ml-3" onclick="openModalCalculadora()"
                            data-toggle="tooltip" data-placement="top"
                            title="Calculadora de Mermas"></i>  
                            <input wire:model.lazy="merma" type="text" class="form-control form-control-sm" disabled>
                        </div>
                    @else
                        <div class="form-group col-sm-6 col-md-2">
                            <label >Precio/Costo</label>
                            <input id="precio_costo" wire:model.lazy="precio_costo" onblur="precioBajo()" type="text" class="form-control form-control-sm">
                        </div>
                        <div class="form-group col-sm-6 col-md-2">
                            <label >% Merma</label>
                            <i class="bi bi-calculator icono ml-3" onclick="openModalCalculadora()"
                            data-toggle="tooltip" data-placement="top"
                            title="Calculadora de Mermas"></i>                             
                            <input wire:blur="calcular_precio_venta()"  wire:model.lazy="merma" type="text" class="form-control form-control-sm" enabled>
                        </div>
                    @endif
                    @if($tipo <> 'Art. Compra'&& $tipo <> "Art. Elaborado")
                        <div class="form-group col-sm-6 col-md-2">
                            @if($modDelivery == "1")
                                <label >P/Vta Salón (Sugerido)</label>
                            @else
                                <label >Pr/Vta Lista 1 (Suger)</label>
                            @endif
                            <input wire:model.lazy="precio_venta_sug_l1" type="text" class="form-control form-control-sm" disabled>
                        </div>  
                        <div class="form-group col-sm-6 col-md-2">
                            @if($modDelivery == "1")
                                <label >P/Vta Deliv.(Sugerido)</label>
                            @else
                                <label >Pr/Vta Lista 2 (Sugerido)</label>
                            @endif
                            <input wire:model.lazy="precio_venta_sug_l2" type="text" class="form-control form-control-sm" disabled>
                        </div> 
                        <div class="form-group col-sm-6 col-md-2">
                            @if($modDelivery == "1")
                                <label >Pr/Vta Salón (Lista)</label>
                            @else
                                <label >Pr/Vta Lista 1 (Lista)</label>
                            @endif
                            <input wire:model.lazy="precio_venta_l1" type="text" class="form-control form-control-sm">
                        </div>  
                        <div class="form-group col-sm-6 col-md-2">
                            @if($modDelivery == "1")
                                <label >Pr/Vta Delivery (Lista)</label>
                            @else
                                <label >Pr/Vta Lista 2 (Lista)</label>
                            @endif
                            <input wire:model.lazy="precio_venta_l2" type="text" class="form-control form-control-sm">
                        </div> 
                    @endif
                </div>
                <!--  sección stock -->	
                <div class="row"> 
                    <div class="form-group col-sm-6 col-md-2">
                        <label >Controlar Stock</label>
                        <select wire:model="controlar_stock" class="form-control form-control-sm text-left">
                            <option value="si">Si</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    {{-- @if(!$balance)
                    <div class="form-group col-sm-6 col-md-2">
                        <label >Stock Inicial ({{$unidad_de_medida}})</label>
                        <i class="bi bi-pencil-square icono ml-1" onclick="openModalStockInicial()"
                        data-toggle="tooltip" data-placement="top"
                        title="Agregar o Modificar Stock Inicial"></i>
                        <input id="stock_inicial" wire:model.lazy="stock_inicial" type="text" class="form-control form-control-sm" disabled>
                     
                    </div>
                    @endif  --}}
                    <div class="form-group col-sm-4 col-md-2">
                        <label >Stock Actual ({{$unidad_de_medida}})</label> 
                        {{-- @if($stock_actual >= 0 && $stock_actual == null)             --}}
                        @if($stock_actual >= 0 || $stock_actual == null)            
                            <input id="stock_actual" wire:model.lazy="stock_actual" onblur="validar('Actual')" type="text" class="form-control form-control-sm">
                        @else
                            <input id="stock_actual" wire:model.lazy="stock_actual" type="text" class="form-control form-control-sm" style="color: red;">
                        @endif
                    </div>
                    <div class="form-group col-sm-4 col-md-2">
                        <label >Stock Ideal ({{$unidad_de_medida}})</label>
                        <input id="stock_ideal" wire:model.lazy="stock_ideal" onblur="validar('Ideal')" type="text" class="form-control form-control-sm">
                    </div>                       
                    <div class="form-group col-sm-4 col-md-2">
                        <label >Stock Mínimo ({{$unidad_de_medida}})</label>
                        <input id="stock_minimo" wire:model.lazy="stock_minimo" onblur="validar('Mínimo')" type="text" class="form-control form-control-sm">
                    </div>
                    <div class="form-group col-sm-12 col-md-2">
                        <label >Estado</label>
                        <select wire:model="estado" class="form-control form-control-sm text-left">
                            <option value="Disponible">Disponible</option>
                            <option value="Suspendido">Suspendido</option>
                        </select>
                    </div>
                </div>
                <!--  sección comandas -->   
                @if($modComandas == "1")
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-2">
                            <label >Sector Comanda</label>
                            <div class="input-group">
                                <select wire:model="sector" class="form-control form-control-sm text-left">
                                    <option value=0>Ninguno</option>
                                    @foreach($sectores as $t)
                                    <option value="{{ $t->id }}">{{$t->descripcion}}</option>                                       
                                    @endforeach                              
                                </select>			               
                            </div>
                        </div>
                        @if($se_imprime == "1")
                            <div class="form-group col-sm-12 col-md-3">
                                <label >Texto Base Comanda</label>
                                <div class="input-group">
                                    <select wire:model="texto" class="form-control form-control-sm text-left">
                                        <option value="Elegir">Elegir</option>
                                        @foreach($textos as $t)
                                        <option value="{{ $t->id }}">
                                            {{$t->descripcion}}
                                        </option>                                       
                                        @endforeach                              
                                    </select>		               
                                    <div class="input-group-append">
                                        <span class="input-group-text" onclick="openModal()">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg></span>
                                    </div>
                                </div>
                            </div>                        
                            <div class="form-group col-sm-6 col-md-2">
                                <label>¿Lleva salsa?</label>
                                <div class="input-group">
                                    <div class="form-check form-check-inline">
                                        @if($salsa)
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="salsa_si" checked>
                                        @else
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="salsa_si">
                                        @endif
                                        <label class="form-check-label" for="inlineRadio1">Si</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        @if($salsa)
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="salsa_no">
                                        @else
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="salsa_no" checked>
                                        @endif
                                        <label class="form-check-label" for="inlineRadio2">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-6 col-md-2">
                                <label>¿Lleva guarnición?</label>
                                <div class="input-group">
                                    <div class="form-check form-check-inline">
                                        @if($guarnicion)
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions2" id="guarn_si" checked>
                                        @else
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions2" id="guarn_si">
                                        @endif
                                        <label class="form-check-label" for="inlineRadio1">Si</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        @if($guarnicion)
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions2" id="guarn_no">
                                        @else
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions2" id="guarn_no" checked>
                                        @endif
                                        <label class="form-check-label" for="inlineRadio2">No</label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </form>
            <div class="row ">
                <div class="col-12 mb-2">
                    <button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">
                        <i class="mbri-left"></i> Cancelar
                    </button>
                    <button type="button" id="btnGuardar"
                        onclick="guardar()"   
                        class="btn btn-primary">
                        Guardar
                    </button> 
                    @if($tiene_receta == 'si')
                    <button type="button" id="btnReceta"
                        wire:click="ver_receta({{$selected_id}})"   
                        class="btn btn-danger ml-1">
                        Crear/Modificar Receta
                    </button> 
                    @endif 
                </div>             
            </div>
        </div>
        <input type="hidden" id="selected_id" wire:model="selected_id">
        <input type="hidden" id="costo_actual" wire:model="costo_actual">
        <input type="hidden" id="tipo" wire:model="tipo">
    </div>
</div>

<style type="text/css" scoped>
.icono {
    color: red;
    cursor: pointer;
    font-weight: bold;
}

</style>

<script type="text/javascript">
    $(document).ready(function() {
        document.getElementById("nombre").focus();
    });
</script>


 