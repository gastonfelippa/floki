<div>
   
    <input type="text" id="action" value="{{$action}}"> 
    @if($action == 1)
    @include('livewire.mesas.modalMesa');
    @else
    @include('livewire.mesas.modalMozo');
    @endif
 
</div>

<script type="text/javascript">
    window.onkeydown=PulsarTecla;
    function PulsarTecla(event)
    {
        tecla = event.keyCode;
        if(e.ctrlKey == 1 && tecla==13) window.location.href="{{ url('facturas') }}";
        else if(tecla == 27) document.getElementById("btnCancelar").click();
    }
    function openModalMesa()
    {
        $('#modalMesa').modal('show')
        $('#modalMesa').on('shown.bs.modal', function() {$('#mesa').focus()})
	}
    function abrirMesa()
    {
        var data = JSON.stringify({'mesa_desc' : $('#mesa').val()})
        $('#modalMesa').modal('hide')
        window.livewire.emit('abrirMesa', data)
    }
    function openModalMozo()
    {
        $('#modalMozo').modal('show')
        $('#modalMozo').on('shown.bs.modal', function() {$('#mozo').focus()})
	}
    function asignarMozo()
    {
        var data = JSON.stringify({'mozo_id' : $('#mozo').val()})
        $('#modalMozo').modal('hide')
        window.livewire.emit('asignarMozo', data)
    }
    function salir()
    {
        window.location.href="{{ url('home') }}";
    }
    window.onload = function() {
        // if($('#action').val() == 1) openModalMesa();
        // else openModalMozo();
     
    }

</script>