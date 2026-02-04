<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $grupo->siglas }} - {{ $grupo->materia->nombre }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
        @if($grupo->deleted_at)
        <div class="rounded-md bg-red-50 p-4 border border-red-200">
            <p class="text-sm text-red-700">Este grupo está <strong>eliminado (soft-deleted)</strong>. Los movimientos
                asociados pueden estar también eliminados y se muestran con su estatus.</p>
        </div>
        @endif
        <!-- Información Principal -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="text-base font-semibold leading-6 text-gray-900">Información del Grupo</h1>
                        <p class="mt-2 text-sm text-gray-700">Contexto e información general del grupo.</p>
                    </div>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        @include('components.back-button', ['url' => route('grupos.index')])
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
                                            <span
                                                class="inline-flex items-center rounded-md bg-blue-50 px-3 py-1 text-sm font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                {{ $grupo->siglas }}
                                            </span>
                                        </dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Materia</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                            <a href="{{ route('materias.show', $grupo->materia) }}"
                                                class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                                {{ $grupo->materia->clave }} - {{ $grupo->materia->nombre }}
                                            </a>
                                        </dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Carrera</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                            <a href="{{ route('carreras.show', $grupo->materia->carrera) }}"
                                                class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                                {{ $grupo->materia->carrera->siglas }} - {{
                                                $grupo->materia->carrera->nombre }}
                                            </a>
                                        </dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Semestre</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                            <a href="{{ route('semestres.show', $grupo->semestre) }}"
                                                class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                                {{ $grupo->semestre->nombre }}
                                            </a>
                                        </dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Disponible</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                            @include('components.disponible-icon', ['disponible' =>
                                            $grupo->is_disponible])
                                        </dd>
                                    </div>

                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm font-medium leading-6 text-gray-900">Paralelizable</dt>
                                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                            @include('components.paralelo-icon', ['paralelo' =>
                                            $grupo->is_paralelizable])
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
                <dd class="mt-2 text-2xl font-semibold text-gray-900">{{ $totalEstudiantes }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-4">
                <dt class="text-xs font-medium text-gray-500 truncate">Movimientos Totales</dt>
                <dd class="mt-2 text-2xl font-semibold text-gray-900">{{ $totalMovimientos }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-4">
                <dt class="text-xs font-medium text-blue-600 truncate">Altas</dt>
                <dd class="mt-2 text-2xl font-semibold text-blue-600">{{ $altasAprobadas }}</dd>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg p-4">
                <dt class="text-xs font-medium text-red-600 truncate">Bajas</dt>
                <dd class="mt-2 text-2xl font-semibold text-red-600">{{ $bajasAprobadas }}</dd>
            </div>
        </div>

        <!-- Solicitudes de Movimientos -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h2 class="text-base font-semibold leading-6 text-gray-900">Movimientos de Estudiantes</h2>
                        <p class="mt-2 text-sm text-gray-700">Solicitudes de altas y bajas registradas en este grupo.
                            Mostrando primeros {{ $movimientos->count() }} de {{ $totalMovimientos }}.</p>
                    </div>
                </div>

                <div class="flow-root mt-8">
                    @if($movimientos->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Estudiante</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Tipo</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Estatus</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($movimientos as $mov)
                                <tr>
                                    <td
                                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        <a href="{{ route('users.show', $mov->user) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ $mov->user->name }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        @include('components.movimiento-tipo-icon', ['tipo' => $mov->tipo->value])
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        @if($mov->deleted_at)
                                        <x-badge color="red" label="Borrado" sm />
                                        @else
                                        @include('components.movimiento-estatus-badge', ['estatus' => $mov->estatus])
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">
                                        {{ $mov->updated_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($totalMovimientos > $movimientos->count())
                    <div class="mt-4 p-3 bg-blue-50 rounded-md border border-blue-200">
                        <p class="text-sm text-blue-700">
                            Mostrando {{ $movimientos->count() }} de {{ $totalMovimientos }} movimientos.
                            <button type="button" wire:click="mostrarTodos"
                                class="font-semibold hover:underline text-blue-700">
                                Ver todos
                            </button>
                        </p>
                    </div>
                    @endif
                    @else
                    <div class="rounded-md bg-blue-50 p-4 mt-8">
                        <p class="text-sm text-blue-700">No hay movimientos registrados para este grupo.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>