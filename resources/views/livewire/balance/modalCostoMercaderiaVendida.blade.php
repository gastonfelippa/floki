<div class="modal fade" id="modalCostoMercaderiaVendida" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fórmula para el Costo de la Mercadería Vendida</h5>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="row mb-1">
                        <label for="existencia_inicial" style="color: black;" class="col-6 offset-2 col-form-label">Existencia Inicial</label>
                        <input type="text" style="color: black;" class="col-4 form-control text-right" id="existencia_inicial" readonly value={{number_format($e_i,2,',','.')}}>
                    </div>
                    <div class="row mb-1">
                        <img src="images/signo_mas.png"class="col-2" height="40" alt="image">
                        <label for="total_compras" style="color: black;" class="col-6 col-form-label">Compras</label>
                        <input type="text" style="color: black;" class="col-4 form-control text-right" id="total_compras" readonly value={{number_format($compras,2,',','.')}}>
                    </div>
                    <div class="row mb-1">
                        <img src="images/signo_mas.png"class="col-2" height="40" alt="image">
                        <label for="res_por_tenencia" style="color: black;" class="col-6 col-form-label">Resultado Por Tenencia</label>
                        <input type="text" style="color: black;" class="col-4 form-control text-right" id="res_por_tenencia" readonly value={{number_format($resultado_por_tenencia,2,',','.')}}>
                    </div>
                    <div class="row">
                        <img src="images/signo_menos.png"class="col-2" height="40" alt="image">
                        <label for="existencia_final" style="color: black;" class="col-6 col-form-label">Existencia Final</label>
                        <input type="text" style="color: black;" class="col-4 form-control text-right" id="existencia_final" readonly value={{number_format($e_f,2,',','.')}}>
                    </div>
                    <hr style="height:1px;border:none;color:#333;background-color:#333;" />
                    <div class="mb-1 row">
                        <img src="images/signo_igual.png" class="col-2" height="40" alt="image">
                        <label for="c_m_v" style="color: black;font-weight:bold;" class="col-6 col-form-label">Costo de la Mercadería Vendida</label>
                        <input type="text" style="color: black;font-weight:bold;" class="col-4 form-control text-right" id="c_m_v" readonly value={{number_format($cmv,2,',','.')}}>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"></i>Volver</button>
            </div>
        </div>
    </div>
</div>
