@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <h3 class="mt-5 text-center col-sm-12 col-md-8 color"><b>ATENCIÓN!!!<br>PARECE QUE OLVIDARON HACER EL ÚLTIMO ARQUEO GENERAL...</b></h3>
        <h2 class="mt-2 text-center col-sm-12 col-md-8 color">Podés continuar, pero verás que hay algunas funciones deshabilitadas hasta que realices el Arqueo General que corresponda.<br></h2>
    </div>
    <div class="row justify-content-center">
        <button type="button" class="btn btn-primary" onclick="continuar()">
            <svg color="white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            Continuar
        </button> 
    </div>
</div> 
<style type="text/css" scoped>
    .color{
        color: black;
        background: white;
        border: 5px;
        padding: 10px;
    }
</style>

<script type="text/javascript">
    function continuar()
    {
        window.location.href="{{ url('notify') }}";
    }
</script>
@endsection