<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ __('Actividades') }}
    </h2>
</x-slot>

<div class="py-6">
    <div class="max-w-full mx-auto space-y-6 sm:px-6 lg:px-8">
        <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="text-base font-semibold leading-6 text-gray-900">{{ __('Actividades') }}</h1>
                        <p class="mt-2 text-sm text-gray-700">Actividades del evento activo.</p>
                    </div>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        <x-primary-button wire:navigate href="{{ route('actividades.create') }}" class="">
                            <x-icon name="plus" class="w-4 h-4 mr-2" />
                            {{ __('Add') }} {{ __('actividad') }}
                        </x-primary-button>
                    </div>
                </div>

                <div class="flow-root">
                    <div class="mt-8 overflow-x-auto">
                        <div class="inline-block min-w-full py-2 align-middle">
                            <table class="w-full divide-y divide-gray-300">
                                <thead>
                                    <tr class="even:bg-gray-300">
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Clave</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Nombre</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Fecha Inicio</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Fecha Fin</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Activo</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Tipo</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Modalidad</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Magistral</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Duración</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Evento</th>

                                        <th scope="col"
                                            class="px-3 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($actividades as $actividad)
                                    <tr class="even:bg-gray-50" wire:key="{{ $actividad->id }}">
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                                            $actividad->clave }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 text-wrap">{{
                                            $actividad->nombre }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                                            $actividad->fecha_inicio }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                                            $actividad->fecha_fin }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                                            $actividad->is_activo ? 'Sí' : 'No' }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                                            $actividad->tipo }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                                            $actividad->modalidad }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                                            $actividad->is_magistral ? 'Sí' : 'No' }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                                            $actividad->duracion }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                                            $actividad->evento->nombre }}</td>

                                        <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            <a wire:navigate href="{{ route('actividades.show', $actividad->id) }}"
                                                class="mr-2 font-bold text-gray-600 hover:text-gray-900">{{ __('Show')
                                                }}</a>
                                            <a wire:navigate href="{{ route('actividades.edit', $actividad->id) }}"
                                                class="mr-2 font-bold text-indigo-600 hover:text-indigo-900">{{
                                                __('Edit') }}</a>
                                            <button class="font-bold text-red-600 hover:text-red-900" type="button"
                                                wire:click="delete({{ $actividad->id }})"
                                                wire:confirm="¿Está seguro de borrar la actividad? No podrá recuperarla">
                                                {{ __('Delete') }}
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="px-4 mt-4">
                                {!! $actividades->withQueryString()->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>