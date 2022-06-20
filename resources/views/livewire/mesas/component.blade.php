<div class="row layout-top-spacing justify-content-center"> 
<div class="col-sm-4 layout-spacing">    
    <div class="widget-content-area">
        <div class="widget-one">
            @include('common.alerts')
            @include('common.messages')
            <h5><b>Abrir mesa</b></h5>
            <div class="row">                               
                <div class="col-5 layout-spacing">
                    <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-basket" viewBox="0 0 16 16"><path d="M5.757 1.071a.5.5 0 0 1 .172.686L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1v4.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 13.5V9a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h1.217L5.07 1.243a.5.5 0 0 1 .686-.172zM2 9v4.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V9H2zM1 7v1h14V7H1zm3 3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 4 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 6 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 8 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5z"/></svg></span>
                            </div>
                        <input id="mesa" name="mesa" onblur="abrirMesa()" type="text" class="form-control text-capitalize" placeholder="N°" autofocus autocomplete="off" maxlength="3">
                    </div>
                </div>
            </div>
            <div class="row">                               
                <div class="col-12 layout-spacing">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16"><path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z"/></svg></span>
                            </div>
                            <select id="mozo" class="form-control">
                                <option value="Elegir">Mozo</option>
                                @foreach($mozos as $m)
                                <option value="{{ $m->id }}">
                                    {{$m->apellido}} {{$m->name}}
                                </option>                                       
                                @endforeach 
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row ">
                <div class="col-12">
                    <button type="button" id="btnCancelar" onclick="salir()" class="btn btn-dark mr-1">
                        <span style="text-decoration: underline;">C</span>ancelar
                    </button>
                    <button type="button" id="btnGuardar" onclick="abrirMesaNueva()" class="btn btn-primary">
                        <span style="text-decoration: underline;">G</span>uardar
                    </button>       
                </div>
            </div>
        </div>
    </div>	
</div>
</div>

<script type="text/javascript">
    /////código para prolongar la session
    var keep_alive = false;
    $(document).bind("click keydown keyup mousemove", function() {
        keep_alive = true;
    });
    setInterval(function() {
        if ( keep_alive ) {
            pingServer();
            keep_alive = false;
        }
    }, 1200000 );
    function pingServer() {
        $.ajax('/keepAlive');
    }
    /////
 
    function abrirMesa()
    {
        var data = JSON.stringify({'mesa_desc' : $('#mesa').val()})
        window.livewire.emit('abrirMesa', data)
    }
    function abrirMesaNueva()
    {
        var data = JSON.stringify({
            'mesa_desc' : $('#mesa').val(),
            'mozo'      : $('#mozo').val()
        });
        window.livewire.emit('abrirMesaNueva',data)
    }
    function salir()
    {
        window.location.href="{{ url('notify') }}";
    }
    $(document).ready(function() {
        document.getElementById("mesa").focus();
    });
</script>