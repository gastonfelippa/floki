<div class="modal fade" id="modalComentario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
            </div>       
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <input id="factura_id" type="hidden">
                            <div class="row">
                                <div class="md-form mb-3 col-12">
                                    <textarea id="comentario" rows="2" class="md-textarea form-control" placeholder="Texto..."></textarea>
                                </div>                           
                            </div>                        
                        </form>    
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btnEliminar" class="btn btn-danger col-6" data-dismiss="modal" onclick="saveComentario(1)">Eliminar Comentario</button>
                <button class="btn btn-dark" data-dismiss="modal">Cancelar</button>
                <button id="btnGuardar" class="btn btn-primary" data-dismiss="modal" type="button" onclick="saveComentario(0)"></button>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        document.getElementById("comentario").focus();
    }
</script>