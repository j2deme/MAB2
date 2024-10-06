<div class="space-y-6">

    <div>
        <x-select wire:model.defer='form.evento_id' id='evento_id' name='evento_id' :label="__('Evento')"
            placeholder='Selecciona un evento' :options="$eventos" option-label="nombre" option-value="id"
            option-description="descripcion" />

        @error('form.evento_id')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>

    <div>
        <x-input wire:model.defer='form.clave' id='clave' name='clave' class='' :label="__('Clave')"
            placeholder='Clave' />

        @error('form.clave')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
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
        <x-input-error class="mt-2 " :messages="$message" />
        @enderror
    </div>
    <div>
        <x-datetime-picker wire:model.defer="form.fecha_inicio" id="fecha_inicio" name="fecha_inicio"
            autocomplete="false" placeholder="Inicio de la actividad" :label="__('Inicio de la actividad')"
            display-format="DD-MM-YYYY HH:mm" />

        @error('form.fecha_inicio')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-datetime-picker wire:model.defer="form.fecha_fin" id="fecha_fin" name="fecha_fin" autocomplete="false"
            placeholder="Fin de la actividad" :label="__('Fin de la actividad')" display-format="DD-MM-YYYY HH:mm" />

        @error('form.fecha_fin')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-input wire:model.live="form.duracion" id="duracion" name="duracion" :label="__('Duración')"
            description="Duración en horas" type="number" min="1" max="20" placeholder="2" />

        @error('form.duracion')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-toggle wire:model.defer="form.is_activo" id="is_activo" name="is_activo" :label="__('Activo')" lg />

            @error('form.is_activo')
            <x-input-error class="mt-2" :messages="$message" />
            @enderror
        </div>
        <div>
            <x-toggle wire:model.defer="form.is_magistral" id="is_magistral" name="is_magistral"
                :label="__('¿Es conferencia magistral?')" lg />

            @error('form.is_magistral')
            <x-input-error class="mt-2" :messages="$message" />
            @enderror
        </div>
    </div>
    <div>
        <x-select wire:model.defer='form.tipo' id='tipo' name='tipo' :label="__('Tipo')"
            placeholder='Selecciona un tipo'>
            <x-select.option label="Conferencia" value="Conferencia" />
            <x-select.option label="Curso" value="Curso" />
            <x-select.option label="Taller" value="Taller" />
            <x-select.option label="Otro" value="Otro" />
        </x-select>

        @error('form.tipo')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-input wire:model.defer='form.lugar' id='lugar' name='lugar' class='' :label="__('Lugar')"
            placeholder='Lugar' />

        @error('form.lugar')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-select wire:model.defer='form.modalidad' id='modalidad' name='modalidad' :label="__('Modalidad')"
            placeholder='Selecciona una modalidad'>
            <x-select.option label="Presencial" value="Presencial" />
            <x-select.option label="Virtual" value="Virtual" />
            <x-select.option label="Híbrida" value="Híbrida" />
        </x-select>

        @error('form.modalidad')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>
            <x-icon name="floppy-disk" class="w-4 h-4 mr-2" />
            {{ __('Guardar') }}
        </x-primary-button>

        <x-link label="Cancelar" :href="route('actividades.index')" />

        @if ($errors->any())
        <div class="flex text-sm text-red-800 flex-items">
            <x-icon name="warning" class="w-4 h-4 mr-2" />
            {{ __('Corrija los errores antes de continuar') }}
        </div>
        @endif
    </div>
</div>