<div class="modal fade" id="modalFormulaPopularidad" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fórmula para el cálculo de la Popularidad Promedio</h5>
            </div>
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="row mb-1">
                        <label for="cantidad_fija" style="color: black;" class="col-9 col-form-label">Cantidad fija que representa el 100 %</label>
                        <input type="text" style="color: black;" class="col-3 form-control text-right" id="cantidad_fija" readonly value="100,00">
                    </div>
                    <div class="row">
                        <img src="images/signo_division.png"class="col-2" height="40" alt="image">
                        <label for="cantidad_productos" style="color: black;" class="col-7 col-form-label">Cantidad de Productos pertenecientes a esta Categoría</label>
                        <input type="text" style="color: black;background-color:white;" readonly class="col-3 form-control text-right" id="cantidad_productos" >
                    </div>
                    <hr style="height:1px;border:none;color:#333;background-color:#333;" />
                    <div class="mb-1 row">
                        <img src="images/signo_igual.png" class="col-2" height="40" alt="image">
                        <label for="mix_ideal" style="color: black;" class="col-7 col-form-label">Mix Ideal</label>
                        <input type="text" style="color: black;" readonly class="col-3 form-control text-right" id="mix_ideal" >
                    </div>
                    <div class="mb-1 row">
                        <img src="images/signo_multiplicacion.png"class="col-2" height="40" alt="image">
                        <label for="setenta" style="color: black;" class="col-7 col-form-label">Tomamos como válido un 70 %</label>
                        <input type="text" style="color: black;background-color:white;" readonly class="col-3 form-control text-right" id="setenta" value="70 %">
                    </div>
                    <hr style="height:1px;border:none;color:#333;background-color:#333;" />
                    <div class="mb-2 row">
                        <img src="images/signo_igual.png" class="col-2" height="40" alt="image">
                        <label for="mix_ideal_corregido" style="color: black;" class="col-7 col-form-label"><b>Mix Ideal Corregido o Popularidad Promedio</b></label>
                        <input type="text" style="color: black;" readonly class="col-3 form-control text-right" id="mix_ideal_corregido" >
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"></i>Volver</button>
            </div>
        </div>
    </div>
</div>
