<div class="main-content">
    <div class="layout-px-spacing">
    @include('common.alerts')
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <div class="widget-content-area">
                    <div class="widget-one">
                        <h4 class="text-center">Reporte de Ventas por Fecha</h4>
                        <hr>
                        <div class="row">
                            <div class="col-sm-12 col-md-2">
                                Fecha inicial
                                <div class="form-group"> 
                                    <input wire:model.lazy="fecha_ini" type="text" class="form-control form-control-sm flatpickr flatpickr-input inactive" placeholder="Haz click">
                                </div>
                            </div> 
                            <div class="col-sm-12 col-md-2">
                                Fecha final
                                <div class="form-group">
                                    <input wire:model.lazy="fecha_fin" type="text" class="form-control form-control-sm flatpickr flatpickr-input active" placeholder="Haz click">
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-2 mt-4">
                                <button type="submit" wire:click="hoy()" class="btn btn-primary">Hoy</button>
                            </div>
                            <div class="row col-sm-12 col-md-6 mt-2">
                                <div class="col-5">
                                @if($verFacturas == 0)
                                    <button type="submit" wire:click="ver_facturas()" class="btn btn-info btn-block">Ver Facturas</button>
                                @else
                                    <button type="submit" wire:click="ver_facturas()" class="btn btn-info btn-block">Ocultar Facturas</button>
                                @endif
                                </div>
                                <div class="col-5">
                                    <button type="submit" class="btn btn-dark">Exportar</button>
                                </div>
                            </div>    
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-5">
                            <div class="row justify-content-between mb-3">
                                <div class="col-8 mb-1">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                                        </div>
                                        <input id="search" type="text" wire:model="search" class="form-control form-control-sm" placeholder="Buscar.." aria-label="notification" aria-describedby="basic-addon1" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-4 mt-1">
                                    <button id="btnNuevo" type="button" wire:click="doAction(2)" class="btn btn-danger btn-block">
                                        <span style="text-decoration: underline;">N</span>uevo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row col-sm-12 col-md-5">
                        <div class="col-7">
                            <b>Fecha/hora Inicio Arqueo</b>
                            <br>
                            <b>Cantidad Registros</b> 
                            <br>
                            <b>Total de Ventas</b> 
                        </div>
                        <div class="col-5">
                            {{\Carbon\Carbon::parse($fecha_arqueo)->format('d-m-Y')}} /
                            {{\Carbon\Carbon::parse($hora_arqueo)->format('H:i')}}
                            <br>
                            {{$cantVentas}}
                            <br>
                            $ {{number_format($sumaTotal,2)}}
                        </div> 
                    </div>
                    <!-- <div class="row justify-content-center">
                        <div class="table-responsive mt-3 table-sm col-lg-8  scroll">
                            <table class="table table-hover table-checkable table-sm">
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
                                <tfoot>
                                    <tr>             
                                        <th class="text-right" colspan="9">SUMA IMPORTES:</th>
                                        <th class="text-center" colspan="1">$ {{number_format($sumaTotal,2)}}</th>
                                    </tr>
                                </tfoot>  
                            </table>
                        </div>
                    </div> -->
                    <div class="row justify-content-center">
                        <div class="table-responsive mt-3 table-sm col-lg-8  scroll">
                            <table class="table table-hover table-checkable table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-center">DESCRIPCION</th>
                                        <th class="text-center">CANTIDAD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($infoProd as $r)
                                    <tr>
                                        <td class="text-center">{{$r->descripcion}}</td>
                                        <td class="text-center">{{$r->cantidad}}</td>                               
                                    </tr>
                                    @endforeach
                                </tbody>
                               <!-- <tfoot>
                                    <tr>             
                                        <th class="text-right" colspan="9">SUMA IMPORTES:</th>
                                        <th class="text-center" colspan="1">$ {{number_format($sumaTotal,2)}}</th>
                                    </tr>
                                </tfoot>  -->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style type="text/css" scoped>
.scroll{
    height: 205px;
    position: relative;
    margin-top: .5rem;
    overflow: auto;
}
</style>

<script type="text/javascript">
    window.onload = function() {
        Livewire.on('arqueo',()=>{
            Swal.fire('Opss','No existe Arqueo para la fecha elegida...','info');
            return;
        })
    }
</script>