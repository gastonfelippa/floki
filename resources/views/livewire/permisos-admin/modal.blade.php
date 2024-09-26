<div class="modal fade" id="modalAdmiteCaja" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"></h5>
        </div>       
        <div class="modal-body">
            <div class="widget-content-area">
                <div class="widget-one">
                    <form>
                        <div class="row">
                            <div class="col-12 layout-spacing">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="admiteCaja" id="si" value="1" >
                                    <label class="form-check-label" for="inlineRadio1">Si</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="admiteCaja" id="no" value="0" checked>
                                    <label class="form-check-label" for="inlineRadio2">No</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-dark" data-dismiss="modal" onclick="clearRoleSelected()"><i class="flaticon-cancel-12"></i>Cancelar</button>
            <button class="btn btn-primary saveTarifa" type="button" onclick="CrearRol()">Guardar</button>
        </div>
    </div>
</div>
</div>
