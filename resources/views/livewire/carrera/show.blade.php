<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $carrera->siglas }} - {{ $carrera->nombre }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Información Principal -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="text-base font-semibold leading-6 text-gray-900">Información de la Carrera</h1>
                        <p class="mt-2 text-sm text-gray-700">Detalles generales.</p>
                    </div>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        @include('components.back-button', ['url' => route('carreras.index')])
                    </div>
                </div>

                <div class="flow-root">
                    <div class="mt-8 overflow-x-auto">
                        <div class="inline-block min-w-full py-2 align-middle">
                            <div class="mt-6 border-t border-gray-100">
                                <dl class="divide-y divide-gray-100">

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Siglas</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                            @include('components.carrera-badge', ['carrera' => $carrera])
                                        </dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Nombre</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{
                                            $carrera->nombre }}</dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Clave Interna</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{
                                            $carrera->clave_interna ?? 'N/A' }}</dd>
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
                <dt class="text-xs font-medium text-gray-500 truncate">Total Materias</dt>
                <dd class="mt-2 text-2xl font-semibold text-gray-900">{{ $totalMaterias }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-4">
                <dt class="text-xs font-medium text-gray-500 truncate">Grupos (Semestre Actual)</dt>
                <dd class="mt-2 text-2xl font-semibold text-gray-900">{{ $totalGruposActivos }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-4">
                <dt class="text-xs font-medium text-gray-500 truncate">Coordinadores</dt>
                <dd class="mt-2 text-2xl font-semibold text-gray-900">{{ $totalCoordinadores }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-4">
                <dt class="text-xs font-medium text-gray-500 truncate">Movimientos Totales</dt>
                <dd class="mt-2 text-2xl font-semibold text-gray-900">{{ $totalMovimientos }}</dd>
            </div>
        </div>

        <!-- Coordinadores -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h2 class="text-base font-semibold leading-6 text-gray-900">Coordinadores Asignados</h2>
                        <p class="mt-2 text-sm text-gray-700">Coordinadores de esta carrera.</p>
                    </div>
                </div>

                <div class="flow-root mt-8">
                    @if($coordinadores->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Nombre</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Email</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Usuario</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($coordinadores as $coord)
                                <tr>
                                    <td
                                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        <a href="{{ route('users.show', $coord) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ $coord->name }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{ $coord->email }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{ $coord->username }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="rounded-md bg-blue-50 p-4 mt-8">
                        <p class="text-sm text-blue-700">No hay coordinadores asignados a esta carrera.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Materias -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h2 class="text-base font-semibold leading-6 text-gray-900">Materias</h2>
                        <p class="mt-2 text-sm text-gray-700">Materias de esta carrera.</p>
                    </div>
                </div>

                <div class="mt-8">
                    <livewire:carreras.materias-table :carreraId="$carreraId" />
                </div>
            </div>
        </div>

        <!-- Movimientos Recientes -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h2 class="text-base font-semibold leading-6 text-gray-900">Movimientos Recientes</h2>
                        <p class="mt-2 text-sm text-gray-700">Últimos movimientos en esta carrera.</p>
                    </div>
                </div>

                <div class="flow-root mt-8">
                    @if($movimientosRecientes->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Estudiante</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Materia</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Grupo</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Tipo</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Estatus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($movimientosRecientes as $mov)
                                <tr>
                                    <td
                                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        {{ $mov->user->name }}
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-700">
                                        <a href="{{ route('materias.show', $mov->grupo->materia) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ $mov->grupo->materia->clave }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">
                                        <a href="{{ route('grupos.show', $mov->grupo) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ $mov->grupo->siglas }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">
                                        @include('components.movimiento-estatus-badge', ['estatus' => $mov->tipo])
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">
                                        @include('components.movimiento-estatus-badge', ['estatus' => $mov->estatus])
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="rounded-md bg-blue-50 p-4 mt-8">
                        <p class="text-sm text-blue-700">No hay movimientos registrados para esta carrera.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>