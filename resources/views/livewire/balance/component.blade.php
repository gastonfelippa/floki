<div class="row layout-top-spacing justify-content-center"> 
<div class="col-sm-12 col-md-6 layout-spacing"> 
    <div class="widget-content-area ">
        <div class="widget-one">  
            <div class="row">
                <div class="col-8 text-center">
                    <h3><b>Balance</b></h3>
                </div>
                <div class="col-4 text-center">
                    <button type="button" wire:click="doAction(1)" onclick="setfocus('nombre')"  class="btn btn-dark mr-1">
                        <i class="mbri-left"></i> Volver
                    </button>
                </div> 
            </div>
            <hr/>
            <div class="row">
                <div class="col-sm-8 col-md-7 text-left">
                    <b>Existencia Inicial:</b>
                    <br>
                    <b>Compras de Mercadería:</b>
                    <br>
                    <b>Existencia Final:</b>
                    <br>
                    <font size=4 color="Olive" face="Comic Sans MS,arial">Costo de la Mercadería Vendida:</font>
                </div>
                <div class="col-sm-2 col-md-3 text-right">
                    <b>{{number_format($e_i,2,',','.')}}</b> 
                    <br>
                    <b>{{number_format($compras,2,',','.')}}</b> 
                    <br>
                    <b>({{number_format($e_f,2,',','.')}})</b> 
                    <br>
                    <font size=4 color="Olive" face="Comic Sans MS,arial">{{number_format($cmv,2,',','.')}}</font>
                </div>                     
            </div>                     
            <hr/>
            <div class="row">    
                <div class="col-sm-8 col-md-7 text-left">
                    <b>Ingresos del Mes:</b>
                    <br>
                    <b>Costo de la Mercadería Vendida:</b>
                    <br>
                    <b>Empleados:</b>
                    <br>
                    <b>Servicios:</b>
                    <br>
                    <b>Impuestos:</b>
                    <br>
                    <b>Gastos de Funcionamiento:</b>
                    <br>
                    <b>Egresos Varios:</b>
                    <br>
                    <font size=4 color="Red" face="Comic Sans MS,arial">Resultado:</font>                
                </div>
                <div class="col-sm-2 col-md-3 text-right">
                    <b>{{number_format($ventas,2,',','.')}}</b>
                    <br>
                    <b>({{number_format($cmv,2,',','.')}})</b> 
                    <br>
                    <b>(86.373,00)</b> 
                    <br>
                    <b>(16.470,00)</b>
                    <br>
                    <b>(18.411,00)</b> 
                    <br>
                    <b>(8.538,00)</b>
                    <br>
                    <b>(10,00)</b>
                    <br>
                    <font size=4 color="Red" face="Comic Sans MS,arial">75.619,00</font>
                </div>
                <div class="col-sm-2 col-md-2 text-right">
                    <div id="espacio"></div>
                    <font size=2 color="Olive" face="Comic Sans MS,arial">  {{number_format($p_cmv,2,',','.')}} %</font>
                    <br>
                    <font size=2 color="Olive" face="Comic Sans MS,arial">  23,91 %</font>
                    <br>
                    <font size=2 color="Olive" face="Comic Sans MS,arial">  4,56 %</font>
                    <br> 
                    <font size=2 color="Olive" face="Comic Sans MS,arial">  5,10 %</font>
                    <br>
                    <font size=2 color="Olive" face="Comic Sans MS,arial">  2,36 %</font> 
                    <br>
                    <font size=2 color="Olive" face="Comic Sans MS,arial">  0,00 %</font>
                    <br> 
                    <font size=2 color="Red" face="Comic Sans MS,arial">  20,93 %</font>
                    <br> 
                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-sm-8 col-md-7 text-left">
                    <b>Ventas:</b>
                    <br>
                    <b>Costos Fijos:</b>
                    <br>
                    <b>Costos Variables:</b>
                    <br>
                    <font size=4 color="Red" face="Comic Sans MS,arial">Ventas en el Punto de Equilibrio:</font>
                </div>
                <div class="col-sm-2 col-md-3 text-right">
                    <b>{{number_format($ventas,2,',','.')}}</b> 
                    <br>
                    <b>113.965,00</b> 
                    <br>
                    <b>166.674,00</b> 
                    <br>
                    <font size=4 color="Red" face="Comic Sans MS,arial">211.583,52</font>
                </div>
                <div class="col-sm-2 col-md-2 text-right">
                    <div id="espacio_p_eq"></div>
                    <font size=2 color="Olive" face="Comic Sans MS,arial">  31,55 %</font>
                    <br>
                    <font size=2 color="Olive" face="Comic Sans MS,arial">  46,14 %</font>
                </div>                    
            </div>
            <hr/>
            <div class="row">                    
                <div class="col-sm-8 col-md-7 text-left">
                    <b>Ingresos del Mes:</b>
                    <br>
                    <b>Costo de la Mercadería Vendida:</b>
                    <br>
                    <font size=4 color="Olive" face="Comic Sans MS,arial">Margen de Contribución:</font>
                    <br>
                    <b>Gastos Operativos:</b>
                    <br>
                    <font size=4 color="Red" face="Comic Sans MS,arial">Utilidad Operativa:</font>
                    <br>
                </div>
                <div class="col-sm-2 col-md-3 text-right">
                    <b>{{number_format($ventas,2,',','.')}}</b> 
                    <br>
                    <b>({{number_format($cmv,2,',','.')}})</b> 
                    <br>
                    <font size=3 color="Olive" face="Comic Sans MS,arial">{{number_format($m_c,2,',','.')}}</font> 
                    <br>
                    <b>(129.802,00)</b> 
                    <br>
                    <font size=4 color="Red" face="Comic Sans MS,arial">75.619,00</font> 
                </div>
                <div class="col-sm-2 col-md-2 text-right">
                    <div id="espacio_utilidad"></div>
                    <font size=2 color="Olive" face="Comic Sans MS,arial"> {{number_format($p_m_c,2,',','.')}} %</font>
                </div> 
            </div>
            <hr/>
            <!-- <div class="row ">
                <div class="col-12">
                    <button type="button" wire:click="doAction(1)" onclick="setfocus('nombre')"  class="btn btn-dark mr-1">
                        <i class="mbri-left"></i> Volver
                    </button>
                </div>
            </div> -->
        </div>
    </div>
</div>
</div>


<script type="text/javascript">
    document.getElementById("espacio").style.height = "22px";
    document.getElementById("espacio_p_eq").style.height = "20px";
    document.getElementById("espacio_utilidad").style.height = "43px";
    $(document).ready(function() {
        document.getElementById("nombre").focus();
    });
</script>


 