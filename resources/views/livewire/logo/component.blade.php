<div>
    <a class="navbar-brand" href="{{ url('home') }}">
        <!--   <img src="images/logo_floki_rojo.png" height="30" alt="image"> -->
        <img src="images/logo/{{ $logo }}" height="30">
        <span class="navbar-brand-name ml-2"> - {{ $nombre }}</span></a>
    <div wire:offline>
        <button class="btn btn-danger ml-4" onclick="sinconexion()"> Sin conexión a Internet...</button>
    </div>
</div>

<script type="text/javascript">
    function sinconexion() {
        alert('Sin conexión a Internet...')
    }
</script>
