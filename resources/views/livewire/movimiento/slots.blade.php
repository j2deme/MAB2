<div>
  <h2 class="text-lg font-bold dark:text-white text-primary-500">Espacios disponibles para altas</h2>
  <p class="py-2">Recuerda que tienes un máximo de solicitudes de alta de materia</p>
  <div class="grid grid-cols-1 gap-2 md:grid-cols-5">
    @for ($i = 0; $i < $form->max_altas; $i++)
      @if (isset($form->altas[$i]))
      @php
      $move = $form->altas[$i];
      @endphp
      <x-card class="border-2 border-{{ $move->grupo->carrera->color }} text-sm">
        <x-slot name="title">
          {{ $move->grupo->materia->clave }}
        </x-slot>
        <span class="visible md:hidden">
          {{ $move->grupo->materia->nombre_completo }} {{ $move->grupo->siglas }}
        </span>
        <span class="invisible md:visible">
          {{ $move->grupo->nombre_corto }}
        </span>

        <x-slot name="footer">
          <div class="grid grid-cols-2">
            <div class="place-self-start">
              @include('components.carrera-badge', ['carrera' => $move->grupo->carrera])
            </div>
            <div class="place-self-end">
              @includeWhen($move->is_paralelo,'components.paralelo-icon', ['paralelo' =>
              $move->is_paralelo])
            </div>
          </div>
        </x-slot>
      </x-card>
      @else
      <div
        class="justify-between flex-grow w-full p-6 text-center bg-gray-200 border border-gray-300 rounded-lg shadow align-content-between hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
          {{ $i + 1 }}
        </h5>
      </div>
      @endif
      @endfor
  </div>
</div>