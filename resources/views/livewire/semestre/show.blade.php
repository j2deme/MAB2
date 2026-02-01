<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $semestre->nombre_completo }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Información Principal -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="text-base font-semibold leading-6 text-gray-900">Información del Semestre</h1>
                        <p class="mt-2 text-sm text-gray-700">Detalles y configuración del semestre.</p>
                    </div>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        @include('components.back-button', ['url' => route('semestres.index')])
                    </div>
                </div>

                <div class="flow-root">
                    <div class="mt-8 overflow-x-auto">
                        <div class="inline-block min-w-full py-2 align-middle">
                            <div class="mt-6 border-t border-gray-100">
                                <dl class="divide-y divide-gray-100">

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Clave</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                            <span
                                                class="inline-flex items-center rounded-md bg-blue-50 px-3 py-1 text-sm font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                {{ $semestre->clave }}
                                            </span>
                                        </dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Nombre</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{
                                            $semestre->nombre }}</dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Estado</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                            @if($estado === 'activo')
                                            <span
                                                class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700 ring-1 ring-inset ring-green-600/20">●
                                                ACTIVO</span>
                                            @elseif($estado === 'futuro')
                                            <span
                                                class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">●
                                                PRÓXIMO</span>
                                            @else
                                            <span
                                                class="inline-flex items-center rounded-full bg-gray-50 px-3 py-1 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-600/20">●
                                                FINALIZADO</span>
                                            @endif
                                        </dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Máximo de Altas</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{
                                            $semestre->max_altas }} por estudiante</dd>
                                    </div>

                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Grupos Activos</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $gruposActivos }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Movimientos</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $movimientosTotales }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                <dt class="text-sm font-medium text-blue-600 truncate">Altas</dt>
                <dd class="mt-1 text-3xl font-semibold text-blue-600">{{ $altasTotales }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                <dt class="text-sm font-medium text-red-600 truncate">Bajas</dt>
                <dd class="mt-1 text-3xl font-semibold text-red-600">{{ $bajasTotales }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                <dt class="text-sm font-medium text-orange-600 truncate">Pendientes</dt>
                <dd class="mt-1 text-3xl font-semibold text-orange-600">{{ $pendientesTotales }}</dd>
            </div>
        </div>

        <!-- Timeline de Períodos -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="w-full">
                <h2 class="text-base font-semibold leading-6 text-gray-900 mb-6">Períodos Disponibles</h2>

                <div class="space-y-6">
                    <!-- Período de Altas -->
                    <div class="border-l-4 border-blue-500 pl-4">
                        <div class="flex items-center mb-2">
                            <span
                                class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-semibold text-blue-700 ring-1 ring-inset ring-blue-600/20">ALTAS</span>
                            @if($periodos['inicio_altas'] && $periodos['fin_altas'])
                            @php
                            $daysToStart = now()->diffInDays($periodos['inicio_altas'], false);
                            $daysToEnd = now()->diffInDays($periodos['fin_altas'], false);
                            @endphp
                            @if($now->between($periodos['inicio_altas'], $periodos['fin_altas']))
                            <span class="ml-2 text-sm font-medium text-blue-600">● EN CURSO</span>
                            <span class="ml-2 text-xs text-gray-500">({{ (int)abs($daysToEnd) }} días restantes)</span>
                            @elseif($daysToStart > 0)
                            <span class="ml-2 text-sm font-medium text-gray-500">○ PRÓXIMO</span>
                            <span class="ml-2 text-xs text-gray-500">(Inicia en {{ $daysToStart }} días)</span>
                            @else
                            <span class="ml-2 text-sm font-medium text-gray-500">○ FINALIZADO</span>
                            @endif
                            @endif
                        </div>
                        @if($periodos['inicio_altas'] && $periodos['fin_altas'])
                        <p class="text-sm text-gray-600">
                            {{ $periodos['inicio_altas']->format('d/m/Y') }} - {{
                            $periodos['fin_altas']->format('d/m/Y') }}
                        </p>
                        @else
                        <p class="text-sm text-gray-500">No configurado</p>
                        @endif
                    </div>

                    <!-- Período de Bajas -->
                    <div class="border-l-4 border-red-500 pl-4">
                        <div class="flex items-center mb-2">
                            <span
                                class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-sm font-semibold text-red-700 ring-1 ring-inset ring-red-600/20">BAJAS</span>
                            @if($periodos['inicio_bajas'] && $periodos['fin_bajas'])
                            @php
                            $daysToStart = now()->diffInDays($periodos['inicio_bajas'], false);
                            $daysToEnd = now()->diffInDays($periodos['fin_bajas'], false);
                            @endphp
                            @if($now->between($periodos['inicio_bajas'], $periodos['fin_bajas']))
                            <span class="ml-2 text-sm font-medium text-red-600">● EN CURSO</span>
                            <span class="ml-2 text-xs text-gray-500">({{ (int)abs($daysToEnd) }} días restantes)</span>
                            @elseif($daysToStart > 0)
                            <span class="ml-2 text-sm font-medium text-gray-500">○ PRÓXIMO</span>
                            <span class="ml-2 text-xs text-gray-500">(Inicia en {{ $daysToStart }} días)</span>
                            @else
                            <span class="ml-2 text-sm font-medium text-gray-500">○ FINALIZADO</span>
                            @endif
                            @endif
                        </div>
                        @if($periodos['inicio_bajas'] && $periodos['fin_bajas'])
                        <p class="text-sm text-gray-600">
                            {{ $periodos['inicio_bajas']->format('d/m/Y') }} - {{
                            $periodos['fin_bajas']->format('d/m/Y') }}
                        </p>
                        @else
                        <p class="text-sm text-gray-500">No configurado</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Carreras por Movimientos -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h2 class="text-base font-semibold leading-6 text-gray-900">Top Carreras por Movimientos</h2>
                        <p class="mt-2 text-sm text-gray-700">Carreras con mayor actividad de movimientos en este
                            semestre.</p>
                    </div>
                </div>

                <div class="flow-root mt-8">
                    @if($topCarreras->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Carrera</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Total</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Altas</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Bajas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($topCarreras as $data)
                                <tr>
                                    <td
                                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        <a href="{{ route('carreras.show', $data['carrera']) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ $data['carrera']->siglas }} - {{ $data['carrera']->nombre }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-900">{{
                                        $data['count'] }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-blue-600 font-semibold">{{
                                        $data['altas'] }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-red-600 font-semibold">{{
                                        $data['bajas'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="rounded-md bg-blue-50 p-4 mt-8">
                        <p class="text-sm text-blue-700">No hay movimientos registrados en este semestre.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>