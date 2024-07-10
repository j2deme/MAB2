<?php

use function Livewire\Volt\{state};
use function Livewire\Volt\{computed};

$semestres = function(){
    $option = [];
    for ($i = 1; $i <= 9; $i++){
        switch ($i) {
            case 1:
            case 3:
                $option = ['value' => $i, 'label' => $i.'er semestre'];
                break;
            default:
                $option = ['value' => $i, 'label' => $i.'o semestre'];
                break;
        }
        $options[] = $option;
    }

    return $options;
};

?>

<div class="space-y-6">
    <x-errors />

    <div class="grid grid-cols-2 gap-6">
        <div>
            <x-input wire:model.defer='form.clave' id='clave' name='clave' class='' :label="__('Clave')"
                placeholder='Clave' />

            {{-- @error('form.clave')
            <x-input-error class="mt-2" :messages="$message" />
            @enderror --}}
        </div>
        <div>
            <x-input wire:model.defer='form.nombre' id='nombre' name='nombre' class='' :label="__('Nombre corto')"
                placeholder='Nombre' />

            {{-- @error('form.nombre')
            <x-input-error class="mt-2" :messages="$message" />
            @enderror --}}
        </div>
    </div>
    <div>
        <x-input wire:model.defer='form.nombre_completo' id='nombre_completo' name='nombre_completo' class=''
            :label="__('Nombre Completo')" placeholder='Nombre Completo' />

        {{-- @error('form.nombre_completo')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror --}}
    </div>
    <div class="grid grid-cols-2 gap-6">
        <div>
            <x-select wire:model.defer="form.carrera_id" id="carrera_id" name="carrera_id" label="Carrera"
                placeholder="Selecciona una carrera" :async-data="route('api.carreras.index')" option-label="nombre"
                option-value="id" />

            {{-- @error('form.carrera_id')
            <x-input-error class="mt-2" :messages="$message" />
            @enderror --}}
        </div>
        <div>
            <x-select wire:model.defer="form.semestre" id="semestre" name="semestre" :label="__('Semestre')"
                placeholder="Selecciona un semestre" :options="$semestres" option-value="value" option-label="label" />

            {{-- @error('form.semestre')
            <x-input-error class="mt-2" :messages="$message" />
            @enderror --}}
        </div>
    </div>
    <div class="grid grid-cols-3 gap-6">
        <div>
            <x-input wire:model.defer='form.ht' id='ht' name='ht' class='' :label="__('Horas teóricas')"
                placeholder='1' />

            {{-- @error('form.ht')
            <x-input-error class="mt-2" :messages="$message" />
            @enderror --}}
        </div>
        <div>
            <x-input wire:model.defer='form.hp' id='hp' name='hp' class='' :label="__('Horas prácticas')"
                placeholder='4' />

            {{-- @error('form.hp')
            <x-input-error class="mt-2" :messages="$message" />
            @enderror --}}
        </div>
        <div>
            <x-input wire:model.defer='form.cr' id='cr' name='cr' class='' :label="__('Créditos')" placeholder='5' />

            {{-- @error('form.cr')
            <x-input-error class="mt-2" :messages="$message" />
            @enderror --}}
        </div>
    </div>
    <div>
        <x-toggle wire:model.defer="form.activo" id="activo" name="activo" :label="__('Activo')" lg />

        {{-- @error('form.activo')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror --}}
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>
            <x-icon name="floppy-disk" class="w-4 h-4 mr-2" />
            {{ __('Guardar') }}
        </x-primary-button>

        <x-link label="Cancelar" :href="route('materias.index')" />

        @if ($errors->any())
        <div class="flex text-sm text-red-800 flex-items">
            <x-icon name="warning" class="w-4 h-4 mr-2" />
            {{ __('Corrija los errores antes de continuar') }}
        </div>
        @endif
    </div>
</div>