<div class="col-sm-12 col-md-8 layout-spacing">      
    @include('common.alerts')
    <div class="widget-content-area">
        <div class="widget-one">
            <div class="row">
                <div class="col-7 text-left">
                    <h3><b>Punto de Equilibrio</b></h3>
                </div>
                <div class="col-5 text-right">
					{{-- <button type="button" class="btn btn-warning" 
						@if($habilitar_botones) enabled @else disabled @endif
						onclick= "doAction(4,0)">Ver detalles...
					</button> --}}
					<button type="button" class="btn btn-secondary"
						onclick= "ayudaPE()"><i class="bi bi-question-square" title="Ayuda..."></i>
					</button>
					<button type="button" class="btn btn-dark"
						onclick= "doAction(1,0)">Volver
					</button>
				</div> 	
            </div>	
            <div class="row">    
                <div class="widget-content-area">
                    <div class="row">
                        <p class="col-sm-5 col-md-6"></p>
                        <label style="color: black;" class="col-sm-3 col-md-3 text-right">ESTIMADO</label>
                        <label style="color: black;" class="col-sm-3 col-md-3 text-left">REAL</label>
                    </div>
                    <div class="row mb-1">
                        <p class="col-sm-2 col-md-1"></p>
                        <label for="gasto_fijo_estimado" style="color: black;" class="col-sm-3 col-md-6 col-form-label">Costos Fijos
                        	<i class="bi bi-plus-circle ml-2 asterisco"
                            data-toggle="tooltip" data-placement="top"
                            title="Agregar Costos Fijos" onclick="ver_costos_fijos_estimados()"></i></label>
                        <input type="text" style="color: black;" class="col-sm-3 col-md-2 form-control text-right mr-1" id="gasto_fijo_estimado" readonly value={{number_format($total_cf_estimado,2,',','.')}}>
                        <input type="text" style="color: black;" class="col-sm-3 col-md-2 form-control text-right" id="gasto_fijo_real" readonly value={{number_format($cFijos,2,',','.')}}>
                    </div>
                    <div class="row mb-1">
                        <img src="images/signo_mas.png"class="col-sm-2 col-md-1" height="40" alt="image">
                        <label for="deudas_estimadas" style="color: black;" class="col-sm-3 col-md-6 col-form-label">Deudas</label>
                        <input type="text" style="color: black;background-color:white;" class="col-sm-3 col-md-2 form-control text-right mr-1" 
                            id="deudas_estimadas" onblur="calcular_total_a_cubrir_estimado()">
                        <input type="text" style="color: black;background-color:white;" class="col-sm-3 col-md-2 form-control text-right" 
                            id="deudas_real" onblur="calcular_total_a_cubrir()">
                    </div>
                    <div class="row">
                        <img src="images/signo_mas.png"class="col-sm-2 col-md-1" height="40" alt="image">
                        <label for="ganancia_deseada_estimada" style="color: black;" class="col-sm-3 col-md-6 col-form-label">Ganancia Deseada</label>
                        <input type="text" style="color: black;background-color:white;" class="col-sm-3 col-md-2 form-control text-right mr-1"
                            id="ganancia_deseada_estimada" onblur="calcular_total_a_cubrir_estimado()">
                        <input type="text" style="color: black;background-color:white;" class="col-sm-3 col-md-2 form-control text-right"
                            id="ganancia_deseada_real" onblur="calcular_total_a_cubrir()">
                    </div>
                    <hr style="height:1px;border:none;color:#333;background-color:#333;" />
                    <div class="mb-1 row">
                        <img src="images/signo_igual.png" class="col-sm-2 col-md-1" height="40" alt="image">
                        <label for="total_a_cubrir_estimado" style="color: black;" class="col-sm-3 col-md-6 col-form-label">Total a Cubrir</label>
                        <input type="text" style="color: black;" readonly class="col-sm-3 col-md-2 form-control text-right mr-1" id="total_a_cubrir_estimado" value={{number_format($total_a_cubrir_estimado,2,',','.')}}>
                        <input type="text" style="color: black;" readonly class="col-sm-3 col-md-2 form-control text-right" id="total_a_cubrir_real" value={{number_format($total_a_cubrir,2,',','.')}}>
                    </div>
                    <div class="mb-1 row">
                        <img src="images/signo_division.png"class="col-sm-2 col-md-1" height="40" alt="image">
                        <label for="mcpp" style="color: black;" class="col-sm-3 col-md-6 col-form-label">Margen de Contribución Promedio Ponderado</label>
                        <input type="text" style="color: black;background-color:white;" readonly class="col-sm-3 col-md-2 form-control text-right mr-1" id="mcpp" value={{number_format($promedio_margen_deseado_1,2,',','.')}}%>
                        <input type="text" style="color: black;background-color:white;" readonly class="col-sm-3 col-md-2 form-control text-right" id="mcpp2" value={{number_format($p_m_c,2,',','.')}}%>
                    </div>
                    <hr style="height:1px;border:none;color:#333;background-color:#333;" />
                    <div class="mb-2 row">
                        <img src="images/signo_igual.png" class="col-sm-2 col-md-1" height="40" alt="image">
                        <label for="punto_de_equilibrio" style="color: black;" class="col-sm-3 col-md-6 col-form-label"><b>Punto de Equilibrio a Nivel de Ventas en el que Deseo Alcanzar Ganancias</b></label>
                        <input type="text" style="color: black;" readonly class="col-sm-3 col-md-2 form-control text-right mr-1" id="punto_de_equilibrio" value={{number_format($punto_de_equilibrio_estimado,2,',','.')}}>
                        <input type="text" style="color: black;" readonly class="col-sm-3 col-md-2 form-control text-right" id="punto_de_equilibrio2" value={{number_format($punto_de_equilibrio,2,',','.')}}>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
