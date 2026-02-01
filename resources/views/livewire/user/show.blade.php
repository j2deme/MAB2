<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ $user->name }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Información Principal -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="text-base font-semibold leading-6 text-gray-900">Información del Usuario</h1>
                        <p class="mt-2 text-sm text-gray-700">Detalles de cuenta y rol.</p>
                    </div>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        @include('components.back-button', ['url' => route('users.index')])
                    </div>
                </div>

                <div class="flow-root">
                    <div class="mt-8 overflow-x-auto">
                        <div class="inline-block min-w-full py-2 align-middle">
                            <div class="mt-6 border-t border-gray-100">
                                <dl class="divide-y divide-gray-100">

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Nombre</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{
                                            $user->name }}</dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Usuario</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{
                                            $user->username ?? '-' }}</dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Email</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                            <a href="mailto:{{ $user->email }}"
                                                class="text-indigo-600 hover:text-indigo-900">
                                                {{ $user->email }}
                                            </a>
                                        </dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Rol</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                            @php
                                            $roleColors = [
                                            'Administrador' => 'bg-black text-white',
                                            'Jefe' => 'bg-indigo-600 text-white',
                                            'Coordinador' => 'bg-green-600 text-white',
                                            'Estudiante' => 'bg-cyan-600 text-white',
                                            ];
                                            $roleColor = $roleColors[$user->rol->value] ?? 'bg-gray-600 text-white';
                                            @endphp
                                            <span
                                                class="inline-flex items-center rounded-md px-3 py-1 text-sm font-semibold {{ $roleColor }}">
                                                {{ $user->rol->value }}
                                            </span>
                                        </dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Estado de Inscripción
                                        </dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                            @if($user->inscrito)
                                            <span
                                                class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700 ring-1 ring-inset ring-green-600/20">✓
                                                Inscrito</span>
                                            @else
                                            <span
                                                class="inline-flex items-center rounded-full bg-gray-50 px-3 py-1 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-600/20">✗
                                                No Inscrito</span>
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

        <!-- Estadísticas de Movimientos -->
        @if($movimientosEstadisticas['total'] > 0)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Movimientos</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $movimientosEstadisticas['total'] }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                <dt class="text-sm font-medium text-blue-600 truncate">Altas</dt>
                <dd class="mt-1 text-3xl font-semibold text-blue-600">{{ $movimientosEstadisticas['altas'] }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                <dt class="text-sm font-medium text-red-600 truncate">Bajas</dt>
                <dd class="mt-1 text-3xl font-semibold text-red-600">{{ $movimientosEstadisticas['bajas'] }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                <dt class="text-sm font-medium text-blue-600 truncate">Cambios</dt>
                <dd class="mt-1 text-3xl font-semibold text-blue-600">{{ $movimientosEstadisticas['cambios'] }}</dd>
            </div>
        </div>
        @endif

        <!-- Sección de Coordinador/Jefe -->
        @if($user->es(['Coordinador', 'Jefe', 'Administrador']) && $carrerasAsignadas)
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h2 class="text-base font-semibold leading-6 text-gray-900">Carreras Asignadas</h2>
                        <p class="mt-2 text-sm text-gray-700">
                            @if($user->es('Coordinador'))
                            Carreras que coordina.
                            @elseif($user->es('Jefe'))
                            Carreras bajo su supervisión.
                            @else
                            Todas las carreras del sistema.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flow-root mt-8">
                    @if($carrerasAsignadas->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Carrera</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Siglas</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Materias</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($carrerasAsignadas as $carrera)
                                <tr>
                                    <td
                                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        <a href="{{ route('carreras.show', $carrera) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ $carrera->nombre }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{ $carrera->siglas }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{
                                        $carrera->materias_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="rounded-md bg-blue-50 p-4 mt-8">
                        <p class="text-sm text-blue-700">Sin carreras asignadas.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Movimientos Recientes -->
        @if($movimientosRecientes->count() > 0)
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h2 class="text-base font-semibold leading-6 text-gray-900">Movimientos Recientes</h2>
                        <p class="mt-2 text-sm text-gray-700">Últimas acciones registradas.</p>
                    </div>
                </div>

                <div class="flow-root mt-8">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    @if($user->es('Estudiante'))
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Carrera</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Materia</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Grupo</th>
                                    @else
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Estudiante</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Carrera</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Materia</th>
                                    @endif
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Tipo</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Estatus</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($movimientosRecientes as $movimiento)
                                <tr>
                                    @if($user->es('Estudiante'))
                                    <td
                                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        <a href="{{ route('carreras.show', $movimiento->carrera) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ $movimiento->carrera->siglas }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">
                                        <a href="{{ route('materias.show', $movimiento->materia) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ $movimiento->materia->clave }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">
                                        <a href="{{ route('grupos.show', $movimiento->grupo) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ $movimiento->grupo->siglas }}
                                        </a>
                                    </td>
                                    @else
                                    <td
                                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        <a href="{{ route('users.show', $movimiento->user) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ $movimiento->user->name }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">
                                        <a href="{{ route('carreras.show', $movimiento->grupo->materia->carrera) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ $movimiento->grupo->materia->carrera->siglas }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">
                                        <a href="{{ route('materias.show', $movimiento->grupo->materia) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ $movimiento->grupo->materia->clave }}
                                        </a>
                                    </td>
                                    @endif
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        @include('components.movimiento-tipo-icon', ['tipo' =>
                                        $movimiento->tipo->value])
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        @include('components.movimiento-estatus-badge', ['estatus' =>
                                        $movimiento->estatus])
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">
                                        {{ $movimiento->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>