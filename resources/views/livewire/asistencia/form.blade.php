<div class="space-y-6">

    <div>
        <x-select wire:model.defer='form.actividad_id' id='actividad_id' name='actividad_id' :label="__('Actividad')"
            placeholder='Selecciona un actividad' :options="$actividades" option-label="nombre" option-value="id"
            :searchable="true" />

        @error('form.actividad_id')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-select wire:model.defer='form.user_id' id='user_id' name='user_id' :label="__('Estudiante')"
            placeholder='Selecciona un estudiante' :options="$users" option-label="username" option-value="id"
            option-description="name" :searchable="true" />

        @error('form.user_id')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>
            <x-icon name="floppy-disk" class="w-4 h-4 mr-2" />
            {{ __('Guardar') }}
        </x-primary-button>

        @if (!request()->routeIs('asistencias.magistral'))
        <x-link label="Cancelar" :href="route('asistencias.index')" />
        @endif

        @if ($errors->any())
        <div class="flex text-sm text-red-800 flex-items">
            <x-icon name="warning" class="w-4 h-4 mr-2" />
            {{ __('Corrija los errores antes de continuar') }}
        </div>
        @endif
    </div>
</div>