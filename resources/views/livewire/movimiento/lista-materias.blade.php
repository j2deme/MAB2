<x-slot name="header">
  <h2 class="text-xl font-semibold leading-tight text-gray-800">
    Solicitudes por materia
  </h2>
</x-slot>

<div class="py-6">
  <div class="max-w-full mx-auto space-y-6 sm:px-6 lg:px-8">
    <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
      <div class="w-full">
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ __('Solicitudes') }}</h1>
            <p class="mt-2 text-sm text-gray-700">{{ $semestre->nombre_completo }}</p>
          </div>
          <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
          </div>
        </div>
        <div class="mt-4">
          @foreach ($semestres as $key => $sem)
          <h1 class="mt-4 mb-4 text-lg font-semibold text-primary-500">Semestre {{ $key }}</h1>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
            @foreach ($sem as $materia)
            @php
            $move = $materia[0];
            @endphp
            <a href="{{ route('movimientos.materias.clave', ['clave' => $move->grupo->materia->clave]) }}">
              <x-card class="border-2 border-{{ $move->grupo->carrera->color }} text-sm">
                <x-slot name="title">
                  {{ $move->grupo->materia->clave }}
                </x-slot>
                <div class="h-10 -mt-2 text-wrap">
                  <span class="visible md:hidden">
                    {{ $move->grupo->materia->nombre_completo }}
                  </span>
                  <span class="invisible md:visible">
                    {{ $move->grupo->materia->nombre }}
                  </span>
                </div>
                <x-slot name="footer">
                  <div class="grid grid-cols-2">
                    <div class="place-self-start">
                      @include('components.carrera-badge', ['carrera' => $move->grupo->carrera])
                    </div>
                    <div class="place-self-end">
                      {{-- @includeWhen($move->is_paralelo,'components.paralelo-icon', ['paralelo' =>
                      $move->is_paralelo])
                      --}}
                      <div
                        class="inline-flex items-center justify-center text-sm bg-white border border-gray-300 rounded-full w-7 h-7">
                        @if ($move->total > 0)
                        {{ $move->total }}
                        @else
                        <x-icon bold name="check" class="w-5 h-5 text-green-500" />
                        @endif
                      </div>
                    </div>
                  </div>
                </x-slot>
              </x-card>
            </a>
            @endforeach
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>