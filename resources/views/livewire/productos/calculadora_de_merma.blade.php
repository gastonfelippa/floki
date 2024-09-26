<div class="modal fade" id="modal_calculadora" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Calculadora de Mermas</h5>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="row">
                        <div class="form-group col-6">
                            <label >Cantidad/Peso Bruto</label>
                            <input id="peso_bruto" type="text" class="form-control" autofocus>
                        </div>
                        <div class="form-group col-6">
                            <label >Cantidad/Peso Neto</label>
                            <input id="peso_neto" type="text" class="form-control">
                        </div>
                    </div> 
                    <div class="row">    
                        <div class="form-group col-6">
                            <label >% Merma</label>
                            <input id="merma" type="text" class="form-control" disabled>
                        </div>    
                        <div class="form-group col-6">
                            <label >% Rendimiento</label>
                            <input id="rendimiento" type="text" class="form-control" disabled>
                        </div>    
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"></i>Cancelar</button>
                <button class="btn btn-primary" type="button" onclick="calcular_merma()">Calcular</button>
            </div>
        </div>
    </div>
</div>
