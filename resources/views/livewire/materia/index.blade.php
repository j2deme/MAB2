<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ __('Materias') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-full mx-auto space-y-6 sm:px-6 lg:px-8">
        <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="text-base font-semibold leading-6 text-gray-900">{{ __('Materias') }}</h1>
                    </div>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        <x-primary-button wire:navigate href="{{ route('materias.create') }}" class="">
                            <x-icon name="plus" class="w-4 h-4 mr-2" />
                            {{ __('Add') }} {{ __('materia') }}
                        </x-primary-button>
                    </div>
                </div>

                <div class="flow-root">
                    <div class="mt-8 overflow-x-auto">
                        <div class="inline-block min-w-full py-2 align-middle">
                            <table class="w-full divide-y divide-gray-300">
                                <thead>
                                    <tr>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            No</th>

                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Clave</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Nombre</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Nombre Completo</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Carrera Id</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Semestre</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Ht</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Hp</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Cr</th>
                                        <th scope="col"
                                            class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                            Activo</th>

                                        <th scope="col"
                                            class="px-3 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($materias as $materia)
                                    <tr class="even:bg-gray-50" wire:key="{{ $materia->id }}">
                                        <td
                                            class="py-4 pl-4 pr-3 text-sm font-semibold text-gray-900 whitespace-nowrap">
                                            {{ ++$i }}</td>

                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $materia->clave
                                            }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                                            $materia->nombre }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                                            $materia->nombre_completo }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                                            $materia->carrera_id }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                                            $materia->semestre }}</td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $materia->ht }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $materia->hp }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $materia->cr }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                                            $materia->activo }}</td>

                                        <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            <a wire:navigate href="{{ route('materias.show', $materia->id) }}"
                                                class="mr-2 font-bold text-gray-600 hover:text-gray-900">{{ __('Show')
                                                }}</a>
                                            <a wire:navigate href="{{ route('materias.edit', $materia->id) }}"
                                                class="mr-2 font-bold text-indigo-600 hover:text-indigo-900">{{
                                                __('Edit') }}</a>
                                            <button class="font-bold text-red-600 hover:text-red-900" type="button"
                                                wire:click="delete({{ $materia->id }})"
                                                wire:confirm="Are you sure you want to delete?">
                                                {{ __('Delete') }}
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="px-4 mt-4">
                                {!! $materias->withQueryString()->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>