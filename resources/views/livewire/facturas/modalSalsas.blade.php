<div class="modal fade" id="modalSalsas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Salsas y Guarniciones</h5>
            <button class="btn btn-dark" data-dismiss="modal">Cancelar</button>
            <button class="btn btn-primary" type="button" onclick="StoreOrUpdate()">Guardar</button>
        </div>       
        <div class="modal-body">
            <div class="widget-content-area">
                <div class="widget-one">
                    <!-- <input id="cantidad" type="number" onchange="grabarCantidad()" value="{{$cantidad}}" style="width: 50px;" class="text-center"> -->
                    <input id="cantidad" type="number" value="{{$cantidad}}" style="width: 50px;" class="text-center">
                    <input class="col-12 mt-2" id="texto_comanda" disabled>
                    <input class="col-12 mt-2" onchange="agregarComentario()" id="texto_comentario" placeholder="Ingresa un breve comentario...">
                    <hr>
                    <div id="divSalsas" class="col-sm-12 ">
                        <div class="widget-one scrollc"> 
                            <div class="scrollContentC"> 
                                @if($salsas != null)
                                @foreach($salsas as $a)                    
                                    <button style="width: 30%;" onclick="crear_descripcion('{{$a->descripcion}}', 'salsa')" type="button" class="btn btn-warning mb-1">{{$a->descripcion}}</button>
                                @endforeach 
                                @endif                   
                            </div>
                        </div>
                    </div>
                    <div id="divGuarniciones" class="col-sm-12">
                        <div class="widget-one scrollc"> 
                            <div class="scrollContentC"> 
                                @if($guarniciones != null)
                                @foreach($guarniciones as $a)                    
                                    <button style="width: 30%;" onclick="crear_descripcion('{{$a->descripcion}}', 'guarnicion')" type="button" class="btn btn-success mb-1">{{$a->descripcion}}</button>
                                @endforeach 
                                @endif                   
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
     
        </div>
    </div>
</div>
</div>
