<div class="row layout-top-spacing justify-content-center"> 
    <div class="col-sm-12 col-md-4 layout-spacing">
        <div class="widget-content-area">
            <div class="widget-one">	
                @include('common.messages')	
                @include('common.alerts')	
                <h5><b> Generar Débito (automático)</b></h5>
                <hr>
                <div class="row mt-3">  
                    <div class="col-6 layout-spacing">
                        <select id="mes" wire:model="mes" class="form-control">
                            <option value="Elegir">Mes</option>
                            <option value="1">ENERO</option>
                            <option value="2">FEBRERO</option>
                            <option value="3">MARZO</option>
                            <option value="4">ABRIL</option>
                            <option value="5">MAYO</option>
                            <option value="6">JUNIO</option>
                            <option value="7">JULIO</option>
                            <option value="8">AGOSTO</option>
                            <option value="9">SETIEMBRE</option>
                            <option value="10">OCTUBRE</option>
                            <option value="11">NOVIEMBRE</option>
                            <option value="12">DICIEMBRE</option>
                        </select>		               
                    </div>            
                    <div class="col-6 layout-spacing">
                        <select id="año" wire:model="año" class="form-control">
                            <option value="Elegir">Año</option>
                            <option value="2022">2022</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                            <option value="2028">2028</option>
                            <option value="2029">2029</option>
                            <option value="2030">2030</option>
                            <option value="2031">2031</option>
                            <option value="2032">2032</option>
                            <option value="2033">2033</option>
                            <option value="2034">2034</option>
                            <option value="2035">2035</option>
                            <option value="2036">2036</option>
                            <option value="2037">2037</option>
                            <option value="2038">2038</option>
                            <option value="2039">2039</option>
                            <option value="2040">2040</option>
                        </select>               
                    </div> 
                </div>              
                <div class="row">  
                    <div class="form-group col-12 col-sm-6">
                        <button type="button" onclick="Confirm()" class="btn btn-danger btn-block">
                            GENERAR
                        </button>
                    </div>                             
                    <div class="col-6 layout-spacing mt-1">
                        <button type="button" onclick="salir()" class="btn btn-primary btn-block">
                            CANCELAR
                        </button>
                    </div>
                </div>   
            </div>
        </div>	
    </div>
</div>

<script type="text/javascript">
 	function Confirm()
    {
        var periodo = $('#mes').val() + '-' + $('#año').val()
        let me = this
        swal({
            title: '¿Deseas generar un nuevo débito para el período ' + periodo + '?',
            text: 'No podrás deshacer esta acción ...',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false
            },
            function() {
                window.livewire.emit('guardar')    
                swal.close()   
            })
    }
    function salir()
    {
        window.location.href="{{ url('home') }}";
    }
    window.onload = function() {
        Livewire.on('debitoGeneradoExistente',()=>{
            Swal.fire({
                position: 'center',
                icon: 'warning',
                iconColor: 'orange',
                title: 'El Débito que deseas generar ya existe!!',
                text: 'Verifica si el mes y año son los que corresponden...',
                showConfirmButton: true
            })
		})
        Livewire.on('generarPdfDebitos',()=>{
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Los Débitos se crearon correctamente!!',
                text: 'Verifica la descarga del Pdf para que puedas imprimir los cupones correspondientes...',
                showConfirmButton: true
            }),
            window.open("{{url('pdfCuotaSocio')}}","_blank");
        })
    }
</script>