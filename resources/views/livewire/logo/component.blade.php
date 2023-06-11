<div>
    <a class="navbar-brand" href="{{ url('home') }}">
        <img src="images/logo_floki_rojo.png" height="30" alt="image">
    <!-- <img src="images/logo/{{$logo}}" height="30"> -->
    <span class="navbar-brand-name ml-2" style="color:grey"> - {{$nombre}}</span></a>
    
    <div wire:offline>
        <button onclick="sinconexion()">Sin conexión</button>
    </div>
</div> 

<script type="text/javascript">	
function sinconexion()
{
    alert('Sin conexión a Internet...')
}
</script>