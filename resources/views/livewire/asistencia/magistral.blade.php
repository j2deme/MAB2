<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ __('Create') }} Asistencia
    </h2>
</x-slot>

<div class="py-6">
    <div class="max-w-full mx-auto space-y-6 sm:px-6 lg:px-8">
        <div class="p-4 ">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="text-xl font-semibold leading-6 text-center text-gray-900">{{ $evento->nombre }}</h1>
                        <p class="mt-2 text-sm text-gray-700">Registro de asistencia a la conferencia magistral "{{
                            $magistral->nombre }}"</p>
                    </div>
                </div>

                <div class="flow-root">
                    <div class="mt-8 overflow-x-auto">
                        <div class="max-w-xl py-2 align-middle">
                            <form method="POST" wire:submit="save" role="form" enctype="multipart/form-data">
                                @csrf
                                @include('livewire.asistencia.form')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>