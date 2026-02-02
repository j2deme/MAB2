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
            </ul>
            <p class="mt-1">Al utilizar la plataforma, el estudiante acepta las condiciones de uso.</p>
            <p class="mt-1">Para continuar, ingresa al menú <strong>Mis solicitudes</strong>.</p>
          </div>
          @else
          <div class="mb-4 text-lg font-bold">Bienvenido {{ auth()->user()->rol->value }}.</div>

          {{-- DASHBOARD PARA ADMINISTRADOR Y JEFE --}}
          @if(auth()->user()->es(['Administrador', 'Jefe']))
          <div class="mb-8">
            <h2 class="mb-6 text-2xl font-bold text-gray-800">Resumen de movimientos del semestre activo</h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
              <div class="flex flex-col p-6 bg-white shadow rounded-xl">
                <div class="flex items-center mb-4">
                  <x-icon name="arrow-up" class="w-8 h-8 mr-2 text-blue-600" />
                  <span class="text-lg font-semibold text-gray-700">Altas</span>
                </div>
                <div class="mb-2 text-4xl font-bold text-blue-800">{{ $altasTotales ?? '---' }}</div>
                <div class="text-sm text-gray-500">Solicitudes de alta</div>
              </div>
              <div class="flex flex-col p-6 bg-white shadow rounded-xl">
                <div class="flex items-center mb-4">
                  <x-icon name="arrow-down" class="w-8 h-8 mr-2 text-red-600" />
                  <span class="text-lg font-semibold text-gray-700">Bajas</span>
                </div>
                <div class="mb-2 text-4xl font-bold text-red-800">{{ $bajasTotales ?? '---' }}</div>
                <div class="text-sm text-gray-500">Solicitudes de baja</div>
              </div>
              <div class="flex flex-col p-6 bg-white shadow rounded-xl">
                <div class="flex items-center mb-4">
                  <x-icon name="clock" class="w-8 h-8 mr-2 text-yellow-600" />
                  <span class="text-lg font-semibold text-gray-700">Pendientes</span>
                </div>
                <div class="mb-2 text-4xl font-bold text-yellow-800">{{ $pendientesTotales ?? '---' }}</div>
                <div class="text-sm text-gray-500">Solicitudes sin procesar</div>
              </div>
            </div>
          </div>

          <div class="mb-8">
            <h3 class="mb-4 text-lg font-semibold">Análisis de movimientos</h3>
            <div class="flex flex-row gap-6">
              <div class="flex flex-col flex-1 p-6 bg-white shadow rounded-xl">
                <h4 class="mb-4 font-medium text-center text-gray-700">Tipo de Movimientos</h4>
                <div class="flex items-center justify-center flex-1">
                  <canvas id="chart-tipo-movimientos" width="180" height="180"></canvas>
                </div>
                <div class="mt-4 text-sm text-center text-gray-600">
                  Altas vs Bajas
                </div>
              </div>

              <div class="flex flex-col flex-1 p-6 bg-white shadow rounded-xl">
                <h4 class="mb-4 font-medium text-center text-gray-700">Estatus de Solicitudes</h4>
                <div class="flex items-center justify-center flex-1">
                  <canvas id="chart-estatus-solicitudes" width="180" height="180"></canvas>
                </div>
                <div class="mt-4 text-sm text-center text-gray-600">
                  Atendidas vs Pendientes
                </div>
              </div>
            </div>
          </div>

          {{-- TABLA DE RESUMEN POR CARRERA --}}
          <div class="mb-8">
            <h3 class="mb-4 text-lg font-semibold">Resumen por carrera</h3>
            <div class="overflow-x-auto">
              <table class="min-w-full bg-white shadow rounded-xl">
                <thead>
                  <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-sm font-semibold text-left text-gray-700">Carrera</th>
                    <th class="px-4 py-2 text-sm font-semibold text-center text-blue-700">Altas</th>
                    <th class="px-4 py-2 text-sm font-semibold text-center text-red-700">Bajas</th>
                    <th class="px-4 py-2 text-sm font-semibold text-center text-yellow-700">Pendientes</th>
                    <th class="px-4 py-2 text-sm font-semibold text-center text-green-700">Autorizados</th>
                    <th class="px-4 py-2 text-sm font-semibold text-center text-pink-700">Rechazados</th>
                    <th class="px-4 py-2 text-sm font-semibold text-center text-gray-700">Total</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($carrerasResumen as $carrera)
                  <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $carrera->nombre }}</td>
                    <td class="px-4 py-2 font-bold text-center text-blue-800">{{ $carrera->resumen['altas'] }}</td>
                    <td class="px-4 py-2 font-bold text-center text-red-800">{{ $carrera->resumen['bajas'] }}</td>
                    <td class="px-4 py-2 font-bold text-center text-yellow-800">{{ $carrera->resumen['pendientes'] }}
                    </td>
                    <td class="px-4 py-2 font-bold text-center text-green-800">{{ $carrera->resumen['autorizados'] }}
                    </td>
                    <td class="px-4 py-2 font-bold text-center text-pink-800">{{ $carrera->resumen['rechazados'] }}</td>
                    <td class="px-4 py-2 font-bold text-center text-gray-800">{{ $carrera->resumen['total'] }}</td>
                  </tr>
                  @endforeach
                  <!-- Fila de totales -->
                  <tr class="font-bold bg-gray-200">
                    <td class="px-4 py-2 text-sm text-gray-800">TOTALES</td>
                    <td class="px-4 py-2 text-center text-blue-800">{{ $totalesResumen['altas'] }}</td>
                    <td class="px-4 py-2 text-center text-red-800">{{ $totalesResumen['bajas'] }}</td>
                    <td class="px-4 py-2 text-center text-yellow-800">{{ $totalesResumen['pendientes'] }}</td>
                    <td class="px-4 py-2 text-center text-green-800">{{ $totalesResumen['autorizados'] }}</td>
                    <td class="px-4 py-2 text-center text-pink-800">{{ $totalesResumen['rechazados'] }}</td>
                    <td class="px-4 py-2 text-center text-gray-800">{{ $totalesResumen['total'] }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <script>
            function renderDashboardCharts() {
              // Gráfica de tipo de movimientos (Altas vs Bajas)
              const tipoCanvas = document.getElementById('chart-tipo-movimientos');
              if (tipoCanvas) {
                const ctxTipo = tipoCanvas.getContext('2d');
                if (ctxTipo) {
                  if (tipoCanvas.chartInstance) tipoCanvas.chartInstance.destroy();
                  tipoCanvas.chartInstance = new Chart(ctxTipo, {
                    type: 'doughnut',
                    data: {
                      labels: ['Altas', 'Bajas'],
                      datasets: [{
                        label: 'Tipo de movimientos',
                        data: [
                          {{ $altasTotales ?? 0 }},
                          {{ $bajasTotales ?? 0 }}
                        ],
                        backgroundColor: [
                          '#3b82f6', // azul - Altas
                          '#ef4444'  // rojo - Bajas
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                      }]
                    },
                    options: {
                      responsive: true,
                      maintainAspectRatio: false,
                      plugins: {
                        legend: {
                          position: 'bottom',
                          labels: {
                            padding: 15,
                            usePointStyle: true
                          }
                        },
                        tooltip: {
                          callbacks: {
                            label: function(context) {
                              const total = context.dataset.data.reduce((a, b) => a + b, 0);
                              const percentage = Math.round((context.raw / total) * 100);
                              return `${context.label}: ${context.raw} (${percentage}%)`;
                            }
                          }
                        }
                      }
                    }
                  });
                }
              }

              // Gráfica de estatus de solicitudes (Atendidas vs Pendientes)
              const estatusCanvas = document.getElementById('chart-estatus-solicitudes');
              if (estatusCanvas) {
                const ctxEstatus = estatusCanvas.getContext('2d');
                if (ctxEstatus) {
                  if (estatusCanvas.chartInstance) estatusCanvas.chartInstance.destroy();
                  estatusCanvas.chartInstance = new Chart(ctxEstatus, {
                    type: 'doughnut',
                    data: {
                      labels: ['Atendidas', 'Pendientes'],
                      datasets: [{
                        label: 'Estatus de solicitudes',
                        data: [
                          {{ $autorizadosTotales + $rechazadosTotales ?? 0 }},
                          {{ $pendientesTotales ?? 0 }}
                        ],
                        backgroundColor: [
                          '#10b981', // verde - Atendidas
                          '#fbbf24'  // amarillo - Pendientes
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                      }]
                    },
                    options: {
                      responsive: true,
                      maintainAspectRatio: false,
                      plugins: {
                        legend: {
                          position: 'bottom',
                          labels: {
                            padding: 15,
                            usePointStyle: true
                          }
                        },
                        tooltip: {
                          callbacks: {
                            label: function(context) {
                              const total = context.dataset.data.reduce((a, b) => a + b, 0);
                              const percentage = Math.round((context.raw / total) * 100);
                              return `${context.label}: ${context.raw} (${percentage}%)`;
                            }
                          }
                        }
                      }
                    }
                  });
                }
              }
            }

            document.addEventListener('DOMContentLoaded', renderDashboardCharts);

            document.addEventListener('livewire:load', function () {
              renderDashboardCharts();
              if (window.Livewire) {
                // Livewire v3: use message.processed to rerender charts after updates
                window.Livewire.on('message.processed', renderDashboardCharts);
              }
            });
          </script>

          @endif

          {{-- DASHBOARD PARA COORDINADOR --}}
          @if(auth()->user()->es('Coordinador'))
          <div class="mb-8">
            <h2 class="mb-6 text-2xl font-bold text-gray-800">Tus carreras</h2>
            <div class="grid grid-cols-1 gap-6">
              @foreach($carreras as $index => $carrera)
              <div class="p-6 bg-white shadow rounded-xl">
                <h4 class="mb-4 font-bold text-center text-primary-700">{{ $carrera->nombre }}</h4>

                <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-3">
                  <div class="flex flex-col items-center p-4 rounded-lg bg-blue-50">
                    <x-icon name="arrow-up" class="w-8 h-8 mb-2 text-blue-600" />
                    <div class="text-lg font-semibold text-gray-700">Altas</div>
                    <div class="text-2xl font-bold text-blue-800">{{ $carrera->altas ?? '---' }}</div>
                  </div>
                  <div class="flex flex-col items-center p-4 rounded-lg bg-red-50">
                    <x-icon name="arrow-down" class="w-8 h-8 mb-2 text-red-600" />
                    <div class="text-lg font-semibold text-gray-700">Bajas</div>
                    <div class="text-2xl font-bold text-red-800">{{ $carrera->bajas ?? '---' }}</div>
                  </div>
                  <div class="flex flex-col items-center p-4 rounded-lg bg-yellow-50">
                    <x-icon name="clock" class="w-8 h-8 mb-2 text-yellow-600" />
                    <div class="text-lg font-semibold text-gray-700">Pendientes</div>
                    <div class="text-2xl font-bold text-yellow-800">{{ $carrera->pendientes ?? '---' }}</div>
                  </div>
                </div>

                <div class="flex flex-row gap-6">
                  <div class="flex-1 p-4 rounded-lg bg-gray-50">
                    <h5 class="mb-3 text-sm font-medium text-center text-gray-700">Tipo de Movimientos</h5>
                    <div class="flex justify-center">
                      <canvas id="chart-tipo-{{ $carrera->id }}" width="120" height="120"></canvas>
                    </div>
                  </div>

                  <div class="flex-1 p-4 rounded-lg bg-gray-50">
                    <h5 class="mb-3 text-sm font-medium text-center text-gray-700">Estatus de Solicitudes</h5>
                    <div class="flex justify-center">
                      <canvas id="chart-estatus-{{ $carrera->id }}" width="120" height="120"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>

          <script>
            document.addEventListener('DOMContentLoaded', function () {
              @foreach($carreras as $index => $carrera)
                // Gráfica de tipo de movimientos por carrera
                const ctxTipo{{ $index }} = document.getElementById('chart-tipo-{{ $carrera->id }}').getContext('2d');
                new Chart(ctxTipo{{ $index }}, {
                  type: 'doughnut',
                  data: {
                    labels: ['Altas', 'Bajas'],
                    datasets: [{
                      label: 'Tipo de movimientos',
                      data: [
                        {{ $carrera->altas ?? 0 }},
                        {{ $carrera->bajas ?? 0 }}
                      ],
                      backgroundColor: [
                        '#3b82f6', // azul - Altas
                        '#ef4444'  // rojo - Bajas
                      ],
                      borderWidth: 2,
                      borderColor: '#ffffff'
                    }]
                  },
                  options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                      legend: {
                        display: false
                      },
                      tooltip: {
                        callbacks: {
                          label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((context.raw / total) * 100);
                            return `${context.label}: ${context.raw} (${percentage}%)`;
                          }
                        }
                      }
                    }
                  }
                });

                // Gráfica de estatus por carrera
                const ctxEstatus{{ $index }} = document.getElementById('chart-estatus-{{ $carrera->id }}').getContext('2d');
                const atendidasCarrera{{ $index }} = ({{ $carrera->altas ?? 0 }} + {{ $carrera->bajas ?? 0 }}) - ({{ $carrera->pendientes ?? 0 }});
                const pendientesCarrera{{ $index }} = {{ $carrera->pendientes ?? 0 }};
                
                new Chart(ctxEstatus{{ $index }}, {
                  type: 'doughnut',
                  data: {
                    labels: ['Atendidas', 'Pendientes'],
                    datasets: [{
                      label: 'Estatus de solicitudes',
                      data: [
                        atendidasCarrera{{ $index }},
                        pendientesCarrera{{ $index }}
                      ],
                      backgroundColor: [
                        '#10b981', // verde - Atendidas
                        '#fbbf24'  // amarillo - Pendientes
                      ],
                      borderWidth: 2,
                      borderColor: '#ffffff'
                    }]
                  },
                  options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                      legend: {
                        display: false
                      },
                      tooltip: {
                        callbacks: {
                          label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((context.raw / total) * 100);
                            return `${context.label}: ${context.raw} (${percentage}%)`;
                          }
                        }
                      }
                    }
                  }
                });
              @endforeach
            });
          </script>
          @endif

          @endif
        </div>
      </div>
    </div>
  </div>
</x-app-layout>