<div class="modal fade" id="modalProductosElaborados" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:darkslategrey;">
                <h5 class="modal-title" style="color:white;"></h5>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                            <input id="productoId" type="hidden">
                        <div class="row">
                            <div class="form-group col-4">
                                <label >Stock Actual</label>
                                <input id="stock_actual" type="text" class="form-control text-right" disabled>
                            </div>
                            <div class="form-group col-4">
                                <label >Stock Nuevo</label>
                                <input id="stock_nuevo" type="text" class="form-control text-right">
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"></i>Cancelar</button>
                <button class="btn btn-primary" type="button" onclick="actualizar()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">  
    window.onload = function() {
        document.getElementById("stock_nuevo").focus();
    }
</script>
