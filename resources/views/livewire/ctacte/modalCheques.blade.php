<div class="modal fade" id="modalCheques" tabindex="-2" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Datos del cheque</h5>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <div class="row">  
                            <div class="col-8">                        
                                <div class="form-group">
                                    <label >Banco/Sucursal</label>
                                    <div class="input-group">
                                        <select id="banco" class="form-control form-control-sm">
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
                            </div>
                            <div class="form-group col-4">
                                <label >Número</label>
                                <input id="numCheque" maxlength="8" type="text" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <div class="row">                          
                            <div class="form-group col-6">
                                <label >Fecha de emisión</label>
                                <input id="fechaDeEmision" type="text" class="form-control" placeholder="dd/mm/aaaa" autocomplete="off">
                            </div>                          
                            <div class="form-group col-6">
                                <label >Fecha de pago</label>
                                <input id="fechaDePago" type="text" class="form-control" placeholder="dd/mm/aaaa" autocomplete="off">
                            </div>
                        </div>
                        <div class="row">                          
                            <div class="form-group col-6">
                                <label >Importe</label>
                                <input id="importeCheque" type="text" class="form-control" autocomplete="off">
                            </div>                       
                            <div class="form-group col-6">
                                <label >CUIT Titular</label>
                                <input id="cuitTitular" type="text" class="form-control" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" onclick="guardarDatosCheque()" data-dismiss="modal" type="button">Guardar</button>
            </div>
        </div>
    </div>
</div>