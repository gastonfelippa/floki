<div class="col-sm-12 col-md-6 layout-spacing"> 
    @include('common.alerts')
    @include('common.messages')
	<div class="widget-content-area">
        <div class="widget-one">
            <h5><b>@if($selected_id == 0) Nuevo Cheque  @else Editar Cheque @endif</b></h5>      
            <div class="row mt-3">                        
                <div class="form-group col-8">
                    <label >Banco/Sucursal</label>
                    <div class="input-group">
                        <select wire:model.lazy="banco" class="form-control form-control-sm">
                            <option value="Elegir" >Elegir</option>
                            @foreach($bancos as $t)
                            <option value="{{ $t->id }}">
                                {{$t->descripcion}} - {{$t->sucursal}}                        
                            </option> 
                            @endforeach   
                        </select>
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="openModalBancos()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg></span>
                        </div> 
                    </div>
                </div>    
                <div class="form-group col-4">
                    <label >Número</label>
                    <input wire:model.lazy="numero" maxlength="8" type="text" class="form-control form-control-sm" autocomplete="off">
                </div> 
            </div>
            <div class="row"> 
                <div class="col-4">
                    <label >Fecha de Emisión</label>
                    <input wire:model.lazy="fecha_de_emision" type="text" class="form-control form-control-sm flatpickr flatpickr-input active"> 
                </div>
                <div class="col-4">
                    <label >Fecha de Pago</label>
                    <input wire:model.lazy="fecha_de_pago" type="text" class="form-control form-control-sm flatpickr flatpickr-input active">
                </div> 
                <div class="form-group col-4">
                    <label >Importe</label>
                    <input wire:model.lazy="importe" type="text" class="form-control form-control-sm">
                </div>  
            </div>
            <div class="row">         
                <div class="form-group col-4">
                    <label >CUIT Titular</label>
                    <input wire:model.lazy="cuitTitular" type="text" class="form-control form-control-sm" autocomplete="off">
                </div>
                <div class="form-group col-5">
                    <label>Procedencia/Cliente</label>
                    <div class="input-group">
                        <select wire:model.lazy="cliente" class="form-control form-control-sm">
                            <option value="Elegir" >Elegir</option>
                            @foreach($clientes as $t)
                            <option value="{{ $t->id }}">
                                {{$t->apellido}} {{$t->nombre}}                        
                            </option> 
                            @endforeach   
                        </select>
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="agregarCliente()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg></span>
                        </div> 
                    </div>
                </div> 
                <div class="form-group col-3">
                    <label>Estado</label>
                    <input wire:model.lazy="estado" type="text" class="form-control form-control-sm" disabled>

                    <!-- <div class="input-group">
                        <select class="form-control form-control-sm">
                            <option value="en_cartera">En Cartera</option> 
                            <option value="entregado">Entregado</option> 
                            <option value="rechazado">Rechazado</option> 
                            <option value="en_cartera">En Cartera</option> 
                        </select>
                    </div> -->
                </div> 
            </div>
            <div class="row ">
                <div class="col-12">
                    <button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">
                        <i class="mbri-left"></i> Cancelar
                    </button>
                    <button type="button" id="btnGuardar"
                        wire:click="StoreOrUpdate()"  
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
        App.init();
        $(".flatpickr").flatpickr({
            enableTime: false,
            dateFormat: "d-m-Y",
            'locale': 'es',
            'position': 'above'
        });
    });
</script>


