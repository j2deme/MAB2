<x-slot name="header">
  <h2 class="text-xl font-semibold leading-tight text-gray-800">
    Solicitudes por generación
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
          @foreach ($generaciones as $key => $generacion)
          <h1 class="mt-4 mb-4 text-lg font-semibold text-primary-500">Generación {{ $key }}</h1>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
            @foreach ($generacion as $estudiante)
            <a href="{{ route('movimientos.generacion.estudiante', ['estudiante' => $estudiante->username]) }}">
              <x-card class="border-2 border-{{ $estudiante->carreras[0]->color }} text-sm">
                <x-slot name="title">
                  {{ $estudiante->username }}
                </x-slot>
                <div class="h-10 -mt-2 text-wrap">
                  {{ $estudiante->name }}
                </div>
                <x-slot name="footer">
                  <div class="grid grid-cols-2">
                    <div class="place-self-start">
                      @include('components.carrera-badge', ['carreras' => $estudiante->carreras])
                    </div>
                    <div class="place-self-end">
                      <div
                        class="inline-flex items-center justify-center text-sm bg-white border border-gray-300 rounded-full w-7 h-7">
                        @if ($estudiante->total > 0)
                        {{ $estudiante->total }}
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