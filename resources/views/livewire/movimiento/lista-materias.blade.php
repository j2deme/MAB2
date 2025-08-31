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
            <p class="mt-2 text-sm text-gray-700">{{ $semestre->nombre_completo ?? 'Todos los semestres' }}</p>
          </div>
          <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
          </div>
        </div>
        <div class="mt-4">
          @foreach ($semestres as $numeroSemestre => $materiasDelSemestre)
          <h1 class="mt-4 mb-4 text-lg font-semibold text-primary-500">Semestre {{ $numeroSemestre }}</h1>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
            @foreach ($materiasDelSemestre as $claveMateria => $coleccionGrupos)
            @php
            // Tomamos el primer grupo para obtener los datos de la materia
            $primerGrupo = $coleccionGrupos->first();
            @endphp
            <a href="{{ route('movimientos.materias.clave', ['clave' => $primerGrupo->clave]) }}">
              <x-card class="border-2 border-{{ $primerGrupo->carrera_color ?? 'gray' }} text-sm">
                <x-slot name="title">
                  {{ $primerGrupo->clave }}
                </x-slot>
                <div class="h-10 -mt-2 text-wrap">
                  <span class="visible md:hidden">
                    {{ $primerGrupo->nombre_completo }}
                  </span>
                  <span class="invisible md:visible">
                    {{ $primerGrupo->nombre }}
                  </span>
                </div>
                <x-slot name="footer">
                  <div class="grid grid-cols-2">
                    <div class="place-self-start">
                      @include('components.carrera-badge', [
                      'carrera' => (object)[
                      'color' => $primerGrupo->carrera_color,
                      'nombre' => $primerGrupo->carrera_nombre,
                      'siglas' => $primerGrupo->carrera_siglas
                      ]
                      ])
                    </div>
                    <div class="place-self-end">
                      <div
                        class="inline-flex items-center justify-center text-sm bg-white border border-gray-300 rounded-full w-7 h-7">
                        @if ($coleccionGrupos->sum('total') > 0)
                        {{ $coleccionGrupos->sum('total') }}
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