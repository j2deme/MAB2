<div class="space-y-6">
    
    <div>
        <x-input wire:model.defer='form.siglas' id='siglas' name='siglas' class='' :label="__('Siglas')" placeholder='Siglas' />

        @error('form.siglas')
            <x-input-error class="mt-2" :messages="$message"/>
        @enderror
    </div>
    <div>
        <x-input wire:model.defer='form.semestre_id' id='semestre_id' name='semestre_id' class='' :label="__('Semestre Id')" placeholder='Semestre Id' />

        @error('form.semestre_id')
            <x-input-error class="mt-2" :messages="$message"/>
        @enderror
    </div>
    <div>
        <x-input wire:model.defer='form.materia_id' id='materia_id' name='materia_id' class='' :label="__('Materia Id')" placeholder='Materia Id' />

        @error('form.materia_id')
            <x-input-error class="mt-2" :messages="$message"/>
        @enderror
    </div>
    <div>
        <x-input wire:model.defer='form.is_disponible' id='is_disponible' name='is_disponible' class='' :label="__('Is Disponible')" placeholder='Is Disponible' />

        @error('form.is_disponible')
            <x-input-error class="mt-2" :messages="$message"/>
        @enderror
    </div>
    <div>
        <x-input wire:model.defer='form.is_paralelizable' id='is_paralelizable' name='is_paralelizable' class='' :label="__('Is Paralelizable')" placeholder='Is Paralelizable' />

        @error('form.is_paralelizable')
            <x-input-error class="mt-2" :messages="$message"/>
        @enderror
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>
            <x-icon name="floppy-disk" class="w-4 h-4 mr-2" />
            {{ __('Guardar') }}
        </x-primary-button>

        <x-link label="Cancelar" :href="route('grupos.index')" />

        @if ($errors->any())
        <div class="flex text-sm text-red-800 flex-items">
            <x-icon name="warning" class="w-4 h-4 mr-2" />
            {{ __('Corrija los errores antes de continuar') }}
        </div>
        @endif
    </div>
</div>