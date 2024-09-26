<div class="modal fade" id="modalStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Producto: {{$producto}}</h5>
            </div>       
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <div class="table-responsive scrollmodal">
                            <table class="table table-hover table-checkable table-sm mb-4">
                                <thead>
                                    <tr>
                                        <th class="text-left">CLIENTE</th>
                                        <th class="text-center">CANTIDAD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stock as $r)
                                    <tr>
                                        @if($r->cantidad > 0)
                                        <td class="text-left">{{$r->apellido}} {{$r->nombre}}</td>
                                        <td class="text-center">
                                            <ul class="table-controls">
                                                <li>{{number_format($r->cantidad,0)}}</li>
                                                <li>
                                                    <a href="javascript:void(0);"
                                                        wire:click="verHistorialStockEnConsignacion({{$r->id}},{{$r->clienteId}},{{$r->producto}})"  
                                                        data-toggle="tooltip" data-placement="top" title="Ver Historial">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>                                 
                                                </li> 
                                            </ul>
                                        </td>                               
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                   
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal"><i class="flaticon-cancel-12"></i>Volver</button>
            </div>
        </div>
    </div>
</div>