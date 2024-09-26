<div class="panel-nav">
    <ul>
        <li>
            <button wire:click="action_edit('datos')"
                @if($action_edit == 'datos') class="active" @endif>
                <i class="bi bi-egg-fried"></i> Datos
            </button>
        </li>
        <li>
            <button type="button" wire:click="action_edit('precios')"
                @if($action_edit == 'precios') class="active" @endif>
                <i class="bi bi-coin"></i> Precios
            </button>
        </li>
        <li>
            <button type="button" wire:click="action_edit('stock')"
                @if($action_edit == 'stock') class="active" @endif>
                <i class="bi bi-bricks"></i> Stock
            </button>
        </li>
        <li>
            <button type="button" wire:click="action_edit('comandas')"
                @if($action_edit == 'comandas') class="active" @endif>
                <i class="bi bi-pencil-square"></i> Comandas
            </button>
        </li>
    </ul>
</div>