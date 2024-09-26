<div class="col-sm-12 col-md-5 layout-spacing">
	<div class="widget-content-area">
    @include('common.alerts')
	@include('common.messages')
        <div class="widget-one">
            <h5><b>Editar Asignaciones de Módulos</b></h5>
            <div class="row" id="tblModulos" >
                <div class="col"> 
                    <div class="card border-dark text-dark bg-light mb-3">
                        <div class="card-header">Comercio: {{$comercio}}</div>
                            <div class="card-body">
            <div class="form-check">
                <input class="form-check-input" data-name="modViandas" {{$modViandas == 1 ? 'checked' : ''}} type="checkbox" id="mViandas">
                <label class="form-check-label" for="mViandas">
                    Módulo Viandas
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" data-name="modComandas" {{$modComandas == 1 ? 'checked' : ''}} type="checkbox" id="mComandas">
                <label class="form-check-label" for="mComandas">
                    Módulo Comandas
                </label>
            </div>           
            <div class="form-check">
                <input class="form-check-input" data-name="modDelivery" {{$modDelivery == 1 ? 'checked' : ''}} type="checkbox" id="mDelivery">
                <label class="form-check-label" for="mDelivery">
                    Módulo Delivery
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" data-name="modConsignaciones" {{$modConsignaciones == 1 ? 'checked' : ''}} type="checkbox" id="mConsignaciones">
                <label class="form-check-label" for="mConsignaciones">
                    Módulo Consignaciones
                </label>
            </div>           
            <div class="form-check">
                <input class="form-check-input" data-name="modClubes" {{$modClubes == 1 ? 'checked' : ''}} type="checkbox" id="mClubes">
                <label class="form-check-label" for="mClubes">
                    Módulo Clubes
                </label>
            </div>           
            </div>           
            </div>           
            </div>           
            </div>           
            </div>   
            <button type="button" onclick="AsignarModulos()" class="btn btn-primary" enabled>Asignar Módulos</button>      
        
			<!-- @include('common.btnCancelarGuardar')           -->
        </div>
    </div>	
</div>

