@props(['carrera' => null,'carreras' => null])

@if (isset($carreras) and $carreras->count() > 0)
@foreach ($carreras as $carrera)
<x-badge label="{{ $carrera->siglas }}" class="bg-{{ $carrera->color }}" />
@endforeach
@elseif (isset($carrera))
<x-badge label="{{ $carrera->siglas }}" class="bg-{{ $carrera->color }}" />
@else
N/A
@endif