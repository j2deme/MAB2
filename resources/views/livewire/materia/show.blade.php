<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $materia->clave }} - {{ $materia->nombre_completo }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Información Principal -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="text-base font-semibold leading-6 text-gray-900">Información de la Materia</h1>
                        <p class="mt-2 text-sm text-gray-700">Detalles generales y académicos.</p>
                    </div>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        @include('components.back-button', ['url' => route('materias.index')])
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
                                            @php
                                            $colorMap = [
                                            'red' => '#ef4444',
                                            'blue' => '#3b82f6',
                                            'green' => '#10b981',
                                            'yellow' => '#f59e0b',
                                            'purple' => '#a855f7',
                                            'pink' => '#ec4899',
                                            'indigo' => '#6366f1',
                                            'cyan' => '#06b6d4',
                                            ];
                                            $colorName = preg_replace('/[^a-zA-Z]/', '', $materia->carrera->color);
                                            $colorHex = $colorMap[strtolower($colorName)] ?? '#6b7280';
                                            @endphp
                                            <span
                                                class="inline-flex items-center rounded-md px-2 py-1 text-sm font-medium text-white ring-1 ring-inset"
                                                style="background-color: {{ $colorHex }}; border-color: {{ $colorHex }};">
                                                {{ $materia->clave }}
                                            </span>
                                        </dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Nombre</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{
                                            $materia->nombre }}</dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Nombre Completo</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{
                                            $materia->nombre_completo }}</dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Carrera</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                            <a href="{{ route('carreras.show', $materia->carrera) }}">
                                                @include('components.carrera-badge', ['carrera' => $materia->carrera])
                                            </a>
                                        </dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Semestre</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{
                                            $materia->semestre }}</dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">SATCA</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                            <div class="flex gap-2">
                                                <span
                                                    class="inline-flex items-center rounded-md bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300">
                                                    Ht: <span class="font-semibold ml-1">{{ $materia->ht }}</span>
                                                </span>
                                                <span
                                                    class="inline-flex items-center rounded-md bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300">
                                                    Hp: <span class="font-semibold ml-1">{{ $materia->hp }}</span>
                                                </span>
                                                <span
                                                    class="inline-flex items-center rounded-md bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300">
                                                    Cr: <span class="font-semibold ml-1">{{ $materia->cr }}</span>
                                                </span>
                                            </div>
                                        </dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Estado</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                            @if($materia->activo)
                                            <span
                                                class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700 ring-1 ring-inset ring-green-600/20">✓
                                                Activa</span>
                                            @else
                                            <span
                                                class="inline-flex items-center rounded-full bg-gray-50 px-3 py-1 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-600/20">○
                                                Inactiva</span>
                                            @endif
                                        </dd>
                                    </div>

                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white overflow-hidden shadow rounded-lg p-4">
                <dt class="text-xs font-medium text-gray-500 truncate">Estudiantes Registrados</dt>
                <dd class="mt-2 text-2xl font-semibold text-gray-900">{{ $estudiantesRegistrados }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-4">
                <dt class="text-xs font-medium text-gray-500 truncate">Movimientos Totales</dt>
                <dd class="mt-2 text-2xl font-semibold text-gray-900">{{ $movimientosTotales }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-4">
                <dt class="text-xs font-medium text-blue-600 truncate">Altas</dt>
                <dd class="mt-2 text-2xl font-semibold text-blue-600">{{ $altasTotales }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-4">
                <dt class="text-xs font-medium text-red-600 truncate">Bajas</dt>
                <dd class="mt-2 text-2xl font-semibold text-red-600">{{ $bajasTotales }}</dd>
            </div>
        </div>

        <!-- Grupos en Semestre Activo -->
        @if($semestreActivo)
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h2 class="text-base font-semibold leading-6 text-gray-900">Grupos en {{ $semestreActivo->nombre
                            }}</h2>
                        <p class="mt-2 text-sm text-gray-700">Grupos de esta materia en el semestre activo con
                            estadísticas de movimientos.</p>
                    </div>
                </div>

                <div class="flow-root mt-8">
                    @if($gruposConEstudiantes->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Siglas</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Estudiantes</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Altas/Bajas</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Disponible</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($gruposConEstudiantes as $data)
                                <tr>
                                    <td
                                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        <a href="{{ route('grupos.show', $data['grupo']) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ $data['grupo']->siglas }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{
                                        $data['estudiantes'] }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold">
                                        <span class="text-blue-600">{{ $data['altas'] }}</span> / <span
                                            class="text-red-600">{{ $data['bajas'] }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm flex gap-2">
                                        @include('components.disponible-icon', ['disponible' =>
                                        $data['grupo']->is_disponible])
                                        @include('components.paralelo-icon', ['paralelo' =>
                                        $data['grupo']->is_paralelizable])
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="rounded-md bg-blue-50 p-4 mt-8">
                        <p class="text-sm text-blue-700">No hay grupos para esta materia en el semestre {{
                            $semestreActivo->nombre }}.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @else
        <div class="rounded-md bg-yellow-50 p-4">
            <p class="text-sm text-yellow-700">No hay semestre activo actualmente.</p>
        </div>
        @endif
    </div>
</div>