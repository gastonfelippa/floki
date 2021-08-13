<div>
    <div class="row">
        <div id="detalle" class="col-9 table-resposive"  style="display:block;"></div>
        <div id="comanda" class="col-3 table-resposive"  style="display:block;"></div>
    </div>
    <input type = "hidden" id="btnEnEspera"  value = "En Espera" onclick = "mostrarComanda(1,{{$infoEnEspera}},{{$infoDetEnEspera}})"></input>
    <input type = "hidden" id="btnProcesando"  value = "Procesando" onclick = "mostrarComanda(2,{{$infoProcesando}},{{$infoDetProcesando}})"></input>
    <input type = "hidden" id="btnTerminado"  value = "Terminado" onclick = "mostrarComanda(3,{{$infoTerminado}},{{$infoDetTerminado}})"></input>
</div>

<script type="text/javascript">
    function mostrarComanda(vista,dataInfo, dataInfoDetalle)
    {
        var divTabla = document.getElementById('detalle');
        $('.table').each(function(index, obj){
            divTabla.removeChild(obj);
        });   

        for (var l = 0; l < dataInfo.length; l++) {
            var tabla = document.createElement('table');
            tabla.setAttribute("class", "table table-hover");
            var tbody = document.createElement('tbody');
            var id    = dataInfo[l]['id'];
            for (var i = 0; i < dataInfoDetalle.length; i++) {
                var tr = document.createElement('tr');
                tr.style.backgroundColor = '#E9967A';
                if(dataInfoDetalle[i]['comanda_id'] === id){
                    var td  = document.createElement('td');
                    var td1 = document.createElement('td');
                    var td2 = document.createElement('td');

                    td.textContent   = parseInt(dataInfoDetalle[i]['cantidad']);
                    td.setAttribute("class",'text-right');
                    td1.textContent  = '   .-';
                    td2.textContent  = dataInfoDetalle[i]['descripcion'];
                    td2.setAttribute("style",'font-size: 35px; padding:0px');
                    td2.setAttribute("class",'text-left');

                    tr.appendChild(td);
                    tr.appendChild(td1);
                    tr.appendChild(td2);
                }
                tbody.appendChild(tr);
            }
            tabla.appendChild(tbody);
            divTabla.appendChild(tabla);
        }
        
    }

    var metaChar = false;
    var exampleKey = 16;

    function keyEvent(event) {
        var key = event.keyCode || event.which;
        var keychar = String.fromCharCode(key);
        if (key == exampleKey) {
            metaChar = true;
        }
        if (key != exampleKey) {
            if (metaChar) {
                metaChar = false;
            } else { 
                if(key == 97){
                    document.getElementById("btnEnEspera").click();
                }else if(key == 98){
                    document.getElementById("btnProcesando").click();
                }else if(key == 99){
                    document.getElementById("btnTerminado").click();
                } 
            }
        }
    }

    function metaKeyUp (event) {
        var key = event.keyCode || event.which;

        if (key == exampleKey) {
            metaChar = false;
        }
    }

    window.onload = function(){

        document.getElementById("btnEnviar").onmouseover = function() {
            this.value = "Vas a enviar el formulario";
        }
        document.getElementById("btnEnviar").onmouseout = function() {
            this.value = "Enviar el formulario";
        }
    }
</script>