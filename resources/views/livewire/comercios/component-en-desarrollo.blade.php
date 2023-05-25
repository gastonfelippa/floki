<div class="row layout-top-spacing justify-content-center"> 
    <div class="col-sm-12 col-md-6 layout-spacing"> 
        <div class="widget-content-area ">
            <div class="widget-one">  
                <div class="row">
                    <div class="col-8 text-center">
                        <h3><b>Vista en desarrollo...</b></h3>
                    </div>
                    <div class="col-4 text-center">
                        <button type="button" onclick="salir()" class="btn btn-dark mr-1">
                            <i class="mbri-left"></i> Volver
                        </button>
                    </div> 
                </div>       
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function salir()
    {
        window.location.href="{{ url('notify') }}";
    }
</script>