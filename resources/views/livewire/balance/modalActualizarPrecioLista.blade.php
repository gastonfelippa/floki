<div class="modal fade" id="modalActualizarPrecioLista" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualizar Precios</h5>
            </div> 
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <form>
                            <div class="row">                          
                                <div class="form-group col-12">
                                    <label for="producto">Producto</label>
                                    <input id="producto" class="form-control" disabled>                                                               
                                </div>
                            </div>
                            <div class="row">                          
                                <div class="form-group col-6">
                                    <label for="precio_sugerido_l1">Pr. de Vta. Sug. Lista 1</label>                                  
                                    <input id="precio_sugerido_l1" class="form-control" disabled>                                                              
                                </div>
                                <div class="form-group col-6">
                                    <label for="precio_venta_l1">Precio de Venta Lista 1</label>                                   
                                    <input id="precio_venta_l1" class="form-control" placeholder="Ingrese el Importe" autoconplete ="off">                                                               
                                </div>
                            </div>
                            <div class="row">                          
                                <div class="form-group col-6">
                                    <label for="precio_sugerido_l2">Pr. de Vta. Sug. Lista 2</label>                                  
                                    <input id="precio_sugerido_l2" class="form-control" disabled>                                                              
                                </div>
                                <div class="form-group col-6">
                                    <label for="precio_venta_l2">Precio de Venta Lista 2</label>                                   
                                    <input id="precio_venta_l2" class="form-control" placeholder="Ingrese el Importe" autoconplete ="off">                                                               
                                </div>
                            </div>
                        </form>
                        <input type="hidden" id="productoId">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal">Cancelar</button>
                <button id="btnGuardar" class="btn btn-primary" onclick="guardarPrecio()" data-dismiss="modal" type="button">Guardar</button>
            </div>
        </div>
    </div>
</div>
