<div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold text-blue-900">Query Monitor</h3>
    <a href="{{ route('query-monitor.report') }}"
      class="text-sm px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
      Ver Reporte Completo
    </a>
  </div>

  @if (app('query-monitor')->getStats())
  <div class="grid grid-cols-3 gap-4 mb-4">
    <div class="bg-white p-3 rounded">
      <p class="text-xs text-gray-600">Total de Queries</p>
      <p class="text-2xl font-bold text-blue-600">{{ app('query-monitor')->getStats()['total_queries'] }}</p>
    </div>
    <div class="bg-white p-3 rounded">
      <p class="text-xs text-gray-600">Tiempo Total</p>
      <p class="text-2xl font-bold text-blue-600">{{ number_format(app('query-monitor')->getStats()['total_time'], 2)
        }}ms</p>
    </div>
    <div class="bg-white p-3 rounded">
      <p class="text-xs text-gray-600">N+1 Issues</p>
      <p
        class="text-2xl font-bold {{ app('query-monitor')->getStats()['n_plus_one_count'] > 0 ? 'text-red-600' : 'text-green-600' }}">
        {{ app('query-monitor')->getStats()['n_plus_one_count'] }}
      </p>
    </div>
  </div>

  @if (app('query-monitor')->getStats()['n_plus_one_count'] > 0)
  <div class="bg-red-50 border border-red-200 rounded p-3">
    <h4 class="font-semibold text-red-900 mb-2">Problemas N+1 Detectados:</h4>
    <pre
      class="text-xs overflow-auto max-h-48">{{ json_encode(app('query-monitor')->getNPlusOneReport(), JSON_PRETTY_PRINT) }}</pre>
  </div>
  @endif
  @else
  <p class="text-sm text-gray-600">No hay datos de monitoreo disponibles. Asegúrate que APP_DEBUG=true</p>
  @endif
</div>