<div class="py-4">
  <div class="max-w-full mx-auto space-y-6 sm:px-6 lg:px-8">
    <div class="w-full">
      {{-- removed page-like header and back button for modal presentation --}}

      @php
      use App\Enums\MovesStatus;
      $statusColor = $movimiento->estatus?->color() ?? 'gray';
      $showRespuesta = in_array($movimiento->estatus, [
      MovesStatus::AUTORIZADO,
      MovesStatus::AUTORIZADO_JEFE,
      MovesStatus::RECHAZADO,
      MovesStatus::RECHAZADO_JEFE,
      ], true) || (
      $movimiento->estatus === MovesStatus::REVISION && (
      !empty($movimiento->respuesta) || !Str($movimiento->respuesta_adicional)->isEmpty()
      )
      );
      @endphp

      <div class="flow-root">
        {{-- expose status color for the outer modal header via JS (hidden) --}}
        <div id="movimiento-status-meta" data-status="{{ $statusColor }}" class="hidden"></div>
        <div class="mt-2">
          <div class="w-full py-2">
            <div
              class="w-full border-2 border-{{ $movimiento->grupo->carrera->color ?? 'gray' }} rounded-md p-4 text-sm">
              <div class="grid grid-cols-2 mb-2">
                <div class="place-self-start text-sm font-medium">{{ $movimiento->grupo->materia->clave ?? '' }}</div>
                <div class="place-self-end text-sm">{{ $movimiento->user->username ?? '' }}</div>
              </div>
              <div class="text-base font-semibold mb-2">{{ $movimiento->grupo->materia->nombre_completo ?? '' }}</div>
              <div class="flex items-center justify-between mt-4">
                <div>
                  @if ($movimiento->is_paralelo)
                  @include('components.carrera-badge', ['carrera' => $movimiento->user->carreras->first(), 'paralelo' =>
                  $movimiento->carrera])
                  @else
                  @include('components.carrera-badge', ['carrera' => $movimiento->user->carreras->first()])
                  @endif
                </div>
                <div class="flex items-center space-x-4">
                  @include('components.movimiento-tipo-icon', ['tipo' => $movimiento->tipo->value])
                  @includeWhen($movimiento->is_paralelo,'components.paralelo-icon',['paralelo' =>
                  $movimiento->is_paralelo])
                </div>
              </div>
            </div>

            <h2 class="mt-4 text-base font-semibold leading-6 text-gray-900">Motivo</h2>
            <div class="mt-2 w-full bg-gray-50 border border-gray-100 rounded shadow-sm p-4">
              <p class="text-sm font-semibold mb-2">{{ $movimiento->motivo }}</p>
              @if (!Str($movimiento->motivo_adicional)->isEmpty())
              <p class="text-sm text-gray-700">{{ $movimiento->motivo_adicional }}</p>
              @endif
            </div>

            @if($showRespuesta)
            <h2 class="mt-4 text-base font-semibold leading-6 text-gray-900">Respuesta</h2>
            <div class="mt-2 w-full border-2 border-{{ $statusColor }}-300 rounded-md p-4 bg-{{ $statusColor }}-50">
              <div class="flex items-center justify-between mb-3">
                <div class="text-sm font-medium">&nbsp;</div>
                <div>
                  @include('components.movimiento-estatus-badge', ['estatus' => $movimiento->estatus])
                </div>
              </div>
              {{-- Mostrar la respuesta principal si existe --}}
              @if (!empty($movimiento->respuesta))
              <p class="text-sm text-gray-900 font-semibold mb-2">{{ $movimiento->respuesta }}</p>
              @endif
              {{-- Mostrar la respuesta adicional (markdown) si existe --}}
              @if (!Str($movimiento->respuesta_adicional)->isEmpty())
              <div class="prose prose-sm max-w-none text-gray-700">{!! Str::markdown($movimiento->respuesta_adicional)
                !!}</div>
              @endif
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>