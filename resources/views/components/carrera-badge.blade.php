@props(['carrera' => null,'carreras' => null, 'paralelo' => null])

@if (isset($carreras) and $carreras->count() > 0)
@foreach ($carreras as $carrera)
<x-badge label="{{ preg_replace('/[^a-zA-Z]/', '', $carrera->siglas) }}"
  color="{{ preg_replace('/[^a-zA-Z]/', '', $carrera->color) }}" />
@endforeach
@elseif (isset($carrera) and is_null($paralelo))
<x-badge label="{{ $carrera->siglas }}" color="{{ preg_replace('/[^a-zA-Z]/', '', $carrera->color) }}" />
@elseif (isset($carrera) and $paralelo)
<div class="flex justify-center">
  <x-badge label="{{ $carrera->siglas }}" color="{{ preg_replace('/[^a-zA-Z]/', '', $carrera->color) }}" class="mr-1" />
  <x-icon name="arrow-right" class="w-5 h-5 text-gray-500" />
  <x-badge label="{{ $paralelo->siglas }}" color="{{ preg_replace('/[^a-zA-Z]/', '', $paralelo->color) }}"
    class="ml-1" />
</div>
@else
N/A
@endif