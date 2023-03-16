<div>
    @include('common.messages')
    <div class="row">
        <div id="detalle" class="col-9 table-resposive"></div>
        <div id="comanda" class="col-3 table-resposive"></div>
    </div>

    <div class="m-0 vh-100 row justify-content-center align-items-center">
        <div class="col-auto text-center">
            <h1 id="mensaje"></h1>
        </div>
    </div>

    <input type="hidden" id="vista" wire:model="vista">
    <input type="hidden" id="posicion" wire:model="posicion">
    <input type="hidden" id="sonido" wire:model="sonido">

    <input type="hidden" id="comSelEnEspera" wire:model="comSelEnEspera">
    <input type="hidden" id="comSelProcesando" wire:model="comSelProcesando">
    <input type="hidden" id="comSelTerminado" wire:model="comSelTerminado">

    <input type="hidden" id="btnEnEspera" value = "En Espera" onclick = "mostrarComanda(1,{{$infoEnEspera}},{{$infoDetEnEspera}})">
    <input type="hidden" id="btnProcesando" value = "Procesando" onclick = "mostrarComanda(2,{{$infoProcesando}},{{$infoDetProcesando}})">
    <input type="hidden" id="btnTerminado" value = "Terminado" onclick = "mostrarComanda(3,{{$infoTerminado}},{{$infoDetTerminado}})">
</div>

