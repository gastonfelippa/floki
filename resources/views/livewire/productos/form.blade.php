<div class="col-sm-12 col-md-6 layout-spacing"> 
    <div class="widget-content-area ">
        <div class="widget-one">
            @include('common.alerts')  <!--  validación de campos -->
            @include('common.messages')  
            <h5><b>@if($selected_id ==0) Nuevo Producto  @else Editar Producto @endif </b></h5>
            <form class="mb-3"> 
                <div class="row mt-3">
                    <div class="form-group col-sm-7">
                        <label >Nombre del Producto</label>
                        <input id="nombre" onblur="validarProducto()" wire:model.lazy="descripcion" type="text" class="form-control text-capitalize" autofocus autocomplete="off">
                    </div>
                    <div class="col-12 col-md-5 ">
                        <label >Categoría</label>
                        <div class="input-group">
                            <select wire:model="categoria" class="form-control text-center">
                                <option value="Elegir" disabled="">Elegir</option>
                                @foreach($categorias as $t)
                                <option value="{{ $t->id }}">
                                    {{$t->descripcion}}
                                </option>                                       
                                @endforeach                              
                            </select>			               
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4 col-sm-12">
                        <label >Estado</label>
                        <select wire:model="estado" class="form-control text-center">
                            <option value="DISPONIBLE">Disponible</option>
                            <option value="SUSPENDIDO">Suspendido</option>
                            <option value="SIN STOCK">Sin Stock</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 col-sm-12">
                        <label >Stock</label>
                        <input wire:model.lazy="stock" type="text" class="form-control">
                    </div>
                    <div class="form-group col-md-5 col-sm-12">
                        <label >Tipo</label>
                        <select wire:model="tipo" class="form-control text-center">
                            <option value="Art. Venta">Art. Venta</option>
                            <option value="Art. Compra">Art. Compra</option>
                            <option value="Ambos">Ambos</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6 col-sm-12">
                        <label >Precio de Costo</label>
                        <input wire:model.lazy="precio_costo" onblur="calcularPrecioVenta()" type="text" class="form-control">
                    </div>
                    <div class="form-group col-md-6 col-sm-12">
                        <label >Precio de Venta</label>
                        <input wire:model.lazy="precio_venta" type="text" class="form-control">
                    </div>                        
                </div>
            </form>
            @include('common.btnCancelarGuardar')
        </div>
    </div>
</div>

<script type="text/javascript">
    window.onload = function() {
        document.getElementById("nombre").focus();
    }
</script>


 