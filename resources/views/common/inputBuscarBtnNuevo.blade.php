<div class="row justify-content-between mb-3">
    <div class="col-8 mb-1">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1"><i class="bi bi-search"></i></span>
            </div>
            <input id="search" type="text" wire:model="search" class="form-control form-control-sm" placeholder="Buscar..." aria-label="notification" aria-describedby="basic-addon1" autocomplete="off" autofocus>
        </div>
    </div>
    @can($create)
    <div class="col-4 mt-1">
        <button id="btnNuevo" type="button" wire:click="doAction(2)" class="btn btn-danger btn-block">
            <span style="text-decoration: underline;">N</span>uevo
        </button>
    </div>
    @endcan
</div>