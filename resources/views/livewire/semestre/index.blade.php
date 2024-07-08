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

        <div class="flow-root mt-4">
          <livewire:semestre-table />
        </div>
      </div>
    </div>
  </div>
</div>