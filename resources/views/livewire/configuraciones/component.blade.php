
<div class="row layout-top-spacing justify-content-center">  
<div class="col-sm-12 col-md-6 layout-spacing">
<div class="widget-content-area">
    <div class="widget-one">
      
            @include('common.alerts')
            @include('common.messages')
            <div class="col-12">
                <h3 class="text-center"><b>Configuraciones Varias</b></h3>
            </div>    
            <div class="row mt-3">                               
                <div class="col-12 layout-spacing">
                    <label >Leyenda Pié de Factura</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></span>
                        </div>
                        <input id="leyenda" type="text" maxlength="43" class="form-control" wire:model.lazy="leyenda_factura" autofocus autocomplete="off">
                    </div>
                </div>  
            </div>
            <div class="row">                               
                <div class="col-12 layout-spacing">
                    <label >Período de Arqueo (en días)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar3" viewBox="0 0 16 16"><path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857V3.857z"/><path d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/></svg></span>
                        </div>
                        <input type="text" class="form-control" wire:model.lazy="periodo_arqueo" autocomplete="off">
                    </div>
                </div>         
            </div>
            <div class="row">
                <div class="col-12 col-md-7 layout-spacing">
                    <h6>Impresiones en Hoja A4</h6>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="1" wire:model="imp_por_hoja" checked>
                        <label class="form-check-label" for="inlineRadio1">1 hoja</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="2" wire:model="imp_por_hoja">
                        <label class="form-check-label" for="inlineRadio2">1/2 hoja</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="4" wire:model="imp_por_hoja">
                        <label class="form-check-label" for="inlineRadio3">1/4 hoja</label>
                    </div>
                </div>
                <div class="col-12 col-md-5 layout-spacing">
                    <h6>Imprimir Duplicados</h6>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions2" id="inlineRadio1" value="1" wire:model="imp_duplicado">
                        <label class="form-check-label" for="inlineRadio1">Si</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions2" id="inlineRadio2" value="0" wire:model="imp_duplicado" checked>
                        <label class="form-check-label" for="inlineRadio2">No</label>
                    </div>
                </div>
            </div>
            <div class="row">                               
                <div class="col-12 layout-spacing">
                    @include('common.btnCancelarGuardar')
                </div>
            </div>
       
    </div>	  
</div>
</div>
</div>

<script type="text/javascript">

</script>