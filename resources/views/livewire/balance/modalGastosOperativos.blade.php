<div class="modal fade" id="modalGastosOperativos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Gastos Operativos</h5>
            </div>
       
            <div class="modal-body">
                <div class="widget-content-area">
                    <p class="col-4 offset-8" style="text-align: right; padding-right:0px;"><strong>% / Ventas</strong></p>
                    <?php $contador = 0 ?>
                    @foreach ($gastos_operativos as $i) 
                        <div class="row mb-1">
                            <?php if ( $contador > 0 ): ?>
                                <img src="images/signo_mas.png"class="col-2" height="40" alt="image">
                                <label for="existencia_inicial" style="color: black;" class="col-4 col-form-label">{{$i->descripcion}}
                                    <i class="bi bi-info-circle ml-2 asterisco"
                                    data-toggle="tooltip" data-placement="top"
                                    title="Ver detalles" onclick="ver_detalle_gastos_op({{$i->id}},'{{$i->descripcion}}')"></i></label>
                            <?php else: ?>
                                <label for="existencia_inicial" style="color: black;" class="col-4 offset-2 col-form-label">{{$i->descripcion}}
                                <i class="bi bi-info-circle ml-2 asterisco"
                                data-toggle="tooltip" data-placement="top"
                                title="Ver detalles" onclick="ver_detalle_gastos_op({{$i->id}},'{{$i->descripcion}}')"></i></label>                                
                            <?php endif; ?>
                            <?php $contador ++?>

                            <input type="text" style="color: black;" class="col-3 form-control text-right" id="existencia_inicial" readonly value={{number_format($i->suma_importe,2,',','.')}}>
                            <font size=2 color="Olive" face="Comic Sans MS,arial" class="col-3 alinear">  {{number_format($i->porcentaje,2,',','.')}} %</font>
                        </div>
                    @endforeach
                    <hr style="height:1px;border:none;color:#333;background-color:#333;" />
                    <div class="row mb-1">
                        <img src="images/signo_igual.png"class="col-2" height="40" alt="image">
                        <label for="total_compras" style="color: black;" class="col-6 col-form-label">TOTAL GASTOS OPERATIVOS</label>
                        <input type="text" style="color: black;" class="col-4 form-control text-right" id="total_compras" readonly value={{number_format($suma_gastos_operativos,2,',','.')}}>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"></i>Volver</button>
            </div>
        </div>
    </div>
</div>