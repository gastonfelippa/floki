<div class="col-sm-12 col-md-6 layout-spacing">    
    <div class="widget-content-area">
        <div class="widget-one">
            @include('common.messages')
            <h5><b>@if($selected_id ==0) Nuevo Rubro  @else Editar Rubro @endif</b></h5>
            <div class="row mt-3">                               
                <div class="col-12 layout-spacing">
                    <div class="input-group ">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-basket" viewBox="0 0 16 16"><path d="M5.757 1.071a.5.5 0 0 1 .172.686L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1v4.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 13.5V9a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h1.217L5.07 1.243a.5.5 0 0 1 .686-.172zM2 9v4.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V9H2zM1 7v1h14V7H1zm3 3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 4 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 6 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 8 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5z"/></svg></span>
                        </div>
                        <input id="nombre" name="nombre" type="text" class="form-control text-uppercase" placeholder="Nombre" wire:model="descripcion" autofocus autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 layout-spacing">
                    <div class="form-check form-switch ml-3">
                        @if($mostrar_al_vender == 'si')
                        <input class="form-check-input" type="checkbox" id="mostrar" checked>
                        @else
                        <input class="form-check-input" type="checkbox" id="mostrar">
                        @endif
                        <label class="form-check-label" for="flexSwitchCheckChecked">Mostrar Rubro al Vender</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">
                        Cancelar
                    </button>
                    <button type="button" onclick="guardar()" class="btn btn-primary">
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