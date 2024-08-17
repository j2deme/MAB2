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
                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" active="dashboard" wire:navigate icon="house" />
                    <x-nav-link :href="route('semestres.index')" active="semestres.*" title="Semestres" wire:navigate
                        icon="calendar-dots" label="Semestres" />
                    <x-nav-link :href="route('carreras.index')" active="carreras.*" title="Carreras" wire:navigate
                        icon="graduation-cap" label="Carreras" />
                    <x-nav-link :href="route('materias.index')" active="materias.*" title="Materias" wire:navigate
                        icon="book" label="Materias" />
                    <x-nav-link :href="route('grupos.index')" active="grupos.*" title="Grupos" wire:navigate
                        icon="shapes" label="Grupos" />
                </div>
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

                    <x-dropdown.item :label="__('Profile')" :href="route('profile')" wire:navigate />

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
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

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