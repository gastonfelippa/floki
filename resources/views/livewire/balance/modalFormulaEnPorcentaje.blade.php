<div class="modal fade" id="modalFormulaEnPorcentaje" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fórmula para el cálculo del Margen de Contribución Promedio Ponderado en Porcentaje</h5>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="row mb-1">
                        <label for="dividendo_porcentaje" style="color: black;" class="col-9 col-form-label">Total (Cantidad * Margen)</label>
                        <input type="text" style="color: black;" class="col-3 form-control text-right" id="dividendo_porcentaje" readonly>
                    </div>
                    <div class="mb-2 row">
                        <img src="images/signo_division.png"class="col-2" height="40" alt="image">
                        <label for="divisor_porcentaje" style="color: black;" class="col-7 col-form-label">Total (Cantidad * Precio Venta Lista 1)</label>
                        <input type="text" style="color: black;background-color:white;" readonly class="col-3 form-control text-right" id="divisor_porcentaje" >
                    </div>
                    <hr style="height:1px;border:none;color:#333;background-color:#333;" />
                    <div class="mb-3 row">
                        <img src="images/signo_igual.png" class="col-2" height="40" alt="image">
                        <label for="resultado_porcentaje" style="color: black;" class="col-7 col-form-label">Margen de Contribución Promedio Ponderado</label>
                        <input type="text" style="color: black;" readonly class="col-3 form-control text-right" id="resultado_porcentaje" >
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"></i>Volver</button>
            </div>
        </div>
    </div>
</div>
