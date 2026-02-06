<div>
  @if(($open ?? false) && ($movimiento ?? false))
  <div class="fixed inset-0 z-50 flex items-start justify-center pt-12">
    <div class="fixed inset-0 bg-black opacity-50" wire:click="close"></div>
    <div class="relative w-full max-w-4xl mx-4 bg-white rounded-lg shadow-lg overflow-auto max-h-[85vh]">
      <div class="flex items-center justify-between p-4 border-b">
        <h3 class="text-lg font-semibold">Solicitud: {{ $movimiento->grupo->materia->nombre_completo ?? '' }}</h3>
        <div class="flex items-center space-x-2">
          <button wire:click="close" class="px-3 py-1 text-sm text-gray-700 rounded hover:bg-gray-100">Cerrar</button>
        </div>
      </div>

      <div class="p-6">
        <div class="grid grid-cols-1 gap-6">
          <div>
            <x-card class="border-2 border-{{ $movimiento->grupo->carrera->color ?? 'gray' }} text-sm" shadow="md">
              <x-slot name="title" class="w-full">
                <div class="grid grid-cols-2">
                  <div class="place-self-start">
                    {{ $movimiento->grupo->materia->clave ?? '' }}
                  </div>
                  <div class="place-self-end">
                    {{ $movimiento->user->username ?? '' }}
                  </div>
                </div>
              </x-slot>

              {{ $movimiento->grupo->materia->nombre_completo ?? '' }} ({{ $movimiento->grupo->siglas ?? '' }})

              <x-slot name="footer" class="w-full">
                <div class="grid grid-cols-3">
                  <div class="place-self-start">
                    @if ($movimiento->is_paralelo)
                    @include('components.carrera-badge', ['carrera' => $movimiento->user->carreras->first(), 'paralelo'
                    =>
                    $movimiento->carrera])
                    @else
                    @include('components.carrera-badge', ['carrera' => $movimiento->user->carreras->first()])
                    @endif
                  </div>
                  <div class="place-self-center">
                    @include('components.movimiento-tipo-icon', ['tipo' => $movimiento->tipo->value])
                  </div>
                  <div class="place-self-end">
                    @includeWhen($movimiento->is_paralelo,'components.paralelo-icon',['paralelo' =>
                    $movimiento->is_paralelo])
                  </div>
                </div>
              </x-slot>
            </x-card>
          </div>

          <div>
            <h2 class="text-base font-semibold leading-6 text-gray-900">Motivo</h2>
            <x-card title="{{ $movimiento->motivo }}" shadow="md" class="mt-2">
              @if (!empty($movimiento->motivo_adicional))
              <p class="text-sm">{{ $movimiento->motivo_adicional }}</p>
              @endif
            </x-card>
          </div>

          <div>
            <h2 class="text-base font-semibold leading-6 text-gray-900">Respuesta</h2>
            <x-card shadow="md" class="mt-2 border-2 border-{{ $movimiento->estatus->color() ?? 'gray' }}-500">
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

              @if (!empty($movimiento->respuesta_adicional))
              <p class="text-sm">{!! Str::markdown($movimiento->respuesta_adicional) !!}</p>
              @endif
            </x-card>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>