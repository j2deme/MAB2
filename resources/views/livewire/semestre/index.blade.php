<?php

use function Livewire\Volt\{state};

state([
    'semestres' => \App\Models\Semestre::paginate(10),
    'i' => 0,
]);

?>
<x-slot name="header">
  <h2 class="text-xl font-semibold leading-tight text-gray-800">
    {{ __('Semestres') }}
  </h2>
</x-slot>

<div class="py-12">
  <div class="max-w-full mx-auto space-y-6 sm:px-6 lg:px-8">
    <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
      <div class="w-full">
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">{{ __('Semestres') }}</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all the {{ __('Semestres') }}.</p>
          </div>
          <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <a type="button" wire:navigate href="{{ route('semestres.create') }}"
              class="block px-3 py-2 text-sm font-semibold text-center text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add
              new</a>
          </div>
        </div>

        <div class="flow-root">
          <div class="mt-8 overflow-x-auto">
            <div class="inline-block min-w-full py-2 align-middle">
              <table class="w-full divide-y divide-gray-300">
                <thead>
                  <tr>
                    <th scope="col"
                      class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">
                      No</th>

                    <th scope="col"
                      class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">
                      Clave</th>
                    <th scope="col"
                      class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">
                      Nombre</th>
                    <th scope="col"
                      class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">
                      Nombre Completo</th>
                    <th scope="col"
                      class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">
                      Periodo Altas</th>
                    <th scope="col"
                      class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">
                      Periodo Bajas</th>
                    <th scope="col"
                      class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">
                      Max Altas</th>
                    <th scope="col"
                      class="py-3 pl-4 pr-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">
                      Activo</th>

                    <th scope="col"
                      class="px-3 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  @foreach ($semestres as $semestre)
                  <tr class="even:bg-gray-50" wire:key="{{ $semestre->id }}">
                    <td class="py-4 pl-4 pr-3 text-sm font-semibold text-center text-gray-900 whitespace-nowrap">
                      {{ $loop->iteration }}</td>

                    <td class="px-3 py-4 text-sm text-center text-gray-500 whitespace-nowrap">{{
                      $semestre->clave }}</td>
                    <td class="px-3 py-4 text-sm text-center text-gray-500 whitespace-nowrap">{{
                      $semestre->nombre }}</td>
                    <td class="px-3 py-4 text-sm text-center text-gray-500 whitespace-nowrap">{{
                      $semestre->nombre_completo }}</td>
                    <td class="px-3 py-4 text-sm text-center text-gray-500 whitespace-nowrap">{{
                      $semestre->periodo_altas }}</td>
                    <td class="px-3 py-4 text-sm text-center text-gray-500 whitespace-nowrap">{{
                      $semestre->periodo_bajas }}</td>
                    <td class="px-3 py-4 text-sm text-center text-gray-500 whitespace-nowrap">{{
                      $semestre->max_altas }}</td>
                    <td class="px-3 py-4 text-sm text-center text-gray-500 whitespace-nowrap">{{
                      $semestre->activo }}</td>

                    <td class="py-4 pl-4 pr-3 text-sm font-medium text-center text-gray-900 whitespace-nowrap">
                      <a wire:navigate href="{{ route('semestres.show', $semestre->id) }}"
                        class="mr-2 font-bold text-gray-600 hover:text-gray-900">{{ __('Show')
                        }}</a>
                      <a wire:navigate href="{{ route('semestres.edit', $semestre->id) }}"
                        class="mr-2 font-bold text-indigo-600 hover:text-indigo-900">{{
                        __('Edit') }}</a>
                      <button class="font-bold text-red-600 hover:text-red-900" type="button"
                        wire:click="delete({{ $semestre->id }})" wire:confirm="Are you sure you want to delete?">
                        {{ __('Delete') }}
                      </button>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>

              <div class="px-4 mt-4">
                {!! $semestres->withQueryString()->links() !!}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>