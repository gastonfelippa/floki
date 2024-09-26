<div class="col-sm-12 col-md-5 layout-spacing">
    <div class="widget-content-area">
        <div class="widget-one">
            @include('common.messages')
            <h5><b>
                    @if ($selected_id == 0)
                        Nuevo Rubro
                    @else
                        Editar Rubro
                    @endif
                </b></h5>
            <div class="row">
                <div class="col-12 layout-spacing">
                    <label>Descripción</label>
                    <input id="nombre" name="nombre" type="text" class="form-control text-uppercase"
                        wire:model="descripcion" autofocus autocomplete="off">
                </div>
                <div class="col-12 layout-spacing">
                    <label>Mostrar al Vender</label>
                    <select wire:model="mostrar" class="form-control form-control-sm text-left">
                        <option value="si">Si</option>
                        <option value="no">No</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">
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
