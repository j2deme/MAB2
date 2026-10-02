<div class="space-y-6">
    @php
    // Formulario optimizado para Estudiantes: layout a 50% en escritorio
    @endphp

    @if ($form->outOfRange)
    <x-alert title="Fuera de rango" negative>
        @php
        $tipoText = (is_object($form->tipo) && property_exists($form->tipo,'value')) ? $form->tipo->value : (string)($form->tipo ?? '');
        @endphp
        @if($tipoText == 'Alta')
        <p>Fuera de rango para solicitar alta de materias.</p>
        @else
        <p>Fuera de rango para solicitar baja de materias.</p>
        @endif
        <p>Contacta a tu coordinador(a) de carrera para mayor información.</p>
    </x-alert>
    @elseif((is_object($form->tipo) && property_exists($form->tipo,'value') ? $form->tipo->value : (string)($form->tipo ?? '')) == 'Alta' and (count($form->altas) >= $form->max_altas) and !($form->allowAltasOverride))
    <x-alert title="Límite alcanzado" negative>
        <p>Has alcanzado el límite de solicitudes de alta de materias.</p>
        <p>Si necesitar otro movimiento, analiza cual de los movimientos registrados ocupas menos y eliminalo.</p>
    </x-alert>
    @else

    <x-errors />

    {{-- Vista Estudiante: formulario con 2 columnas (50% / 50%) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-1">
            <div class="space-y-4">
                <div wire:key="materia-container-{{ $form->carrera_id ?: 'none' }}" class="relative rounded-lg"
                    wire:loading.class="shadow-[0_0_0_3px_rgba(59,130,246,0.22),0_0_18px_rgba(59,130,246,0.7)] animate-pulse"
                    wire:target="form.carrera_id">
                    <x-select wire:model.live="form.carrera_id" id="carrera_id" name="carrera_id"
                        label="Carrera donde se imparte" placeholder="Selecciona una carrera" class="w-full"
                        searchable wire:loading.attr="disabled" wire:target="form.carrera_id"
                        :async-data="route('api.carreras.index')" option-label="nombre" option-value="id"
                        :disabled="((is_object($form->tipo) && property_exists($form->tipo,'value')) ? $form->tipo->value : (string)($form->tipo ?? '')) == 'Baja'" />
                </div>

                <div class="relative rounded-lg" wire:target="form.carrera_id,form.materia_id">
                    <x-select wire:model.live="form.materia_id" id="materia_id" name="materia_id" class="w-full"
                        label="Materia" placeholder="Busca una materia por nombre o clave" searchable
                        wire:loading.attr="disabled" wire:target="form.carrera_id,form.materia_id"
                        :async-data="route('api.materias.index', ['carrera_id' => $form->carrera_id, 'available' => 1])"
                        option-label="nombre_visual" option-value="id" always-fetch :disabled="!$form->carrera_id" />
                </div>

                <div class="relative rounded-lg" wire:target="form.carrera_id,form.materia_id">
                    <label for="grupo_id" class="block text-sm font-medium text-gray-700">Grupo</label>
                    <select wire:model.defer="form.grupo_id" id="grupo_id" name="grupo_id"
                        wire:loading.attr="disabled" wire:target="form.carrera_id,form.materia_id"
                        @disabled(!$form->materia_id)
                        class="mt-1 block w-full rounded-md border-gray-300 bg-white text-sm shadow-sm
                        focus:border-primary-500 focus:ring-primary-500 disabled:bg-gray-100 disabled:text-gray-400">
                        <option value="">Selecciona un grupo</option>
                        @foreach ($form->grupos as $grupo)
                        <option value="{{ $grupo['id'] }}">{{ $grupo['siglas'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-select wire:model.defer='form.motivo' id='motivo' name='motivo' class="w-full"
                        :label="__('Motivo')" placeholder='Selecciona un motivo'>
                        @foreach ($form->motivos as $motivo)
                        @php
                        $mValue = is_array($motivo) ? $motivo['value'] : (is_object($motivo) && property_exists($motivo,'value') ? $motivo->value : $motivo);
                        $mLabel = is_array($motivo) ? ($motivo['label'] ?? $motivo['name']) : (is_object($motivo) && property_exists($motivo,'value') ? $motivo->value : $motivo);
                        @endphp
                        <x-select.option label="{{ $mLabel }}" value="{{ $mValue }}" />
                        @endforeach
                    </x-select>
                </div>

                <div>
                    <x-textarea wire:model.live.debounce.150ms='form.motivo_adicional' class='w-full'
                        id="motivo_adicional" :label="__('Motivo Adicional')" placeholder='Motivo Adicional'
                        maxlength="200" />
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

                <div class="flex items-center gap-4">
                    <x-primary-button>
                        <x-icon name="floppy-disk" class="w-4 h-4 mr-2" />
                        {{ __('Guardar') }}
                    </x-primary-button>

                    <x-link label="Cancelar" :href="route('movimientos.index')" />
                </div>
            </div>
        </div>

        <div class="md:col-span-1">
            @if($form->student_name)
            <x-card>
                <h3 class="text-base font-semibold">Datos del estudiante</h3>
                <div class="mt-2 text-sm text-gray-700">
                    <div><strong>Nombre:</strong> {{ $form->student_name }}</div>
                    <div><strong>Usuario:</strong> {{ $form->student_username }}</div>
                    <div><strong>Carreras:</strong> {{ implode(', ', $form->student_carreras) }}</div>
                </div>
            </x-card>
            <div class="mt-4">
                @includeWhen($form->movimientos && count($form->movimientos) > 0, 'livewire.movimiento.slots')
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
