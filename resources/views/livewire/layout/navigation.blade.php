<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 dark:bg-gray-800 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block w-5 h-5 -m-1" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" active="dashboard" wire:navigate icon="house" />
                    @if (auth()->user()->es(['Administrador', 'Jefe']))
                    <x-nav-link :href="route('semestres.index')" active="semestres.*" wire:navigate icon="calendar-dots"
                        label="Semestres" />
                    <x-nav-link :href="route('carreras.index')" active="carreras.*" wire:navigate icon="graduation-cap"
                        label="Carreras" />
                    <x-nav-link :href="route('materias.index')" active="materias.*" wire:navigate icon="book"
                        label="Materias" />
                    <x-nav-link :href="route('grupos.index')" active="grupos.*" wire:navigate icon="shapes"
                        label="Grupos" />
                    <x-nav-link :href="route('users.index')" active="users.*" wire:navigate icon="users"
                        label="Usuarios" />
                    @endif

                    @if (auth()->user()->es('Estudiante'))
                    <x-nav-link :href="route('movimientos.index')" active="movimientos.*" wire:navigate
                        icon="arrows-down-up"
                        label="{{ auth()->user()->es('Estudiante') ? 'Mis solicitudes' : 'Solicitudes' }}" />
                    @endif
                </div>

                @if(!auth()->user()->es('Estudiante'))
                <div class="hidden h-full px-1 pt-1 sm:flex sm:items-center sm:ms-6">
                    <x-dropdown icon="chevron-down" width="2xl">
                        <x-slot name="trigger">
                            @php
                            $active = request()->routeIs('movimientos.*') ?? null;
                            $classes = ($active ?? false)
                            ? 'inline-flex items-center px-3 py-4 border-b-2 border-primary-400 dark:border-primary-600
                            text-sm font-medium
                            leading-5
                            text-gray-900 dark:text-gray-100 focus:outline-none focus:border-primary-700 transition
                            duration-150 ease-in-out'
                            : 'inline-flex items-center px-3 py-4 border-b-2 border-transparent text-sm font-medium
                            leading-5 text-gray-500
                            dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300
                            dark:hover:border-gray-700
                            focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300
                            dark:focus:border-gray-700
                            transition duration-150 ease-in-out';
                            @endphp
                            <button class="{{ $classes }}}}">
                                <x-icon name="arrows-down-up"
                                    class="w-5 h-5 mr-1 {{ $active ? 'text-primary-500' : null }}" />
                                <div>Solicitudes</div>

                                <div class="ms-1">
                                    <x-icon name="caret-down" bold class="w-3 h-3" />
                                </div>
                            </button>
                        </x-slot>

                        <x-dropdown.item :href="route('movimientos.index')" wire:navigate>
                            <x-icon name="list" class="w-5 h-5 mr-1 text-gray-500" />
                            Todas las solicitudes
                        </x-dropdown.item>

                        <x-dropdown.item :href="route('movimientos.pending')" wire:navigate>
                            <x-icon name="clock-countdown" class="w-5 h-5 mr-1 text-gray-500" />
                            Solicitudes pendientes
                        </x-dropdown.item>
                        <x-dropdown.item :href="route('movimientos.attended')" wire:navigate>
                            <x-icon name="checks" class="w-5 h-5 mr-1 text-gray-500" />
                            Solicitudes atendidas
                        </x-dropdown.item>
                        <x-dropdown.item separator :href="route('movimientos.materias')">
                            <x-icon name="book" class="w-5 h-5 mr-1 text-gray-500" />
                            Listado por materia
                        </x-dropdown.item>
                        <x-dropdown.item :href="route('movimientos.generacion')">
                            <x-icon name="list-numbers" class="w-5 h-5 mr-1 text-gray-500" />
                            Listado por generación
                        </x-dropdown.item>
                    </x-dropdown>
                </div>
                @endif
            </div>

            <div class="flex-grow p-4 text-right align-middle">
                <x-badge :color="Auth()->user()->rol->color()" :label="Auth()->user()->rol" sm />
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown icon="chevron-down">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md dark:text-gray-400 dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                                x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <x-icon name="caret-down" bold class="w-3 h-3" />
                            </div>
                        </button>
                    </x-slot>

                    @if (!auth()->user()->es('Estudiante'))
                    <x-dropdown.item :label="__('Profile')" :href="route('profile')" wire:navigate />
                    @endif

                    <!-- Authentication -->
                    <x-dropdown.item :label="__('Logout')" wire:click='logout' />
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="flex items-center -me-2 sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @if (auth()->user()->es(['Administrador', 'Jefe']))
            <x-responsive-nav-link :href="route('semestres.index')" :active="request()->routeIs('semestres.*')"
                wire:navigate>
                {{ __('Semestres') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('carreras.index')" :active="request()->routeIs('carreras.*')"
                wire:navigate>
                {{ __('Carreras') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('materias.index')" :active="request()->routeIs('materias.*')"
                wire:navigate>
                {{ __('Materias') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('grupos.index')" :active="request()->routeIs('grupos.*')" wire:navigate>
                {{ __('Grupos') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" wire:navigate>
                {{ __('Usuarios') }}
            </x-responsive-nav-link>
            @endif
            <x-responsive-nav-link :href="route('movimientos.index')" :active="request()->routeIs('movimientos.*')"
                wire:navigate>
                {{ auth()->user()->es('Estudiante') ? 'Mis solicitudes' : 'Solicitudes' }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="text-base font-medium text-gray-800 dark:text-gray-200"
                    x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                    x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="text-sm font-medium text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                @if (!auth()->user()->es('Estudiante'))
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                @else
                {{ __('Profile') }}
                @endif

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>