<x-slot name="header">
  <h2 class="text-xl font-semibold leading-tight text-gray-800">
    {{ __('Eventos') }}
  </h2>
</x-slot>

<div class="py-6">
  <div class="max-w-full mx-auto space-y-6 sm:px-6 lg:px-8">
    <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
      <div class="w-full">
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ __('Eventos') }}</h1>
            <p class="mt-2 text-sm text-gray-700">Gestor de eventos para Tec Valles</p>
          </div>
          <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <x-primary-button wire:navigate href="{{ route('eventos.create') }}" class="">
              <x-icon name="plus" class="w-4 h-4 mr-2" />
              {{ __('Add') }} {{ __('evento') }}
            </x-primary-button>
          </div>
        </div>

        <div class="flow-root">
          <div class="mt-8 overflow-x-auto">
            <div class="inline-block min-w-full py-2 align-middle">
              <table class="w-full divide-y divide-gray-300">
                <thead>
                  <tr class="even:bg-gray-200">
                    <th scope="col"
                      class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                      Nombre</th>
                    <th scope="col"
                      class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase text-wrap">
                      Descripción</th>
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
                      class="px-3 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  @foreach ($eventos as $evento)
                  <tr class="even:bg-gray-50" wire:key="{{ $evento->id }}">
                    <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $evento->nombre
                      }}</td>
                    <td class="px-3 py-4 text-sm text-gray-500">{{
                      $evento->descripcion }}</td>
                    <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                      $evento->fecha_inicio->format("d/m/Y") }}</td>
                    <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{
                      $evento->fecha_fin->format("d/m/Y") }}</td>
                    <td class="items-center justify-center px-3 py-4 text-sm text-gray-500 flex-inline">
                      @if ($evento->is_activo)
                      <x-icon bold name="check" class="w-5 h-5 text-green-500" />
                      @else
                      <x-icon bold name="x" class="w-5 h-5 text-red-500" />
                      @endif
                    </td>

                    <td class="flex py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                      <a wire:navigate href="{{ route('eventos.show', $evento->id) }}"
                        class="mr-2 font-bold text-gray-600 hover:text-gray-900">
                        <x-icon bold name="eye" class="w-5 h-5" />
                      </a>
                      <a wire:navigate href="{{ route('eventos.edit', $evento->id) }}"
                        class="mr-2 font-bold text-indigo-600 hover:text-indigo-900">
                        <x-icon bold name="pencil" class="w-5 h-5" />
                      </a>
                      <button class="font-bold text-red-600 hover:text-red-900" type="button"
                        wire:click="delete({{ $evento->id }})"
                        wire:confirm="¿Está seguro de borrar el evento? No podrá recuperarlo">
                        <x-icon bold name="trash" class="w-5 h-5" />
                      </button>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>

              <div class="px-4 mt-4">
                {!! $eventos->withQueryString()->links() !!}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>