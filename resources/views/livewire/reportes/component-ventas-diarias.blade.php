<div class="row layout-top-spacing">
    <div class="col-12 layout-spacing">
        <div class="widget-content-area">
            <div class="widget-one">
                <div class="row">
                    <div class="col-8 text-center">
                        <h3>Reporte de Ventas Diarias</h3>
                    </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-block btn-warning">
                            <a href="{{url('pdfFacturas')}}" target="_blank">
                                IMPRIMIR </a>   
                            </button>
                        </div>
                </div>
                <div class="row mt-2">
                    <div class="col-sm-12 col-md-3 text-left mb-2">
                        <b>Fecha de Consulta:</b> {{\Carbon\Carbon::now()->format('d-m-Y')}}
                        <br>
                        <b>Cantidad de Registros:</b> {{$info->count()}}
                        <br>
                        @if($estado == 1)
                        <b>Total de Ingresos:</b> $ {{number_format($sumaTotal,2)}}
                        @endif
                        @if($estado == 2)
                        <b>Total Contado:</b> $ {{number_format($sumaTotal,2)}}
                        @endif
                        @if($estado == 3)
                        <b>Total Cuenta Corriente:</b> $ {{number_format($sumaTotal,2)}}
                        @endif
                    </div>
                    <div class="col-sm-12 col-md-3 mb-2">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                            </div>
                            <input type="text" wire:model="search" onfocus="limpiarSearch()" class="form-control form-control-sm" placeholder="Buscar por N° Fact..." aria-label="notification" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-3 mb-2">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                            </div>
                            <input type="text" wire:model="searchCli" onfocus="limpiarSearch()" class="form-control form-control-sm" placeholder="Buscar por Cliente..." aria-label="notification" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" value="1" wire:model="estado" checked>
                            <label class="form-check-label">Todas</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" value="2" wire:model="estado" >
                            <label class="form-check-label">Contado</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" value="3" wire:model="estado">
                            <label class="form-check-label">Cta. Cte.</label>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="table-responsive mt-3 table-sm col-lg-8  scroll">
                        <table class="table table-hover table-checkable table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">FACT N°</th>
                                    <th class="text-center">FECHA</th>
                                    <th class="text-center">CLIENTE</th>
                                    <th class="text-center">IMPORTE</th>
                                    <th class="text-center">CAJA</th>
                                    <th class="text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($info as $r)
                                <tr>
                                    <td class="text-center">{{str_pad($r->numero, 6, '0', STR_PAD_LEFT)}}</td>
                                    <td class="text-center">{{$r->created_at->format('d-m-Y')}}</td>
                                    <td class="text-left">{{$r->nomCli}}</td> 
                                    <td class="text-center">{{number_format($r->importe,2)}}</td> 
                                    <td class="text-left">{{$r->nomRep}}</td> 
                                    <td class="text-center">
                                        <a href="{{url('pdfFactDel',array($r->id))}}" target="_blank" title="Ver Factura">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye text-success"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>    
                                            <!-- @include('common.actions', ['edit' => 'Facturas_index', 'destroy' => 'Facturas_index']) botones editar y eliminar             -->
                                            <!-- <button type="button" class="btn btn-success" enabled>
                                            <a href="{{url('pdfFactDel',array($r->id))}}" target="_blank">
                                            Imprimir</a>
                                            </button> -->
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>               
            </div>
        </div>
    </div>    
</div>

<style type="text/css" scoped>
.scroll{
    height: 255px;
    position: relative;
    margin-top: .5rem;
    overflow: auto;
}
</style>

<script type="text/javascript">

function limpiarSearch(){
    window.livewire.emit('limpiarSearch')
}

</script>
