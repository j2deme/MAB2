<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ __('Usuarios') }}
    </h2>
</x-slot>

<div class="py-6">
    <div class="max-w-full mx-auto space-y-6 sm:px-6 lg:px-8">
        <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="text-base font-semibold leading-6 text-gray-900">{{ __('Usuarios') }}</h1>
                    </div>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        <x-primary-button wire:navigate href="{{ route('users.create') }}" class="">
                            <x-icon name="plus" class="w-4 h-4 mr-2" />
                            {{ __('Add') }} {{ __('usuario') }}
                        </x-primary-button>
                    </div>
                </div>

                <div class="flow-root mt-4">
                    <livewire:users-table />
                </div>
            </div>
        </div>
    </div>
</div>