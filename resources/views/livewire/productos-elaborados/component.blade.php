<div class="row layout-top-spacing justify-content-center"> 
    <div class="col-sm-12 col-md-6 layout-spacing">             
        <div class="widget-content-area">
            <div class="widget-one">
                @include('common.alerts')   
                @include('common.messages') <!-- validación de campos -->
                <div class="row">
                    <div class="col-xl-12 text-center">
                        <h3><b>Productos con receta</b></h3>
                    </div> 
                </div> 
                @include('common.inputBuscarBtnNuevo', ['create' => 'Productos_create']) 
                <div class="table-responsive scroll">
                    <table class="table table-hover table-checkable table-sm">
                        <thead>
                            <tr>                      
                                <th class="">DESCRIPCIÓN</th>
                                <th class="text-center">STOCK</th>
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($info as $r)
                            <tr>                     
                                <td>{{$r->descripcion}}</td>
                                @if (intval($r->stock) == floatval($r->stock)) {{--  si no hay decimales --}}
                                    <td class="text-center">{{number_format($r->stock,0)}} {{$r->unidad_de_medida}}</td>
                                @else
                                    @if ($r->unidad_de_medida == 'Un')
                                        <td class="text-center">{{number_format($r->stock,1,',','.')}} {{$r->unidad_de_medida}}</td>
                                    @else
                                        <td class="text-center">{{number_format($r->stock,3,',','.')}} {{$r->unidad_de_medida}}</td>
                                    @endif
                                @endif
                                <td class="text-center">
                                    @if ($r->edit == 1)                                        
                                    <ul class="table-controls">
                                        <li>
                                            <a href="javascript:void(0);" 
                                            onclick="openModal({{$r->id}}, '{{$r->descripcion}}', '{{$r->stock}}')"
                                            data-toggle="tooltip" data-placement="top" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                                        </li>   
                                    </ul>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
               
            </div>
        </div>
    </div>
    @include('livewire.productos-elaborados.modal_productos_elaborados')	
</div>

{{-- <style type="text/css" scoped>
.scroll{
    position: relative;
    height: 270px;
    margin-top: .5rem;
    overflow: auto;
}
thead tr th {     /* fija la cabecera de la tabla */
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #ffffff;
}
</style> --}}

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script type="text/javascript">    
    function openModal(id, producto, stock)
    { 
        $('#productoId').val(id);    
        $('.modal-title').text('Editar Stock de ' + producto);
        $('#stock_actual').val(stock);
        $('#stock_nuevo').trigger("focus");
        $('#stock_nuevo').val('');
        $('#modalProductosElaborados').modal('show');  
	}
	function actualizar()
    {
        var id = $('#productoId').val();
        var stock_nuevo = $('#stock_nuevo').val();

        let me = this
        swal({
            title: 'CONFIRMAR',
            text: '¿Deseas modificar el Stock?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            closeOnConfirm: false
            },
            function() {
                window.livewire.emit('actualizar', id, stock_nuevo);  
                swal.close()   
            });
        $('#modalProductosElaborados').modal('hide');
        
    }
    function foco(idElemento){
        document.getElementById(idElemento).focus();
    }

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
    }, 120000 );
    function pingServer() {
        $.ajax('/keepAlive');
    }
    /////
    window.onload = function() {
        document.getElementById("stock_nuevo").focus();
        Livewire.on('stock_no_disponible_sin_opcion',(stock, producto)=>{
            var texto = 'Solo restan ';
            var unidades = ' unidades';
            if(stock == 0 || stock === null){
                texto = 'Restan ';
                stock = '0';
            }else if(stock == 1){
                texto = 'Solo resta ';
                unidades = ' unidad';  
            }
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Stock no disponible',
                html: texto + stock + unidades + ' de ' + producto + '.<br><br>' +
                'Para continuar con su carga deberá modificar su STOCK dirigiéndose a la pestaña <br> ABM ->> PRODUCTOS...',
                showConfirmButton: true
            })
        })
        Livewire.on('stock_no_disponible_con_opcion',(stock, producto, id)=>{
            var texto = 'Solo restan ';
            var unidades = ' unidades';
            if(stock == 0 || stock == null){
                texto = 'Restan ';
                stock = '0';
            }else if(stock == 1){
                texto = 'Solo resta ';
                unidades = ' unidad';  
            }else if(stock < 1){
                texto = 'Tienes stock negativo';
                stock = '';
                unidades = '';  
            } 
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Stock no disponible',
                text: texto + stock + unidades + ' de ' + producto,
                showDenyButton: true,
                confirmButtonText: `Permitir cargar sin stock`,
                denyButtonText: `Anular carga`,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('permitirCargaSinStock', 'si', id);
                } else if (result.isDenied) {
                    window.livewire.emit('permitirCargaSinStock', 'no', id);
                }
            })
        })
        Livewire.on('stock_receta_no_disponible_sin_opcion',(stock, id)=>{
            texto = '';
            for (var clave in stock) {
                texto = texto + 'Restan ' + stock[clave].stock + stock[clave].unidadDeMedida + ' de ' + stock[clave].descripcion + '<br>'
            };
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Stock no disponible',
                html: texto +
                'Para continuar con su carga deberá modificar su STOCK dirigiéndose a la pestaña <br> ABM ->> PRODUCTOS...',
                showConfirmButton: true
            })
        })
        Livewire.on('stock_receta_no_disponible_con_opcion',(stock, id)=>{
            texto = '';
            for (var clave in stock) {
                texto = texto + 'Restan ' + stock[clave].stock + stock[clave].unidadDeMedida + ' de ' + stock[clave].descripcion + '<br>'
            };
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Stock de Materia Prima no disponible',
                html: texto,
                showDenyButton: true,
                confirmButtonText: `Permitir cargar sin stock`,
                denyButtonText: `Anular carga`,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('permitirCargaSinStock', 'si', id);
                } else if (result.isDenied) {
                    window.livewire.emit('permitirCargaSinStock', 'no', id);
                }
            })
        })
        Livewire.on('receta_sin_detalle',(producto)=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Debe agregar un detalle a la receta de '+ producto + ' para poder descontar su stock.',
                text: 'O simplemente indicar que no tiene receta, o que no se controla stock para este producto desde la pestaña ABM ->> PRODUCTOS...',
                showConfirmButton: true
            })
		})
        Livewire.on('receta_sin_principal',(producto)=>{
            Swal.fire({
                position: 'center',
                icon: 'info',
                title: 'Debe designar algún componente de la receta de '+ producto + ' como "principal" para poder descontar su stock.',
                text: 'O simplemente indicar que este producto no tiene receta, o que no se controla stock para el mismo desde la pestaña ABM ->> PRODUCTOS...',
                showConfirmButton: true
            })
		}) 
    }
</script>