<script type="text/javascript">
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
                if(key == 97){         //número 1
                    document.getElementById("btnEnEspera").click();
                    //window.location="{{ url('comandas') }}"
                }else if(key == 98){   //número 2
                    document.getElementById("btnProcesando").click();
                }else if(key == 99){   //número 3
                    document.getElementById("btnTerminado").click();
                }else if(key == 37){ //flecha izquierda, vuelve un estado                 
                    cambiarEstado('atras');
                }else if(key == 39){ //flecha derecha, pasa al estado siguiente
                    cambiarEstado('adelante');
                }else if(key == 38){ //flecha arriba, sube a la comanda siguiente     
                    seleccionarComanda('arriba');
                }else if(key == 40){ //flecha abajo, baja a la comanda siguiente
                    seleccionarComanda('abajo');
                }else if(key == 27){ //escape ->salir
                    window.location.href="{{ url('notify') }}";
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

    function mostrarComanda(vista,dataInfo, dataInfoDetalle)
    {
        if(vista == '1' & dataInfo == ''){
            document.getElementById('mensaje').innerHTML='Esperando Comandas...';
        }else if(vista == '2' & dataInfo == ''){
            document.getElementById('mensaje').innerHTML='Sin comandas para procesar,<br> presione la tecla 1';
        }else if(vista == '3' & dataInfo == ''){
            document.getElementById('mensaje').innerHTML='Ventana 3';
        }else{
            document.getElementById('mensaje').innerHTML='';
        }
        $('#vista').val(vista);
        var divTabla   = document.getElementById('detalle');
        var divComanda = document.getElementById('comanda');
   
        $('.table').each(function(index, obj){
            divTabla.removeChild(obj);
        });   
        $('.div').each(function(index, obj){
            divComanda.removeChild(obj);
        }); 
        var comSeleccionada = null;
        if(vista == '1') comSeleccionada = $('#comSelEnEspera').val();  
        else if(vista == '2') comSeleccionada = $('#comSelProcesando').val();  
        else if(vista == '3') comSeleccionada = $('#comSelTerminado').val();  

        if(dataInfo.length > 0){
            for (var l = 0; l < dataInfo.length; l++) {
                ///inicio creación tabla detalle
                var tabla = document.createElement('table');
                tabla.setAttribute("class", "table");
                var tbody = document.createElement('tbody');
                var id    = dataInfo[l]['id'];
                var contadorFilas = 0;
                for (var i = 0; i < dataInfoDetalle.length; i++) {
                    var tr = document.createElement('tr');
                    if(vista == '1' && comSeleccionada == dataInfo[l]['id']) tr.style.backgroundColor = '#DC7633';
                    else if(vista == '2' && comSeleccionada == dataInfo[l]['id']) tr.style.backgroundColor = '#F1C40F';
                    else if(vista == '3' && comSeleccionada == dataInfo[l]['id']) tr.style.backgroundColor = '#52BE80';
                    else tr.style.backgroundColor = '#B3B6B7';
    
                    if(dataInfoDetalle[i]['comanda_id'] == id){
                        var tdCant  = document.createElement('td');
                        var tdDesc = document.createElement('td');

                        tdCant.textContent   = parseInt(dataInfoDetalle[i]['cantidad']) + ' -';
                        tdCant.setAttribute("class",'text-center');
                        tdCant.setAttribute("style",'font-size: 35px; padding:0px; width: 7%;');
                        tdDesc.textContent  = dataInfoDetalle[i]['descripcion'];
                        tdDesc.setAttribute("style",'font-size: 35px; padding:0px;');
                        tdDesc.setAttribute("class",'text-left');

                        tr.appendChild(tdCant);
                        tr.appendChild(tdDesc);
                        contadorFilas ++;
                    }
                   

                    tbody.appendChild(tr);
                }
                tabla.appendChild(tbody);
                if(contadorFilas < 2){
                    var br  = document.createElement('br');
                    var br2 = document.createElement('br');
                    var br3 = document.createElement('br');
                    var br4 = document.createElement('br');
                    tabla.appendChild(br);
                    tabla.appendChild(br2);
                    // tabla.appendChild(br3);
                    // tabla.appendChild(br4);
                }else if(contadorFilas < 3){
                    // var br  = document.createElement('br');
                    // var br2 = document.createElement('br');
                    // tabla.appendChild(br);
                    // tabla.appendChild(br2);
                }  
                divTabla.appendChild(tabla);
                var coords = tabla.getBoundingClientRect();
                var posicion = coords.top;
                ////fin tabla detalle

                //creo un nuevo div dentro del divComanda y lo ubico en la posicion.top antes obtenida
                var div = document.createElement('div');
                div.setAttribute("id", "div");
                div.setAttribute("class", 'div');
                div.style.position = "absolute";
                div.style.top = posicion + 'px';

              //  var pId     = document.createElement('p');
                var pMesa   = document.createElement('p');
                var pMozo   = document.createElement('p');
                var pDemora = document.createElement('p');
             //   pId.innerHTML   = 'Id: ' + dataInfo[l]['id'];
                pMesa.innerHTML = 'Mesa: ' + dataInfo[l]['descripcion'];
                pMozo.innerHTML = 'Mozo: ' + dataInfo[l]['name'];
                pMesa.setAttribute("style",'font-size: 30px; height:17px; width:300px;');
                pMozo.setAttribute("style",'font-size: 30px; height:17px;');
                pDemora.setAttribute("style",'font-size: 20px; padding-top:5px; height:17px;');
                if(vista != 3) pDemora.innerHTML = 'Demora: ' + dataInfo[l]['demora'];
                else pDemora.innerHTML = 'Terminado hace ' + dataInfo[l]['demora'];
            
             //   div.appendChild(pId);
                div.appendChild(pMesa);
                div.appendChild(pMozo);
                div.appendChild(pDemora);
                divComanda.appendChild(div);
            } 
        } 
    }

    function cambiarEstado(movimiento){
        var vista = $('#vista').val();
        var comSeleccionada = null;
        if(vista == '1') comSeleccionada = $('#comSelEnEspera').val();  
        else if(vista == '2') comSeleccionada = $('#comSelProcesando').val();  
        else if(vista == '3') comSeleccionada = $('#comSelTerminado').val();  

        window.livewire.emit('cambiarEstado', comSeleccionada, vista, movimiento);
    }
    
    function seleccionarComanda(sentido){
        var vista = $('#vista').val();
        var comSeleccionada = null;
        if(vista == '1') comSeleccionada = $('#comSelEnEspera').val();  
        else if(vista == '2') comSeleccionada = $('#comSelProcesando').val();  
        else if(vista == '3') comSeleccionada = $('#comSelTerminado').val();  
    
        window.livewire.emit('seleccionarComanda', comSeleccionada, vista, sentido);
    }

    let sound = new Audio('./sonidos/bastara-los-cafres.mp3');
    function sonidos(estado)
    {
        if(estado == 1) sound.play();
        else{
            sound.pause();
            sound.currentTime = 0;
        } 
    }
 
    setInterval(function() {

        //window.location="{{ url('comandas') }}"
        if ($('#vista').val() == '1' && $('#sonido').val() == '1') {
            document.getElementById("btnEnEspera").click();
            sonidos(1);
            //window.livewire.emit('leerSinCargar')
        }else if ($('#vista').val() == '1' && $('#sonido').val() == '') {
            //window.livewire.emit('leerSinCargar')
            //document.getElementById("btnEnEspera").click();
            sonidos(0);
        }
    }, 5000);
    
    window.onload = function(){
        if($('#vista').val() == '1') document.getElementById("btnEnEspera").click();
        else if($('#vista').val() == '2') document.getElementById("btnProcesando").click();
        else if($('#vista').val() == '3') document.getElementById("btnTerminado").click();

        Livewire.on('selComanda', (enespera,procesando,terminado) => {
            $('#comSelEnEspera').val(enespera);
            $('#comSelProcesando').val(procesando);
            $('#comSelTerminado').val(terminado);
            if($('#vista').val() == '1'){
                document.getElementById("btnEnEspera").click();
            }else if($('#vista').val() == '2'){
                document.getElementById("btnProcesando").click();
            }else if($('#vista').val() == '3'){
                document.getElementById("btnTerminado").click();
            }
        })
        Livewire.on('leer', () => {
            alert('leyendo...')
        })
    }
</script>