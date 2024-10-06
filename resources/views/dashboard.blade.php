<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
      {{ __('Dashboard') }}
    </h2>
  </x-slot>

  <div class="py-6">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          @if(auth()->user()->es('Estudiante'))
          <div class="max-w-xl text-justify">
            <p>Bienvenido(a) {{ auth()->user()->name }}.</p>
            <p>Es importante que sepas lo siguiente:</p>
            <ul class="m-3 mt-1 list-disc list-inside">
              <li>La solicitud de movimientos de alta y baja de materias será únicamente en las fechas establecidas.
              </li>
              <li>No se aceptan solicitudes directas con el personal de la División de Estudios Profesionales.</li>
              <li>Los movimientos están sujetos a la capacidad de los cupos, compatibilidad entre materias y carga de
                créditos.</li>
              <li>Las solicitudes que impliquen una carrera distinta están sujetos a disponibilidad de cupo en la
                carrera
                de destino.</li>
              <li>Las solicitudes no son garantía de que el movimiento sea autorizado.</li>
              <li>El estudiante es responsable de monitorear el estatus de su solicitud y realizar lo conducente según
                el
                estatus final de la misma.</li>
              <li>El estatus de las solicitudes se verá reflejado en de 5 a 10 días hábiles.</li>
              <p class="mt-1">Al utilizar la plataforma, el estudiante acepta las condiciones de uso.</p>

              <p class="mt-1">Para continuar, ingresa al menú <strong>Mis solicitudes</strong>.</p>
          </div>
          @else
          Bienvenido {{ auth()->user()->rol->value }}.
          @if (auth()->user()->es('Administrador'))
          {{-- Add a set row of cards to access additional sub apps --}}
          <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            <x-mini-app-card title="Eventos" description="Gestiona los eventos del Tec Valles" route="eventos.index"
              icon='calendar-star' color="blue" />
            <x-mini-app-card title="Actividades" description="Gestiona las actividades de cada evento"
              route="actividades.index" icon="ticket" color="blue" />
            <x-mini-app-card title="Asistencias" description="Gestiona la asistencia a cada actividad"
              route="asistencias.index" icon="calendar-check" color="blue" />
          </div>
          @endif
          @endif
        </div>
      </div>
    </div>
  </div>
</x-app-layout>