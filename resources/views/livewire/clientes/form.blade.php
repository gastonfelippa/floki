<div class="col-sm-12 col-md-8 layout-spacing"> 
    @include('common.alerts')
    @include('common.messages')
	<div class="widget-content-area">
        <div class="widget-one">
            <h5><b>@if($selected_id == 0) Nuevo Cliente  @else Editar Cliente @endif</b></h5>      
            <div class="row mt-3">                               
                <div class="col-12 col-md-4 layout-spacing">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/></svg></span>
                        </div>
                        <input id="nombre" type="text" class="form-control text-uppercase" placeholder="Nombre" wire:model.lazy="nombre" autofocus autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-4 layout-spacing">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/></svg></span>
                        </div>
                        <input id="apellido" type="text" class="form-control text-uppercase" placeholder="Apellido" wire:model.lazy="apellido" autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-4 layout-spacing">
                    <div class="input-group ">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16"><path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/></svg></span>
                        </div>
                        <input type="text" class="form-control" placeholder=Teléfono wire:model.lazy="telefono" autocomplete="off">
                    </div>
                </div>   
            </div>
            <div class="row">                               
                <div class="col-7 col-md-4 layout-spacing">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-fullscreen-exit" viewBox="0 0 16 16"><path d="M5.5 0a.5.5 0 0 1 .5.5v4A1.5 1.5 0 0 1 4.5 6h-4a.5.5 0 0 1 0-1h4a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 1 .5-.5zm5 0a.5.5 0 0 1 .5.5v4a.5.5 0 0 0 .5.5h4a.5.5 0 0 1 0 1h-4A1.5 1.5 0 0 1 10 4.5v-4a.5.5 0 0 1 .5-.5zM0 10.5a.5.5 0 0 1 .5-.5h4A1.5 1.5 0 0 1 6 11.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 0-.5-.5h-4a.5.5 0 0 1-.5-.5zm10 1a1.5 1.5 0 0 1 1.5-1.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 0-.5.5v4a.5.5 0 0 1-1 0v-4z"/></svg></span>
                        </div>
                        <input type="text" class="form-control text-capitalize" placeholder=Calle wire:model.lazy="calle" autocomplete="off">
                    </div>
                </div>
                <div class="col-5 col-md-4 layout-spacing">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></span>
                        </div>
                            <input type="text" class="form-control" placeholder=Número wire:model.lazy="numero" autocomplete="off">
                    </div>
                </div>
                <div class="col-12 col-md-4 layout-spacing">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16"><path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/></svg></span>
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
                            <span class="input-group-text" onclick="openModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg></span>
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
                    <button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">
                        <i class="mbri-left"></i> Cancelar
                    </button>
                    <button type="button" id="btnGuardar"
                        onclick="guardar()"  
                        class="btn btn-primary">
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


