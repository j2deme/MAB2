<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Impersonar</h2>
</x-slot>

<div class="py-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <h3 class="text-lg font-semibold mb-4">Impersonar rol / usuario</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium mb-2">Coordinadores (todos)</h4>
                    <div class="space-y-2">
                        @foreach($coordinators as $u)
                        <div class="flex items-center justify-between border rounded p-2">
                            <div>{{ $u['label'] }}</div>
                            <div>
                                <button wire:click="startImpersonation({{ $u['id'] }})"
                                    class="px-3 py-1 bg-blue-600 text-white rounded">Impersonar</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h4 class="font-medium mb-2">Estudiantes (aleatorio, hasta 5)</h4>
                    <div class="space-y-2">
                        @foreach($students as $u)
                        <div class="flex items-center justify-between border rounded p-2">
                            <div>{{ $u['label'] }}</div>
                            <div>
                                <button wire:click="startImpersonation({{ $u['id'] }})"
                                    class="px-3 py-1 bg-blue-600 text-white rounded">Impersonar</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h4 class="font-medium mb-2">Jefes (todos)</h4>
                <div class="space-y-2">
                    @foreach($jefes as $u)
                    <div class="flex items-center justify-between border rounded p-2">
                        <div>{{ $u['label'] }}</div>
                        <div>
                            <button wire:click="startImpersonation({{ $u['id'] }})"
                                class="px-3 py-1 bg-blue-600 text-white rounded">Impersonar</button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>