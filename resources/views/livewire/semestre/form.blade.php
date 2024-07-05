<div class="space-y-6">
    
    <div>
        <x-input-label for="clave" :value="__('Clave')"/>
        <x-text-input wire:model="form.clave" id="clave" name="clave" type="text" class="mt-1 block w-full" autocomplete="clave" placeholder="Clave"/>
        @error('form.clave')
            <x-input-error class="mt-2" :messages="$message"/>
        @enderror
    </div>
    <div>
        <x-input-label for="nombre" :value="__('Nombre')"/>
        <x-text-input wire:model="form.nombre" id="nombre" name="nombre" type="text" class="mt-1 block w-full" autocomplete="nombre" placeholder="Nombre"/>
        @error('form.nombre')
            <x-input-error class="mt-2" :messages="$message"/>
        @enderror
    </div>
    <div>
        <x-input-label for="nombre_completo" :value="__('Nombre Completo')"/>
        <x-text-input wire:model="form.nombre_completo" id="nombre_completo" name="nombre_completo" type="text" class="mt-1 block w-full" autocomplete="nombre_completo" placeholder="Nombre Completo"/>
        @error('form.nombre_completo')
            <x-input-error class="mt-2" :messages="$message"/>
        @enderror
    </div>
    <div>
        <x-input-label for="inicio_altas" :value="__('Inicio Altas')"/>
        <x-text-input wire:model="form.inicio_altas" id="inicio_altas" name="inicio_altas" type="text" class="mt-1 block w-full" autocomplete="inicio_altas" placeholder="Inicio Altas"/>
        @error('form.inicio_altas')
            <x-input-error class="mt-2" :messages="$message"/>
        @enderror
    </div>
    <div>
        <x-input-label for="fin_altas" :value="__('Fin Altas')"/>
        <x-text-input wire:model="form.fin_altas" id="fin_altas" name="fin_altas" type="text" class="mt-1 block w-full" autocomplete="fin_altas" placeholder="Fin Altas"/>
        @error('form.fin_altas')
            <x-input-error class="mt-2" :messages="$message"/>
        @enderror
    </div>
    <div>
        <x-input-label for="inicio_bajas" :value="__('Inicio Bajas')"/>
        <x-text-input wire:model="form.inicio_bajas" id="inicio_bajas" name="inicio_bajas" type="text" class="mt-1 block w-full" autocomplete="inicio_bajas" placeholder="Inicio Bajas"/>
        @error('form.inicio_bajas')
            <x-input-error class="mt-2" :messages="$message"/>
        @enderror
    </div>
    <div>
        <x-input-label for="fin_bajas" :value="__('Fin Bajas')"/>
        <x-text-input wire:model="form.fin_bajas" id="fin_bajas" name="fin_bajas" type="text" class="mt-1 block w-full" autocomplete="fin_bajas" placeholder="Fin Bajas"/>
        @error('form.fin_bajas')
            <x-input-error class="mt-2" :messages="$message"/>
        @enderror
    </div>
    <div>
        <x-input-label for="max_altas" :value="__('Max Altas')"/>
        <x-text-input wire:model="form.max_altas" id="max_altas" name="max_altas" type="text" class="mt-1 block w-full" autocomplete="max_altas" placeholder="Max Altas"/>
        @error('form.max_altas')
            <x-input-error class="mt-2" :messages="$message"/>
        @enderror
    </div>
    <div>
        <x-input-label for="activo" :value="__('Activo')"/>
        <x-text-input wire:model="form.activo" id="activo" name="activo" type="text" class="mt-1 block w-full" autocomplete="activo" placeholder="Activo"/>
        @error('form.activo')
            <x-input-error class="mt-2" :messages="$message"/>
        @enderror
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Submit</x-primary-button>
    </div>
</div>