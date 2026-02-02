<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ __('Carreras') }}
    </h2>
</x-slot>

<div class="py-6">
    <div class="max-w-full mx-auto space-y-6 sm:px-6 lg:px-8">
        <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
            <div class="w-full">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h1 class="text-base font-semibold leading-6 text-gray-900">{{ __('Carreras') }}</h1>
                    </div>
                    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                        <x-primary-button wire:navigate href="{{ route('carreras.create') }}" class="">
                            <x-icon name="plus" class="w-4 h-4 mr-2" />
                            {{ __('Add') }} {{ __('carrera') }}
                        </x-primary-button>
                    </div>
                </div>

                <div class="flow-root mt-4" id="carreras-wrapper">
                    <div id="carreras-spinner" class="p-6 flex items-center justify-center bg-white border rounded">
                        <svg class="animate-spin h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span class="ml-3 text-sm text-gray-600">Cargando tabla...</span>
                    </div>

                    <livewire:carreras-table />

                    <script>
                        (function () {
                            window.addEventListener('carreras-table-mounted', function () {
                                var s = document.getElementById('carreras-spinner');
                                if (s) s.style.display = 'none';
                            });
                            setTimeout(function () {
                                var s = document.getElementById('carreras-spinner');
                                if (s && s.style.display !== 'none') s.style.display = 'none';
                            }, 6000);
                        })();
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>