<div class="col-sm-12 col-md-6 layout-spacing">
    <div class="widget-content-area">
        <div class="widget-one">
            @include('common.alerts')
            @include('common.messages')
            <h5><b>
                    @if ($selected_id == 0)
                        Nueva Categoría
                    @else
                        Editar Categoría
                    @endif
                </b></h5>
            <div class="row">
                <div class="col-12 layout-spacing">
                    <label>Descripción</label>
                    <input id="nombre" type="text" class="form-control text-uppercase" wire:model="descripcion"
                        autofocus autocomplete="off">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 layout-spacing">
                    <label>Tipo de Artículos que incluye</label>
                    <select wire:model="tipo" class="form-control form-control-sm text-left">
                        <option value="Elegir">Elegir</option>
                        @foreach ($tipos as $t)
                            <option value="{{ $t->id }}">{{ $t->descripcion }}</option>
                        @endforeach
                    </select>
                </div>  
                <div class="col-sm-12 col-md-6 layout-spacing">
                    <label>Rubro</label>
                    <select wire:model="rubro" class="form-control form-control-sm text-left"
                        @if ($tipo == 'Elegir') disabled @endif>
                        <option value="Elegir">Elegir</option>
                        @foreach ($rubros as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>            
            </div>
            @if ($tipo == 2 || $tipo == 3)
            <div class="row">
                <div class="col-sm-12 col-md-6 layout-spacing">
                    @if ($modComandas == '1')
                        <label>Margen Lista Salón (%)</label>
                    @else
                        <label>Margen Lista 1 (%)</label>
                    @endif
                    <input type="text" class="form-control form-control-sm " wire:model="margen_1">
                </div>
                <div class="col-sm-12 col-md-6 layout-spacing">
                    @if ($modComandas == '1')
                        <label>Margen Lista Delivery (%)</label>
                    @else
                        <label>Margen Lista 2 (%)</label>
                    @endif
                    <input type="text" class="form-control form-control-sm " wire:model="margen_2">
                </div>
            </div>
            @endif
            {{-- <div class="row">
                <div class="col-12 col-md-6 layout-spacing">
                    <div class="form-check form-switch ml-3">
                        @if ($mostrar_al_vender == 'si')
                        <input class="form-check-input" type="checkbox" id="mostrar" checked>
                        @else
                        <input class="form-check-input" type="checkbox" id="mostrar">
                        @endif
                        <label class="form-check-label" for="flexSwitchCheckChecked">Mostrar Categoría al Vender</label>
                    </div>
                </div>
            </div> --}}
            <div class="row">
                <div class="col-12">
                    <button type="button" wire:click="doAction(1)" class="btn btn-info mr-1">
                        Cancelar
                    </button>
                    <button type="button" onclick="guardar()" class="btn btn-success">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        document.getElementById("nombre").focus();
    });
</script>
