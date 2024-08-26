<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Subida Masiva de Estudiantes
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <div class="w-full">
                    <div class="sm:flex sm:items-center">
                        <div class="sm:flex-auto">
                            <h1 class="text-base font-semibold leading-6 text-gray-900">Subida de Masiva Estudiantes
                            </h1>
                        </div>
                        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                            @include('components.back-button', ['url' => route('users.index')])
                        </div>
                    </div>

                    <div class="flow-root">
                        <div class="mt-8 overflow-x-auto">
                            <div class="max-w-2xl py-2 align-middle">
                                <form method="POST" action="{{ route('users.upload') }}" wire:submit="save" role="form"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @include('livewire.user.upload-form')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>