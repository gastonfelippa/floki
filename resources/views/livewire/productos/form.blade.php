<div class="col-12 layout-spacing"> 
    <div class="widget-content-area ">
        <div class="widget-one">
            @include('common.alerts')   
            @include('common.messages') <!-- validación de campos -->
            <h5><b>@if($selected_id == null) Nuevo Producto  @else Editar Producto @endif </b></h5>            
            @if($selected_id > 0)
                @if($cambiar_precios == 'solo_costos')
                    <p><b>(Al Modificar el COSTO del Producto SOLO SE ACTUALIZARÁN éste y los Precios de Venta Sugeridos)</b>
                @else
                    <p><b>(Al Modificar el Costo del Producto SE ACTUALIZARÁN TODOS LOS PRECIOS... el de Costo, los Sugeridos y los de Lista)</b>
                @endif
                <!-- <span class="badge bg-danger" onClick="opcionCambiarPrecios()">Cambiar Opción de Guardado</span></p>                 -->
            @endif
            <form class="mb-3"> 
                <div class="row mt-3">
                    <div class="form-group col-md-5 col-sm-12">
                        <label >Nombre del Producto</label>
                        <input id="nombre" onblur="validarProducto()" wire:model.lazy="descripcion" type="text" class="form-control text-capitalize" maxlength="30" autofocus autocomplete="off">
                    </div>
                    <div class="form-group col-12 col-md-3">
                        <label >Categoría</label>
                        <select wire:model="categoria" class="form-control text-left">
                            <option value="Elegir">Elegir</option>
                            @foreach($categorias as $t)
                            <option value="{{ $t->id }}">
                                {{$t->descripcion}}
                            </option>                                       
                            @endforeach                              
                        </select>		
                    </div>
                    <div class="form-group col-md-2 col-sm-12">
                        <label >Tipo</label>
                        <select wire:model="tipo" class="form-control text-left">
                            <option value="Ambos">Ambos</option>
                            <option value="Art. Venta">Art. Venta</option>
                            <option value="Art. Compra">Art. Compra</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 col-sm-12">
                        <label >Estado</label>
                        <select wire:model="estado" class="form-control text-left">
                            <option value="Disponible">Disponible</option>
                            <option value="Suspendido">Suspendido</option>
                        </select>
                    </div>
                </div>
                <!--  sección precios -->
                <!-- <hr/> -->
                <div class="row">
                    @if($tiene_receta == 'si')
                    <div class="form-group col-md-2 col-sm-12">
                        <label >Precio/Costo</label>
                        <input wire:model.lazy="precio_costo" type="text" class="form-control" disabled>
                    </div>
                    @else
                    <div class="form-group col-md-2 col-sm-12">
                        <label >Precio/Costo</label>
                        <input id="precio_costo" wire:model.lazy="precio_costo" onblur="precioBajo()" type="text" class="form-control">
                    </div>
                    @endif
                    @if($tipo <> 'Art. Compra')
                    <div class="form-group col-md-3 col-sm-12">
                        @if($modDelivery == "1")
                        <label >Pr/Vta Salón (Sugerido)</label>
                        @else
                        <label >Pr/Vta Lista 1 (Sugerido)</label>
                        @endif
                        <input wire:model.lazy="precio_venta_sug_l1" type="text" class="form-control" disabled>
                    </div>  
                    <div class="form-group col-md-3 col-sm-12">
                        @if($modDelivery == "1")
                        <label >Pr/Vta Delivery (Sugerido)</label>
                        @else
                        <label >Pr/Vta Lista 2 (Sugerido)</label>
                        @endif
                        <input wire:model.lazy="precio_venta_sug_l2" type="text" class="form-control" disabled>
                    </div> 
                    <div class="form-group col-md-2 col-sm-12">
                        @if($modDelivery == "1")
                        <label >Pr/Vta Salón (Lista)</label>
                        @else
                        <label >Pr/Vta Lista 1 (Lista)</label>
                        @endif
                        <input wire:model.lazy="precio_venta_l1" type="text" class="form-control">
                    </div>  
                    <div class="form-group col-md-2 col-sm-12">
                        @if($modDelivery == "1")
                        <label >Pr/Vta Delivery (Lista)</label>
                        @else
                        <label >Pr/Vta Lista 2 (Lista)</label>
                        @endif
                        <input wire:model.lazy="precio_venta_l2" type="text" class="form-control">
                    </div> 
                    @endif
                </div>
                	
                <!-- <hr/> -->
                @if($tiene_sp == null)
                <div class="row">  
                    <div class="form-group col-md-3 col-sm-12">
                        <div class="row">
                            <div class="col-5">
                                <label >Stock Actual</label>
                            </div>
                            <div class="col-7">
                                <span class="badge bg-danger" onClick="existenciaInicial()">Existencia inicial</span></p>
                            </div>
                        </div>                        
                        <input wire:model.lazy="stock_actual" type="text" class="form-control">
                    </div>
                    <div class="form-group col-md-2 col-sm-12">
                        <label >Stock Ideal</label>
                        <input wire:model.lazy="stock_ideal" type="text" class="form-control">
                    </div>                       
                    <div class="form-group col-md-2 col-sm-12">
                        <label >Stock Mínimo</label>
                        <input wire:model.lazy="stock_minimo" type="text" class="form-control">
                    </div>
                </div>
                <!-- <hr/> -->
                @endif                       
                @if($modComandas == "1")
                <div class="row">
                    <div class="form-group col-sm-12 col-md-2">
                        <label >Sector Comanda</label>
                        <div class="input-group">
                            <select wire:model="sector" class="form-control text-left">
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
                            <select wire:model="texto" class="form-control text-left">
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
                   
                    <div class="form-group col-sm-9 col-md-2">
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
                    <div class="form-group col-sm-9 col-md-4">
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
                <div class="col-9 col-md-4 layout-spacing">
                    <div class="form-check">
                        @if($tiene_receta == 'si')
                        <input class="form-check-input" type="checkbox" value="si" id="receta_si" checked>
                        @else
                        <input class="form-check-input" type="checkbox" value="no" id="receta_si">
                        @endif
                        <label class="form-check-label" for="defaultCheck1">Tiene fórmula o receta</label>
                    </div>
                    <div class="form-check">
                        @if($controlar_stock == 'si')
                        <input class="form-check-input" type="checkbox" value="si" id="stock_si" checked>
                        @else
                        <input class="form-check-input" type="checkbox" value="no" id="stock_si">
                        @endif
                        <label class="form-check-label" for="defaultCheck1">Controlar stock</label>
                    </div>
                </div>
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
                    <!-- @if($selected_id > 0)      
                    <button type="button" id="btnSubproducto"
                        wire:click="agregar_sp()"   
                        class="btn btn-danger ml-5">
                        Agregar/Modificar Subproductos
                    </button>
                    @endif -->
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


<script type="text/javascript">
    $(document).ready(function() {
        document.getElementById("nombre").focus();
    });
</script>


 