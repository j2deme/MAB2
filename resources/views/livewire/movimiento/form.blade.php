<div class="space-y-6">
    @if ($form->outOfRange)
    <x-alert title="Fuera de rango" negative>
        @if($form->tipo->value == 'Alta')
        <p>Fuera de rango para solicitar alta de materias.</p>
        @else
        <p>Fuera de rango para solicitar baja de materias.</p>
        @endif
        <p>Contacta a tu coordinador(a) de carrera para mayor información.</p>
    </x-alert>
    @else
    <x-errors />
    {{-- <div>
        <x-input wire:model.defer='form.user_id' id='user_id' name='user_id' class='' :label="__('User Id')"
            placeholder='User Id' />
    </div>
    <div>
        <x-input wire:model.defer='form.semestre_id' id='semestre_id' name='semestre_id' class=''
            :label="__('Semestre Id')" placeholder='Semestre Id' />
    </div>
    <div>
        <x-input wire:model.defer='form.carrera_id' id='carrera_id' name='carrera_id' class='' :label="__('Carrera Id')"
            placeholder='Carrera Id' />
    </div>
    <div>
        <x-select wire:model.defer='form.tipo' id='tipo' name='tipo' :label="__('Tipo')"
            placeholder='Selecciona un tipo de movimiento'>
            @foreach ($form->tipos as $tipo)
            <x-select.option label="{{ $tipo->value }}" value="{{ $tipo->value }}" />
            @endforeach
        </x-select>
    </div>
    <div>
        <x-toggle wire:model.defer="form.is_paralelo" id="is_paralelo" name="is_paralelo" :label="__('¿Paralelo?')"
            lg />
    </div> --}}

    @includeWhen(auth()->user()->es('Estudiante') and $form->tipo->value == 'Alta', 'livewire.movimiento.slots')

    @if (!auth()->user()->es('Estudiante') and $form->movimientoModel->exists)
    @php
    $move = $form->movimientoModel;
    @endphp
    <x-card class="border-2 border-{{ $move->grupo->carrera->color }} text-sm">
        <x-slot name="title" class="w-full">
            <div class="grid grid-cols-2">
                <div class="place-self-start">
                    {{ $move->grupo->materia->clave }}
                </div>
                <div class="place-self-end">
                    {{ $move->user->username }}
                </div>
            </div>
        </x-slot>
        {{ $move->grupo->materia->nombre_completo }} {{ $move->grupo->siglas }}

        <x-slot name="footer" class="w-full">
            <div class="grid grid-cols-3">
                <div class="place-self-start">
                    @include('components.carrera-badge', ['carrera' => $move->grupo->carrera])
                </div>
                <div class="place-self-center">
                    @include('components.movimiento-tipo-icon', ['tipo' => $move->tipo->value])
                </div>
                <div class="place-self-end">
                    @includeWhen($move->is_paralelo,'components.paralelo-icon', ['paralelo' =>
                    $move->is_paralelo])
                </div>
            </div>
        </x-slot>
    </x-card>

    <x-card title="{{ $form->motivo }}" shadow="md">
        @if (!Str($form->motivo_adicional)->isEmpty())
        <p class="text-sm">{{ $form->motivo_adicional }}</p>
        @endif
    </x-card>
    @endif

    @if (auth()->user()->es('Estudiante'))
    <div>
        <x-select wire:model.defer='form.grupo_id' id='grupo_id' name='grupo_id' :label="__('Grupo')"
            placeholder='Selecciona un grupo' :options="$form->grupos" option-label="nombre" option-value="id"
            option-description="materia.carrera.nombre" :searchable="true" />
    </div>
    <div>
        <x-select wire:model.defer='form.motivo' id='motivo' name='motivo' :label="__('Motivo')"
            placeholder='Selecciona un motivo'>
            @foreach ($form->motivos as $motivo)
            <x-select.option label="{{ $motivo->value }}" value="{{ $motivo->value }}" />
            @endforeach
        </x-select>
    </div>
    <div>
        <x-textarea wire:model.defer='form.motivo_adicional' id='motivo_adicional' name='motivo_adicional' class=''
            :label="__('Motivo Adicional')" placeholder='Motivo Adicional' />
    </div>
    @endif

    @if (!auth()->user()->es('Estudiante'))
    <div>
        <x-select wire:model.defer='form.respuesta' id='respuesta' name='respuesta' :label="__('Respuesta')"
            placeholder='Selecciona una respuesta rápida'>
            @foreach ($form->respuestas as $respuesta)
            <x-select.option label="{{ $respuesta->value }}" value="{{ $respuesta->value }}" />
            @endforeach
        </x-select>
    </div>
    <div>
        <x-textarea wire:model.defer='form.respuesta_adicional' id='respuesta_adicional' name='respuesta_adicional'
            class='' :label="__('Respuesta Adicional')" placeholder='Respuesta Adicional' />
    </div>
    <div>
        <x-select wire:model.defer='form.estatus' id='estatus' name='estatus' :label="__('Estatus')"
            placeholder='Selecciona un estatus'>
            @foreach ($form->estatuses as $est)
            <x-select.option label="{{ $est->value }}" value="{{ $est->value }}" />
            @endforeach
        </x-select>
    </div>
    @endif

    {{-- <div>
        <x-select wire:model.defer='form.asociado_id' id='asociado_id' name='asociado_id'
            :label="__('Movimiento asociado')" placeholder='Selecciona un movimiento para asociar'
            :options="$form->movimientos" option-label="nombre" option-value="id" />
    </div> --}}

    <div class="flex items-center gap-4">
        <x-primary-button>
            <x-icon name="floppy-disk" class="w-4 h-4 mr-2" />
            {{ __('Guardar') }}
        </x-primary-button>

        <x-link label="Cancelar" :href="route('movimientos.index')" />

        @if ($errors->any())
        <div class="flex text-sm text-red-800 flex-items">
            <x-icon name="warning" class="w-4 h-4 mr-2" />
            {{ __('Corrija los errores antes de continuar') }}
        </div>
        @endif
    </div>
    @endif
</div>