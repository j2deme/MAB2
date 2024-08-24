<div class="space-y-6">
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
    </div> --}}
    @includeWhen(auth()->user()->es('Estudiante') and $form->tipo->value == 'Alta', 'livewire.movimiento.slots')
    <div>
        @if (auth()->user()->es('Estudiante'))
        <x-select wire:model.defer='form.grupo_id' id='grupo_id' name='grupo_id' :label="__('Grupo')"
            placeholder='Selecciona un grupo' :options="$form->grupos" option-label="nombre" option-value="id"
            option-description="materia.carrera.nombre" :searchable="true" />
        @else
        <x-select wire:model.defer='form.grupo_id' id='grupo_id' name='grupo_id' :label="__('Grupo')"
            placeholder='Selecciona un grupo' :options="$form->grupos" option-label="nombre" option-value="id"
            option-description="materia.carrera.nombre" readonly />
        @endif
    </div>
    @if (!auth()->user()->es('Estudiante'))
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
            <x-select wire:model.defer='form.tipo' id='tipo' name='tipo' :label="__('Tipo')"
                placeholder='Selecciona un tipo de movimiento' readonly>
                @foreach ($form->tipos as $tipo)
                <x-select.option label="{{ $tipo->value }}" value="{{ $tipo->value }}" />
                @endforeach
            </x-select>
        </div>
        <div>
            <x-select wire:model.defer='form.estatus' id='estatus' name='estatus' :label="__('Estatus')"
                placeholder='Selecciona un estatus'>
                @foreach ($form->estatuses as $est)
                <x-select.option label="{{ $est->value }}" value="{{ $est->value }}" />
                @endforeach
            </x-select>
        </div>
    </div>
    @endif
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
    @endif
    <div>
        <x-select wire:model.defer='form.asociado_id' id='asociado_id' name='asociado_id'
            :label="__('Movimiento asociado')" placeholder='Selecciona un movimiento para asociar'>
            @foreach ($form->movimientos as $movimiento)
            <x-select.option label="{{ $movimiento->nombre }}" value="{{ $movimiento->id }}" />
            @endforeach
        </x-select>
    </div>
    <div>
        @if (!auth()->user()->es('Estudiante'))
        <x-toggle wire:model.defer="form.is_paralelo" id="is_paralelo" name="is_paralelo" :label="__('¿Paralelo?')"
            lg />
        @endif
    </div>

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
</div>