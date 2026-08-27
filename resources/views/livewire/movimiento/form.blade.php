<div class="space-y-6">
    @php
    // Reverted to server-side loading for selects to avoid async-data issues.
    @endphp
    @if ($form->outOfRange)
    {{-- Mensaje exclusivo para estudiantes --}}
    <x-alert title="Fuera de rango" negative>
        @if($form->tipo?->value == 'Alta')
        <p>Fuera de rango para solicitar alta de materias.</p>
        @else
        <p>Fuera de rango para solicitar baja de materias.</p>
        @endif
        <p>Contacta a tu coordinador(a) de carrera para mayor información.</p>
    </x-alert>
    @elseif(auth()->user()->es('Estudiante') and $form->tipo?->value == 'Alta' and (count($form->altas) >=
    $form->max_altas))
    {{-- Mensaje exclusivo para estudiantes que ya alcanzaron el máximo de solicitudes --}}
    <x-alert title="Límite alcanzado" negative>
        <p>Has alcanzado el límite de solicitudes de alta de materias.</p>
        <p>Si necesitar otro movimiento, analiza cual de los movimientos registrados ocupas menos y eliminalo.</p>
    </x-alert>
    @includeWhen(auth()->user()->es('Estudiante') and $form->tipo?->value == 'Alta', 'livewire.movimiento.slots')
    @else
    <x-errors />

    @includeWhen(auth()->user()->es('Estudiante') and $form->tipo?->value == 'Alta', 'livewire.movimiento.slots')

    {{-- Admin / Jefe / Coordinador: vista de lectura del movimiento --}}
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
        {{ $move->grupo->materia->nombre_completo }} ({{ $move->grupo->siglas }})

        <x-slot name="footer" class="w-full">
            <div class="grid grid-cols-3">
                <div class="place-self-start">
                    @if ($move->is_paralelo)
                    @include('components.carrera-badge', ['carrera' => $move->user->carreras->first(), 'paralelo' =>
                    $move->carrera])
                    @else
                    @include('components.carrera-badge', ['carrera' => $move->user->carreras->first()])
                    @endif
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

    @if(auth()->user()->es('Administrador'))
    <div>
        <x-select wire:model.defer="form.user_id" id="user_id" name="user_id" label="Estudiante"
            placeholder="Selecciona un estudiante" :async-data="route('api.estudiantes.index')" option-label="username"
            option-description="name" option-value="id" />

        @error('form.user_id')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    @endif

    {{-- Estudiante: mostrar campos para registrar movimiento --}}
    @if ((auth()->user()->es('Estudiante') and (count($form->altas) < $form->max_altas)) or
        auth()->user()->es('Administrador'))
        @if (auth()->user()->es('Estudiante'))
        <div wire:key="materia-container-{{ $form->carrera_id ?: 'none' }}" class="relative rounded-lg"
            wire:loading.class="shadow-[0_0_0_3px_rgba(59,130,246,0.22),0_0_18px_rgba(59,130,246,0.7)] animate-pulse"
            wire:target="form.carrera_id">
            <x-select wire:model.live="form.carrera_id" id="carrera_id" name="carrera_id"
                label="Carrera donde se imparte" placeholder="Selecciona una carrera" searchable
                wire:loading.attr="disabled" wire:target="form.carrera_id" :async-data="route('api.carreras.index')"
                option-label="nombre" option-value="id" :disabled="$form->tipo?->value == 'Baja'" />
            <span class="mt-1 hidden items-center justify-end gap-1 text-xs font-medium text-blue-600" wire:loading.flex
                wire:target="form.carrera_id">
                <x-icon name="spinner-gap" class="h-3.5 w-3.5 animate-spin" />
                Cargando carrera...
            </span>
        </div>
        <div class="relative rounded-lg"
            wire:loading.class="shadow-[0_0_0_3px_rgba(59,130,246,0.22),0_0_18px_rgba(59,130,246,0.7)] animate-pulse"
            wire:target="form.carrera_id,form.materia_id">
            <x-select wire:model.live="form.materia_id" id="materia_id" name="materia_id" label="Materia"
                placeholder="Busca una materia por nombre o clave" searchable wire:loading.attr="disabled"
                wire:target="form.carrera_id,form.materia_id"
                :async-data="route('api.materias.index', ['carrera_id' => $form->carrera_id, 'available' => 1])"
                option-label="nombre_visual" option-value="id" always-fetch :disabled="!$form->carrera_id" />
            <span class="mt-1 hidden items-center justify-end gap-1 text-xs font-medium text-blue-600" wire:loading.flex
                wire:target="form.carrera_id,form.materia_id">
                <x-icon name="spinner-gap" class="h-3.5 w-3.5 animate-spin" />
                Cargando materias...
            </span>
        </div>
        <div class="relative rounded-lg"
            wire:loading.class="shadow-[0_0_0_3px_rgba(59,130,246,0.22),0_0_18px_rgba(59,130,246,0.7)] animate-pulse"
            wire:target="form.carrera_id,form.materia_id">
            <label for="grupo_id" class="block text-sm font-medium text-gray-700">Grupo</label>
            <select wire:model.defer="form.grupo_id" id="grupo_id" name="grupo_id" wire:loading.attr="disabled"
                wire:target="form.carrera_id,form.materia_id" @disabled(!$form->materia_id)
                class="mt-1 block w-full rounded-md border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500
                focus:ring-primary-500 disabled:bg-gray-100 disabled:text-gray-400">
                <option value="">Selecciona un grupo</option>
                @foreach ($form->grupos as $grupo)
                <option value="{{ $grupo['id'] }}">{{ $grupo['siglas'] }}</option>
                @endforeach
            </select>
            <span class="mt-1 hidden items-center justify-end gap-1 text-xs font-medium text-blue-600" wire:loading.flex
                wire:target="form.carrera_id,form.materia_id">
                <x-icon name="spinner-gap" class="h-3.5 w-3.5 animate-spin" />
                Cargando grupos...
            </span>
        </div>
        @else
        <div>
            <x-select wire:model.defer='form.grupo_id' id='grupo_id' name='grupo_id' :label="__('Grupo')"
                placeholder='Selecciona un grupo' :async-data="route('api.grupos.index')" option-label="nombre"
                option-value="id" option-description="materia.carrera.nombre" />
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
            <x-textarea wire:model.live.debounce.150ms='form.motivo_adicional' class='' id="motivo_adicional"
                :label="__('Motivo Adicional')" placeholder='Motivo Adicional' maxlength="200" />
            @php
            $motivoLen = strlen($form->motivo_adicional ?? '');
            $color = match(true) {
            $motivoLen >= 190 => 'text-red-600 font-bold',
            $motivoLen >= 150 => 'text-yellow-600 font-semibold',
            default => 'text-gray-500',
            };
            @endphp
            <div id="motivo_adicional_counter" class="text-xs mt-1 {{ $color }}">
                <span>{{ $motivoLen }}</span> / 200 caracteres
            </div>
        </div>
        @endif

        {{-- Admin / Jefe / Coordinador: mostrar campos para resolver movimiento --}}
        @if (!auth()->user()->es('Estudiante'))
        <div>
            <x-select wire:model.defer='form.respuesta' id='respuesta' name='respuesta' :label="__('Respuesta')"
                placeholder='Selecciona una respuesta rápida'>
                @foreach ($form->respuestas as $respuesta)
                <x-select.option label="{{ $respuesta['value'] }}" value="{{ $respuesta['value'] }}" />
                @endforeach
            </x-select>
        </div>
        <div>
            <x-textarea wire:model.defer='form.respuesta_adicional' id='respuesta_adicional' name='respuesta_adicional'
                class='' :label="__('Respuesta Adicional')" placeholder='Respuesta Adicional' rows="6" />
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