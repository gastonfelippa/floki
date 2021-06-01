<div class="row justify-content-between mb-3">
    <div class="col-8 mb-1">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
            </div>
            <input id="search" type="text" wire:model="search" class="form-control form-control-sm" placeholder="Buscar.." aria-label="notification" aria-describedby="basic-addon1" autocomplete="off">
        </div>
    </div>
    @can($create)
    <div class="col-4 mt-1">
        <button type="button" wire:click="doAction(2)" class="btn btn-danger btn-block">
            Nuevo
        </button>
    </div>
    @endcan
</div>