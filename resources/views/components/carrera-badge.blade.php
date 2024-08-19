@props(['carrera'])
<x-badge outline label="{{ $carrera->siglas }}" class="border-{{ $carrera->color }}
  text-{{ $carrera->color }}" />