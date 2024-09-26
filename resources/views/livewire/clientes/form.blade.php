<div class="col-sm-12 col-md-8 layout-spacing"> 
    @include('common.alerts')
    @include('common.messages')
	<div class="widget-content-area">
        <div class="widget-one">
            <h5 style="border-bottom: 1px solid rgba(128, 128, 128, 0.2);"><b>@if($selected_id == 0) Nuevo Cliente  @else Editar Cliente @endif</b></h5>      
            <div class="row mt-3">                               
                <div class="col-12 col-md-4 layout-spacing">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="bi bi-person"></i></span>
                        </div>
                        <input id="nombre" type="text" class="form-control text-uppercase" placeholder="Nombre" wire:model.lazy="nombre" autofocus autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-4 layout-spacing">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="bi bi-person"></i></span>
                        </div>
                        <input id="apellido" type="text" class="form-control text-uppercase" placeholder="Apellido" wire:model.lazy="apellido" autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-4 layout-spacing">
                    <div class="input-group ">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="bi bi-telephone"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder=Teléfono wire:model.lazy="telefono" autocomplete="off">
                    </div>
                </div>   
            </div>
            <div class="row">                               
                <div class="col-7 col-md-4 layout-spacing">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="bi bi-fullscreen-exit"></i></span>
                        </div>
                        <input type="text" class="form-control text-capitalize" placeholder=Calle wire:model.lazy="calle" autocomplete="off">
                    </div>
                </div>
                <div class="col-5 col-md-4 layout-spacing">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="bi bi-hash"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder=Número wire:model.lazy="numero" autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-4 layout-spacing">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="bi bi-globe"></i></span>
                        </div>
                        <select wire:model="localidad" class="form-control">
                            <option value="Elegir">Localidad</option>
                            @foreach($localidades as $l)
                            <option value="{{ $l->id }}">
                                {{$l->descripcion}}
                            </option>                                       
                            @endforeach 
                        </select>
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="openModal()"><i class="bi bi-plus-circle"></i></span>
                        </div>
                    </div>			               
                </div>                    
            </div>
            <div class="row">
                @if($modViandas == "1")
                <div class="col-9 col-md-4 layout-spacing">
                    <h6>¿Es cliente de Viandas?</h6>
                    <div class="form-check form-check-inline">
                        @if($vianda)
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="vianda_si" value="1" checked>
                        @else
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="vianda_si" value="1">
                        @endif
                        <label class="form-check-label" for="inlineRadio1">Si</label>
                    </div>
                    <div class="form-check form-check-inline">
                        @if($vianda)
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="vianda_no" value="0">
                        @else
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="vianda_no" value="0" checked>
                        @endif
                        <label class="form-check-label" for="inlineRadio2">No</label>
                    </div>
                </div>
                @elseif($modConsignaciones == "1" && $comercioTipo == 11)
                <div class="col-9 col-md-4 layout-spacing">
                    <h6>¿Es Consignatario?</h6>
                    <div class="form-check form-check-inline">
                        @if($consignatario)
                        <input class="form-check-input" type="radio" name="inlineRadioOptions2" id="consignatario_si" value="1" checked>
                        @else
                        <input class="form-check-input" type="radio" name="inlineRadioOptions2" id="consignatario_si" value="1">
                        @endif
                        <label class="form-check-label" for="inlineRadio3">Si</label>
                    </div>
                    <div class="form-check form-check-inline">
                        @if($consignatario)
                        <input class="form-check-input" type="radio" name="inlineRadioOptions2" id="consignatario_no" value="0">
                        @else
                        <input class="form-check-input" type="radio" name="inlineRadioOptions2" id="consignatario_no" value="0" checked>
                        @endif
                        <label class="form-check-label" for="inlineRadio4">No</label>
                    </div>
                </div>
                @endif
            </div>
            <div class="row ">
                <div class="col-12">
                    <button type="button" wire:click="doAction(1)" class="btn btn-primary mr-1">
                        <i class="mbri-left"></i> Cancelar
                    </button>
                    <button type="button" id="btnGuardar"
                        onclick="guardar()"  
                        class="btn btn-success">
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


