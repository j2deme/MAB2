<div class="space-y-6">

    <div>
        <x-input wire:model.defer='form.nombre' id='nombre' name='nombre' class='' :label="__('Nombre')"
            placeholder='Nombre' />

        @error('form.nombre')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-textarea wire:model.defer='form.descripcion' id='descripcion' name='descripcion' class=''
            :label="__('Descripcion')" placeholder='Descripcion' rows="5" />

        @error('form.descripcion')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-datetime-picker wire:model.defer="form.fecha_inicio" id="fecha_inicio" name="fecha_inicio"
            autocomplete="false" placeholder="Fecha de inicio" :label="__('Fecha de inicio')"
            display-format="DD-MM-YYYY HH:mm" />

        @error('form.fecha_inicio')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-datetime-picker wire:model.defer="form.fecha_fin" id="fecha_fin" name="fecha_fin" autocomplete="false"
            placeholder="Fecha de fin" :label="__('Fecha de fin')" display-format="DD-MM-YYYY HH:mm" />

        @error('form.fecha_fin')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-toggle wire:model.defer="form.is_activo" id="is_activo" name="is_activo" :label="__('Activo')" lg />

        @error('form.is_activo')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>
            <x-icon name="floppy-disk" class="w-4 h-4 mr-2" />
            {{ __('Guardar') }}
        </x-primary-button>

        <x-link label="Cancelar" :href="route('eventos.index')" />

        @if ($errors->any())
        <div class="flex text-sm text-red-800 flex-items">
            <x-icon name="warning" class="w-4 h-4 mr-2" />
            {{ __('Corrija los errores antes de continuar') }}
        </div>
        @endif
    </div>
</div>