<div class="panel-nav">
    <ul>
        <li>
            <button wire:click="action_edit('datos')"
                @if($action_edit == 'datos') class="active" @endif>
                <i class="bi bi-card-checklist"></i> Datos generales
            </button>
        </li>
        <li>
            <button type="button" wire:click="action_edit('opciones')"
                @if($action_edit == 'opciones') class="active" @endif>
                <i class="bi bi-save"></i> Opciones de guardado
            </button>
        </li>
        <li>
            <button type="button" wire:click="action_edit('precios')"
                @if($action_edit == 'precios') class="active" @endif>
                <i class="bi bi-currency-dollar"></i> Cálculo precio de venta
            </button>
        </li>
        <li>
            <button type="button" wire:click="action_edit('impresiones')"
                @if($action_edit == 'impresiones') class="active" @endif>
                <i class="bi bi-printer"></i> Impresiones
            </button>
        </li>
        <li>
            <button type="button" wire:click="action_edit('prueba')"
                @if($action_edit == 'prueba') class="active" @endif>
                <i class="bi bi-card-checklist"></i> Datos de prueba
            </button>
        </li>
    </ul>
</div>