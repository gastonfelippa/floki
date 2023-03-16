<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <input type="hidden" id="producto_id">
                <input type="hidden" id="selected_id">
                <h5 class="modal-title"></h5>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row"> 
                                <label >Cantidad a reponer</label>       
                                <input id="cantidad_a_reponer" type="text" class="form-control text-capitalize" placeholder="Ingresa una cantidad" autocomplete="off">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"></i>Cancelar</button>
                <button class="btn btn-primary" type="button" onclick="save()">Guardar</button>
            </div>
        </div>
    </div>
</div>