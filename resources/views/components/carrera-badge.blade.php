@props(['carrera' => null,'carreras' => null])

@if (isset($carreras) and $carreras->count() > 0)
@foreach ($carreras as $carrera)
<x-badge label="{{ preg_replace('/[^a-zA-Z]/', '', $carrera->siglas) }}"
  color="{{ preg_replace('/[^a-zA-Z]/', '', $carrera->color) }}" />
@endforeach
@elseif (isset($carrera))
<x-badge label="{{ $carrera->siglas }}" color="{{ preg_replace('/[^a-zA-Z]/', '', $carrera->color) }}" />
@else
N/A
@endif