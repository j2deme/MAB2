@props(['carrera' => null,'carreras' => null, 'paralelo' => null])

@if (isset($carreras) and $carreras->count() > 0)
@foreach ($carreras as $c)
@php
$color = trim((string) $c->color);
$isHex = preg_match('/^#?[0-9A-Fa-f]{3,6}$/', $color);
@endphp

@if ($isHex && $color !== '')
@php $hex = strpos($color, '#') === 0 ? $color : '#'.$color; @endphp
<span class="inline-flex items-center px-2 py-0.5 rounded text-white" style="background-color: {{ $hex }}">{{ $c->siglas
  }}</span>
@else
<x-badge label="{{ $c->siglas }}" color="{{ preg_replace('/[^a-zA-Z]/', '', $c->color) }}" />
@endif
@endforeach
@elseif (isset($carrera) and is_null($paralelo))
@php
$color = trim((string) $carrera->color);
$isHex = preg_match('/^#?[0-9A-Fa-f]{3,6}$/', $color);
@endphp

@if ($isHex && $color !== '')
@php $hex = strpos($color, '#') === 0 ? $color : '#'.$color; @endphp
<span class="inline-flex items-center px-2 py-0.5 rounded text-white" style="background-color: {{ $hex }}">{{
  $carrera->siglas }}</span>
@else
<x-badge label="{{ $carrera->siglas }}" color="{{ preg_replace('/[^a-zA-Z]/', '', $carrera->color) }}" />
@endif

@elseif (isset($carrera) and $paralelo)
<div class="flex justify-center items-center">
  @php
  $colorA = trim((string) $carrera->color);
  $isHexA = preg_match('/^#?[0-9A-Fa-f]{3,6}$/', $colorA);
  @endphp

  @if ($isHexA && $colorA !== '')
  @php $hexA = strpos($colorA, '#') === 0 ? $colorA : '#'.$colorA; @endphp
  <span class="inline-flex items-center px-2 py-0.5 rounded text-white mr-1" style="background-color: {{ $hexA }}">{{
    $carrera->siglas }}</span>
  @else
  <x-badge label="{{ $carrera->siglas }}" color="{{ preg_replace('/[^a-zA-Z]/', '', $carrera->color) }}" class="mr-1" />
  @endif

  <x-icon name="arrow-right" class="w-5 h-5 text-gray-500 mx-1" />

  @php
  $colorB = trim((string) $paralelo->color);
  $isHexB = preg_match('/^#?[0-9A-Fa-f]{3,6}$/', $colorB);
  @endphp

  @if ($isHexB && $colorB !== '')
  @php $hexB = strpos($colorB, '#') === 0 ? $colorB : '#'.$colorB; @endphp
  <span class="inline-flex items-center px-2 py-0.5 rounded text-white ml-1" style="background-color: {{ $hexB }}">{{
    $paralelo->siglas }}</span>
  @else
  <x-badge label="{{ $paralelo->siglas }}" color="{{ preg_replace('/[^a-zA-Z]/', '', $paralelo->color) }}"
    class="ml-1" />
  @endif
</div>
@else
N/A
@endif