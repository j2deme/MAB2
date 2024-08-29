<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
        @if (auth()->user()->es('Estudiante'))
        {{ __('Mis solicitudes') }}
        @else
        @if (request()->routeIs('movimientos.pending'))
        {{ __('Solicitudes pendientes') }}
        @elseif (request()->routeIs('movimientos.attended'))
        {{ __('Solicitudes atendidas') }}
        @else
        {{ __('Solicitudes') }}
        @endif
        @endif
    </h2>
</x-slot>

<div class="py-6">
    <div class="max-w-full mx-auto space-y-6 sm:px-6 lg:px-8">
        <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="text-base font-semibold leading-6 text-gray-900">{{ __('Solicitudes') }}</h1>
                        <p class="mt-2 text-sm text-gray-700">{{ $semestre->nombre_completo }}</p>
                    </div>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        @if (auth()->user()->es('Estudiante'))
                        <x-button blue label="Alta" icon="plus"
                            href="{{ route('movimientos.request', ['tipo' => 'alta']) }}"
                            class="text-xs font-semibold tracking-widest uppercase" />
                        <x-button red label="Baja" icon="minus"
                            href="{{ route('movimientos.request', ['tipo' => 'baja']) }}"
                            class="text-xs font-semibold tracking-widest uppercase" />
                        @endif
                        @if (Auth::user()->es(['Administrador', 'Jefe']))
                        <x-primary-button wire:navigate href="{{ route('movimientos.create') }}" class="">
                            <x-icon name="plus" class="w-4 h-4 mr-2" />
                            {{ __('Add') }} {{ __('movimiento') }}
                        </x-primary-button>
                        @endif
                    </div>
                </div>

                <div class="flow-root mt-4">
                    <livewire:movimientos-table />
                </div>
            </div>
        </div>
    </div>