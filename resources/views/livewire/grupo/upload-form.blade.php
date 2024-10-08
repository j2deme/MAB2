<h2 class="font-bold text-gray-800">Instrucciones</h2>
{{-- <p class="text-justify text-gray-700">Para subir un archivo de grupos, selecciona la carrera a la que pertenecen
    las
    materias y sube
    el archivo con la información de las materias. Toma en cuenta que se deben cargar <strong>únicamente</strong> las
    materias que
    pertenecen a la carrera seleccionada.</p> --}}
<p class="pt-1 text-justify text-gray-700">Se requiere un arrchivo de tipo <code
        class="p-1 font-mono bg-gray-300 rounded">XLSX</code> o
    <code class="p-1 font-mono bg-gray-300 rounded">XLS</code> y debe incluir la siguiente estructura por fila:
</p>

<ul class="pt-2 pl-2 text-gray-700 list-disc list-inside">
    <li>Clave de la materia</li>
    <li>Siglas</li>
</ul>
<div class="space-y-6">
    <x-errors />
    {{-- <div>
        <x-select wire:model.defer="carrera_id" id="carrera_id" name="carrera_id" label="Carrera"
            placeholder="Selecciona una carrera" :options="$carreras" option-label="nombre" option-value="id" />
    </div> --}}

    <div>
        <x-input wire:model="archivo" id="archivo" name="archivo" label="Archivo" placeholder="Archivo de materias"
            type="file"
            accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
            <x-slot name="append">
                <x-button class="h-full" rounded="rounded-r-md" primary flat>
                    <x-icon name="upload" class="w-4 h-4 mr-2" />
                </x-button>
            </x-slot>
        </x-input>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>
            <x-icon name="floppy-disk" class="w-4 h-4 mr-2" />
            {{ __('Guardar') }}
        </x-primary-button>

        <x-link label="Cancelar" :href="route('materias.index')" />

        @if ($errors->any())
        <div class="flex text-sm text-red-800 flex-items">
            <x-icon name="warning" class="w-4 h-4 mr-2" />
            {{ __('Corrija los errores antes de continuar') }}
        </div>
        @endif
    </div>
</div>