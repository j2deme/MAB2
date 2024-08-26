<div class="space-y-4">
    <x-errors />
    <div class="grid grid-cols-2 gap-6">
        <div>
            <x-input wire:model.defer="form.clave" id="clave" name="clave" class="" :label="__('Clave')"
                placeholder="{{ date('Yn') }}" />
        </div>
        <div>
            <x-input wire:model.defer="form.nombre" id="nombre" name="nombre" class="" :label="__('Nombre corto')"
                placeholder="Ago - Dic {{ date('Y') }}" />
        </div>
    </div>
    <div>
        <x-input wire:model.defer="form.nombre_completo" id="nombre_completo" name="nombre_completo" class=""
            :label="__('Nombre Completo')" placeholder="Agosto - Diciembre {{ date('Y') }}" />
    </div>
    <div class="flex items-center gap-4">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Fechas') }}
        </h2>
    </div>
    <div class="grid grid-cols-2 gap-6">
        <div>
            <x-datetime-picker wire:model.defer="form.inicio_altas" id="inicio_altas" name="inicio_altas"
                autocomplete="false" placeholder="Inicio altas" :label="__('Inicio Altas')"
                display-format="DD-MM-YYYY HH:mm" />
        </div>
        <div>
            <x-datetime-picker wire:model.defer="form.fin_altas" id="fin_altas" name="fin_altas" autocomplete="false"
                placeholder="Fin altas" :label="__('Fin Altas')" display-format="DD-MM-YYYY HH:mm" />
        </div>
    </div>
    <div class="grid grid-cols-2 gap-6">
        <div>
            <x-datetime-picker wire:model.defer="form.inicio_bajas" id="inicio_bajas" name="inicio_bajas"
                autocomplete="false" placeholder="Inicio bajas" :label="__('Inicio Bajas')"
                display-format="DD-MM-YYYY HH:mm" />
        </div>
        <div>
            <x-datetime-picker wire:model.defer="form.fin_bajas" id="fin_bajas" name="fin_bajas" autocomplete="false"
                placeholder="Fin bajas" :label="__('Fin Bajas')" display-format="DD-MM-YYYY HH:mm" />
        </div>
    </div>
    <div class="flex items-center gap-4">
        <h2 class="text-lg font-semibold leading-tight text-gray-800">
            {{ __('Otros') }}
        </h2>
    </div>
    <div class="grid grid-cols-2 gap-6">
        <div>
            <x-input wire:model.defer="form.max_altas" id="max_altas" name="max_altas" :label="__('Máximo Altas')"
                description="Cantidad máxima de altas permitidas" type="number" min="1" max="10" placeholder="5" />
        </div>
        <div>
            <x-toggle wire:model.defer="form.activo" id="activo" name="activo" :label="__('Activo')" lg />
        </div>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>
            <x-icon name="floppy-disk" class="w-4 h-4 mr-2" />
            {{ __('Guardar') }}
        </x-primary-button>

        <x-link label="Cancelar" :href="route('semestres.index')" />

        @if ($errors->any())
        <div class="flex text-sm text-red-800 flex-items">
            <x-icon name="warning" class="w-4 h-4 mr-2" />
            {{ __('Corrija los errores antes de continuar') }}
        </div>
        @endif
    </div>
</div>