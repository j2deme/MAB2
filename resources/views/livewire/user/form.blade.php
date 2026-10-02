<div class="space-y-6">
    <x-errors />
    @if (auth()->user()->es('Coordinador') && $form->mode === 'update' && isset($form->userModel) &&
    $form->userModel->es('Estudiante'))
    <div class="p-4 bg-gray-50 rounded">
        <h3 class="text-sm font-medium">Datos del estudiante</h3>
        <div class="mt-2 text-sm text-gray-700">
            <div><strong>Nombre:</strong> {{ $form->userModel->name }}</div>
            <div><strong>Usuario:</strong> {{ $form->userModel->username }}</div>
            <div><strong>Carreras:</strong> {{ $form->userModel->carreras->pluck('nombre')->join(', ') }}</div>
        </div>

        <div class="mt-4">
            <x-password wire:model.defer="newPassword" id="newPassword" name="newPassword"
                :label="__('Nueva contraseña')" placeholder="Nueva contraseña" autocomplete="off" />
        </div>

        <div class="mt-4 flex items-center gap-4">
            <x-primary-button wire:click.prevent="coordinatorResetPassword">Cambiar contraseña</x-primary-button>
            <x-link label="Cancelar" :href="route('users.index')" />
        </div>
    </div>
    @else
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
        <div>
            <x-select wire:model.defer='form.rol' id='rol' name='rol' :label="__('Rol')" placeholder='Selecciona un rol'
                :searchable="true">
                @foreach ($form->tipos as $tipo)
                <x-select.option label="{{ $tipo->value }}" value="{{ $tipo->value }}" />
                @endforeach
            </x-select>
        </div>
        <div>
            <x-input wire:model.defer=' form.email' id='email' name='email' class='' :label="__('Email')"
                placeholder='Correo electrónico' />
        </div>
        @if ($form->mode === 'create')
        <div>
            <x-password wire:model.defer='form.password' id='password' name='password' class=''
                :label="__('Contraseña')" placeholder='Contraseña' autocomplete="off" />
        </div>
        @endif
        @if (
        (auth()->user()->es('Administrador')) ||
        (auth()->user()->es('Jefe') && in_array($form->rol->value, ['Coordinador', 'Estudiante']))
        )
        <div>
            <x-password wire:model.defer='form.password' id='password' name='password' :label="__('Contraseña')"
                placeholder='Contraseña' autocomplete="off" />
        </div>
        @endif
        <div>
            <x-toggle wire:model.defer="form.inscrito" id="inscrito" name="inscrito" :label="__('¿Está inscrito?')"
                lg />
        </div>
    </div>
    <div>
        <x-select wire:model.defer='form.carreras_id' id='carreras_id' name='carreras_id' :label="__('Carrera')"
            placeholder='Selecciona una carrera' :searchable="true" multiselect :options="$form->carreras"
            option-label="nombre" option-value="id" />
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
@endif