<x-slot name="header">
  <h2 class="text-xl font-semibold leading-tight text-gray-800">
    {{ Str::title($movimiento->nombre) ?? __('Show') . " " . __('Solicitud') }}
  </h2>
</x-slot>

<div class="py-6">
  <div class="max-w-full mx-auto space-y-6 sm:px-6 lg:px-8">
    <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
      <div class="w-full">
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">Solicitud</h1>
            <p class="mt-2 text-sm text-gray-700">{{ $semestre->nombre_completo }}</p>
          </div>
          <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            @include('components.back-button')
            {{-- , ['url' => route('movimientos.index')] --}}
          </div>
        </div>

        <div class="flow-root">
          <div class="mt-8 overflow-x-auto">
            <div class="inline-block w-1/2 py-2 align-middle">
              <x-card class="border-2 border-{{ $movimiento->grupo->carrera->color }} text-sm" shadow="md">
                <x-slot name="title" class="w-full">
                  <div class="grid grid-cols-2">
                    <div class="place-self-start">
                      {{ $movimiento->grupo->materia->clave }}
                    </div>
                    <div class="place-self-end">
                      {{ $movimiento->user->username }}
                    </div>
                  </div>
                </x-slot>
                {{ $movimiento->grupo->materia->nombre_completo }} ({{ $movimiento->grupo->siglas }})

                <x-slot name="footer" class="w-full">
                  <div class="grid grid-cols-3">
                    <div class="place-self-start">
                      @if ($movimiento->is_paralelo)
                      @include('components.carrera-badge', ['carrera' => $movimiento->user->carreras->first(),
                      'paralelo' =>
                      $movimiento->carrera])
                      @else
                      @include('components.carrera-badge', ['carrera' => $movimiento->user->carreras->first()])
                      @endif
                    </div>
                    <div class="place-self-center">
                      @include('components.movimiento-tipo-icon', ['tipo' =>
                      $movimiento->tipo->value])
                    </div>
                    <div class="place-self-end">
                      @includeWhen($movimiento->is_paralelo,'components.paralelo-icon',
                      ['paralelo' =>
                      $movimiento->is_paralelo])
                    </div>
                  </div>
                </x-slot>
              </x-card>

              <h2 class="mt-8 text-base font-semibold leading-6 text-gray-900">Motivo</h2>
              <x-card title="{{ $movimiento->motivo }}" shadow="md" class="mt-4">
                @if (!Str($movimiento->motivo_adicional)->isEmpty())
                <p class="text-sm">{{ $movimiento->motivo_adicional }}</p>
                @endif
              </x-card>

              <h2 class="mt-8 text-base font-semibold leading-6 text-gray-900">Respuesta</h2>
              <x-card shadow="md" class="mt-4 border-2 border-{{ $movimiento->estatus->color() }}-500">
                <x-slot name="title" class="w-full">
                  <div class="grid grid-cols-2">
                    <div class="place-self-start">
                      {{ $movimiento->respuesta }}
                    </div>
                    <div class="place-self-end">
                      @include('components.movimiento-estatus-badge', ['estatus' => $movimiento->estatus])
                    </div>
                  </div>
                </x-slot>

                @if (!Str($movimiento->respuesta_adicional)->isEmpty())
                <p class="text-sm">{{ $movimiento->respuesta_adicional }}</p>
                @endif
              </x-card>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>