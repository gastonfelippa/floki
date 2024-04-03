<div class="modal fade" id="modal_stock_inicial" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stock Inicial</h5>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="row">
                        <div class="form-group col-6">
                            <label>Cantidad</label>
                            <i class="bi bi-info-circle icono ml-2"
                                data-toggle="tooltip" data-placement="top"
                                title="El valor que agregues aquí corresponderá a la cantidad de Producto y Unidad de Medida a la que hace referencia el Precio de Costo."></i>
                            <input id="stockInicial" type="text" class="form-control form-control-sm text-right">
                        </div>
                        <div class="form-group col-6">
                            <label>Unidad de Medida</label>
                            <select id="unidad_de_medida" class="form-control form-control-sm text-left pl-2">
                                <option value="Elegir">Elegir</option>
                                <option value="Un">Un</option>
                                <option value="Gr">Gr</option>
                                <option value="Kg">Kg</option>
                                <option value="Ml">Ml</option>
                                <option value="Lt">Lt</option>
                                <option value="Mt">Mt</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-12">
                            <label >Precio de Costo Histórico</label>
                            <input id="costo_historico" type="text" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"></i>Cancelar</button>
                <button class="btn btn-primary" type="button" onclick="cargarStockInicial()">Cargar</button>
            </div>
        </div>
    </div>
</div>
