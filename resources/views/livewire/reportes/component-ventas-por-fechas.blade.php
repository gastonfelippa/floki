<div class="main-content">
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <div class="widget-content-area">

                    <div class="widget-one">
                        <h4 class="text-center mb-5">Reporte de Ventas por Fecha</h4>
                        <div class="row">
                            <div class="col-sm-12 col-md-2 col-lg-2">
                                Fecha inicial
                                <div class="form-group"> 
                                    <input wire:model.lazy="fecha_ini" type="text" class="form-control flatpickr flatpickr-input active" placeholder="Haz click">
                                </div>
                            </div> 
                        
                        <!-- <div class="row"> -->
                            <div class="col-sm-12 col-md-2 col-lg-2">
                                Fecha final
                                <div class="form-group"> 
                                    <input wire:model.lazy="fecha_fin" type="text" class="form-control flatpickr flatpickr-input active" placeholder="Haz click">
                                </div>
                            </div> 
                        <!-- </div> -->
                        <div class="col-sm-12 col-md-1 col-lg-1 text-left">
                            <button type="submit" class="btn btn-info mt-4 mobile-only">Ver</button>
                        </div>
                        <div class="col-sm-12 col-md-1 col-lg-1 text-left">
                            <button type="submit" class="btn btn-dark mt-4 mobile-only">Exportar</button>
                        </div>
                        <div class="col-sm-12 col-md-3 col-lg-3 offset-lg-3">
                            <b>Fecha de Consulta</b> {{\Carbon\Carbon::now()->format('d-m-Y')}}
                            <br>
                            <b>Cantidad Registros</b> {{$cantVentas->count()}}
                            <br>
                            <b>Total de Ingresos</b> $ {{number_format($sumaTotal,2)}}
                        </div>
                        </div>
                    </div>

                    <div class="row">
                    <div class="col-lg-1"></div>
                            <div class="table-responsive mt-3 table-sm col-lg-10">
                            <table class="table table-bordered table-hover table-striped table-checkable table-highlight mb-4">
                                <thead>
                                    <tr>
                                        <th class="text-center">FACT N°</th>
                                        <th class="text-center">FECHA</th>
                                        <th class="text-center">CLIENTE</th>
                                        <th class="text-center">IMPORTE</th>
                                        <th class="text-center">REPARTIDOR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($info as $r)
                                    <tr>
                                        <td class="text-center">{{$r->id}}</td>
                                        <td class="text-center">{{$r->created_at->format('d-m-Y')}}</td>
                                        <td class="text-left">{{$r->cliente}}</td>
                                        <td class="text-right">{{number_format($r->importe,2)}}</td>
                                        <td class="text-center">{{$r->repartidor}}</td>                                 
                                    </tr>
                                    @endforeach
                                </tbody>
                             <!--   <tfoot>
                                    <tr>             
                                        <th class="text-right" colspan="9">SUMA IMPORTES:</th>
                                        <th class="text-center" colspan="1">$ {{number_format($sumaTotal,2)}}</th>
                                    </tr>
                                </tfoot>  -->
                            </table>
                            {{$info->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>