<div class="row layout-top-spacing justify-content-center"> 
<div class="col-sm-4 layout-spacing">    
    <div class="widget-content-area">
        <div class="widget-one">
            @include('common.alerts')
            @include('common.messages')
            <div class="row mb-2">
    			<div class="col-xl-12 text-center">
                    <h3><b>Abrir mesa</b></h3>
                </div>
            </div>
            <div class="row">                               
                <div class="col-5 layout-spacing">
                    <div class="input-group ">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-table" viewBox="0 0 16 16"><path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm0 4h-4v3h4V8zm0 4h-4v3h3a1 1 0 0 0 1-1v-2zm-5 3v-3H6v3h4zm-5 0v-3H1v2a1 1 0 0 0 1 1h3zm-4-4h4V8H1v3zm0-4h4V4H1v3zm5-3v3h4V4H6zm4 4H6v3h4V8z"/></svg></span>
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
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/></svg></span>    
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
 
    function abrirMesa()
    {
        var data = JSON.stringify({'mesa_desc' : $('#mesa').val()})
        window.livewire.emit('abrirMesa', data, null)
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
    window.onload = function() {
        Livewire.on('mesa',(idMesa)=>{
            $('#mesa').val(idMesa);
            abrirMesa();
		})
    }
</script>