<div>
    @include('livewire.mesas.modal');
</div>

<script type="text/javascript">
    window.onkeydown=PulsarTecla;
    function PulsarTecla(event)
    {
        tecla = event.keyCode;
        if(e.ctrlKey == 1 && tecla==13) window.location.href="{{ url('facturas') }}";
        else if(tecla == 27) document.getElementById("btnCancelar").click();
    }
    function openModalMesa()
    {
        $('#modalMesa').modal('show')
        $('#modalMesa').on('shown.bs.modal', function() {
            $('#mesa').focus();
        });
	}
    function abrirMesa()
    {
        var data = JSON.stringify({
            'mesa_id'   : $('#mesa').val(),
            'mozo_id'   : $('#mozo').val(),
        });
        $('#modalMesa').modal('hide')
        window.livewire.emit('abrirMesa', data)
    }
    function salir()
    {
        window.location.href="{{ url('home') }}";
    }
    window.onload = function() {
        openModalMesa();
    }
</script>