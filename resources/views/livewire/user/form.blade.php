<div class="space-y-6">
    <x-errors />
    <div>
        <x-input wire:model.defer='form.name' id='name' name='name' class='' :label="__('Name')"
            placeholder='Nombre completo' />
    </div>
    <div class="grid grid-cols-2 gap-6">
        <div>
            <x-input wire:model.defer='form.username' id='username' name='username' class=''
                :label="__('Nombre de usuario')" placeholder='Nombre de usuario'
                description="Para estudiantes es el número de control" autocomplete="off" />
        </div>
        @if ($form->mode === 'create')
        <div>
            <x-password wire:model.defer='form.password' id='password' name='password' class=''
                :label="__('Contraseña')" placeholder='Contraseña' autocomplete="off" />
        </div>
        @endif
        <div>
            <x-input wire:model.defer=' form.email' id='email' name='email' class='' :label="__('Email')"
                placeholder='Correo electrónico' />
        </div>
        <div>
            <x-select wire:model.defer='form.rol' id='rol' name='rol' :label="__('Rol')" placeholder='Selecciona un rol'
                :searchable="true">
                @foreach ($form->tipos as $tipo)
                <x-select.option label="{{ $tipo->value }}" value="{{ $tipo->value }}" />
                @endforeach
            </x-select>
        </div>
        <div>
            <x-toggle wire:model.defer="form.inscrito" id="inscrito" name="inscrito" :label="__('¿Está inscrito?')"
                lg />
        </div>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>
            <x-icon name="floppy-disk" class="w-4 h-4 mr-2" />
            {{ __('Guardar') }}
        </x-primary-button>

        <x-link label="Cancelar" :href="route('users.index')" />

        @if ($errors->any())
        <div class="flex text-sm text-red-800 flex-items">
            <x-icon name="warning" class="w-4 h-4 mr-2" />
            {{ __('Corrija los errores antes de continuar') }}
        </div>
        @endif
    </div>
</div>