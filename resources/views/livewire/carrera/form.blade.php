<div class="space-y-4">
    <x-errors />
    <div class="grid grid-cols-2 gap-6">
        <div>
            <x-input wire:model.defer="form.siglas" id="siglas" name="siglas" class="" :label="__('Siglas')"
                placeholder="II" errorless />

            @error('form.siglas')
            <x-input-error class="mt-2" :messages="$message" />
            @enderror
        </div>
        <div>
            <x-input wire:model.defer="form.clave_interna" id="clave_interna" name="clave_interna" class=""
                :label="__('Clave Interna')" placeholder="99" type="number"
                description="Clave en la plataforma Mindbox" />

            @error('form.clave_interna')
            <x-input-error class="mt-2" :messages="$message" />
            @enderror
        </div>
    </div>
    <div>
        <x-input wire:model.defer="form.nombre" id="nombre" name="nombre" class="" :label="__('Nombre')"
            placeholder="Ingeniería..." />

        @error('form.nombre')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>

    <div>
        <x-color-picker wire:model.defer="form.color" id="color" name="color" :label="__('Color')"
            :placeholder="fake()->hexColor()" color-name-as-value />

        @error('form.color')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>
            <x-icon name="floppy-disk" class="w-4 h-4 mr-2" />
            {{ __('Guardar') }}
        </x-primary-button>

        <x-link label="Cancelar" :href="route('carreras.index')" />

        @if ($errors->any())
        <div class="flex text-sm text-red-800 flex-items">
            <x-icon name="warning" class="w-4 h-4 mr-2" />
            {{ __('Corrija los errores antes de continuar') }}
        </div>
        @endif
    </div>
</div>