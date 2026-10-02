<div class="space-y-6">
    @php
    // Formulario optimizado para Admin / Jefe:
    @endphp

    <x-errors />

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <div class="md:col-span-1">
            <div class="space-y-4">
                <div>
                    {{-- Lista vía api.estudiantes.index, que con el middleware web acota
                         la consulta al rol del usuario (el Coordinador sólo ve
                         estudiantes de sus carreras). --}}
                    <x-select wire:model.live="form.user_id" id="user_id" name="user_id" class="w-full"
                        label="Estudiante" placeholder="Selecciona un estudiante"
                        :async-data="route('api.estudiantes.index')" option-label="username" option-description="name"
                        option-value="id" />
                </div>

                <div>
                    <x-select wire:model.live="form.tipo" id="tipo" name="tipo" class="w-full"
                        label="Tipo de movimiento" placeholder="Selecciona tipo">
                        @foreach($form->tipos as $t)
                        @php
                        $tValue = is_array($t) ? $t['value'] : (is_object($t) && property_exists($t,'value') ? $t->value
                        : $t);
                        $tLabel = is_array($t) ? ($t['label'] ?? $t['name']) : (is_object($t) && property_exists($t,'value') ? $t->value
                        : $t);
                        @endphp
                        <x-select.option label="{{ $tLabel }}" value="{{ $tValue }}" />
                        @endforeach
                    </x-select>
                </div>

                <div>
                    <x-select wire:model.live="form.carrera_id" id="carrera_id" name="carrera_id" class="w-full"
                        label="Carrera donde se imparte" placeholder="Selecciona una carrera" searchable
                        wire:loading.attr="disabled" wire:target="form.carrera_id" :options="$form->carreras"
                        option-label="nombre" option-value="id" :disabled="((is_object($form->tipo) && property_exists($form->tipo,'value')) ? $form->tipo->value : (string)($form->tipo ?? '')) == 'Baja'" />
                </div>

                <div>
                    <x-select wire:model.live="form.materia_id" id="materia_id" name="materia_id" class="w-full"
                        label="Materia" placeholder="Busca una materia por nombre o clave" searchable
                        wire:loading.attr="disabled" wire:target="form.carrera_id,form.materia_id"
                        :async-data="route('api.materias.index', ['carrera_id' => $form->carrera_id, 'available' => 1])"
                        option-label="nombre_visual" option-value="id" always-fetch :disabled="!$form->carrera_id" />
                </div>

                <div>
                    <label for="grupo_id" class="block text-sm font-medium text-gray-700">Grupo</label>
                    <select wire:model.defer="form.grupo_id" id="grupo_id" name="grupo_id" wire:loading.attr="disabled"
                        wire:target="form.carrera_id,form.materia_id" @disabled(!$form->materia_id)
                        class="block w-full mt-1 text-sm bg-white border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 disabled:bg-gray-100 disabled:text-gray-400">
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
                        $mValue = is_array($motivo) ? $motivo['value'] : (is_object($motivo) &&
                        property_exists($motivo,'value') ? $motivo->value : $motivo);
                        $mLabel = is_array($motivo) ? ($motivo['label'] ?? $motivo['name']) : (is_object($motivo) &&
                        property_exists($motivo,'value') ? $motivo->value : $motivo);
                        @endphp
                        <x-select.option label="{{ $mLabel }}" value="{{ $mValue }}" />
                        @endforeach
                    </x-select>
                </div>

                <div>
                    <x-textarea wire:model.live.debounce.150ms='form.motivo_adicional' class='w-full'
                        id="motivo_adicional" :label="__('Motivo Adicional')" placeholder='Motivo Adicional'
                        maxlength="200" />
                </div>

                <div class="mt-4">
                    <div class="flex items-center gap-4">
                        <x-primary-button>
                            <x-icon name="floppy-disk" class="w-4 h-4 mr-2" />
                            {{ __('Guardar') }}
                        </x-primary-button>

                        <x-link label="Cancelar" :href="route('movimientos.index')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4 md:col-span-3" wire:loading.class="opacity-50 pointer-events-none">
            @if($form->student_name)
            <x-card>
                <h3 class="text-base font-semibold">Datos del estudiante</h3>
                <div class="mt-2 text-sm text-gray-700">
                    <div><strong>Nombre:</strong> {{ $form->student_name }}</div>
                    <div><strong>Número de control:</strong> {{ $form->student_username }}</div>
                    <div><strong>Carrera:</strong> {{ implode(', ', $form->student_carreras) }}</div>
                </div>
            </x-card>
            <div class="mt-4">
                @includeWhen($form->movimientos && count($form->movimientos) > 0, 'livewire.movimiento.slots_mini')
            </div>
            @else
            <div class="p-4 rounded bg-gray-50">
                <h3 class="text-sm font-medium">Datos del estudiante</h3>
                <div class="mt-2 text-sm text-gray-700">
                    <div><strong>Nombre:</strong> -</div>
                    <div><strong>Número de control:</strong> -</div>
                    <div><strong>Carrera:</strong> -</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
