@extends('layouts.template_con_sessions')


@section('content')
<div class="row layout-top-spacing justify-content-center">
    <div class="col-8 layout-spacing"> 
        <div class="widget-content-area br-4">
			<div class="widget-one">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <h4 class="mt-2 text-center col-sm-12 col-md-10 color"><b>ATENCIÓN!!!</b><br>NO SE PUEDE INICIAR UN NUEVO ARQUEO PORQUE YA EXISTE UNO CON LA MISMA FECHA Y ESTÁ CERRADO...</h4>
                        <h5 class="mt-2 text-center col-sm-12 col-md-10 color">Deberás esperar hasta el próximo horario de inicio de apertura del local.</h5>
                    </div>
                    <div class="row justify-content-center">
                        <button type="button" class="btn btn-primary" onclick="continuar()">
                            <svg color="white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            Continuar
                        </button> 
                    </div>
                </div> 
            </div> 
        </div> 
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