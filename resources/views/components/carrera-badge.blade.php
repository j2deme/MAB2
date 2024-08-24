@props(['carrera' => null,'carreras' => null])

@if (isset($carreras) and $carreras->count() > 0)
@foreach ($carreras as $carrera)
<x-badge outline label="{{ $carrera->siglas }}" class="border-{{ $carrera->color }}
  text-{{ $carrera->color }}" />
@endforeach
@elseif (isset($carrera))
<x-badge outline label="{{ $carrera->siglas }}" class="border-{{ $carrera->color }}
    text-{{ $carrera->color }}" />
@else
N/A
@endif