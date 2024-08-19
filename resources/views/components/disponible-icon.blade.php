@props(['disponible'])

@if ($disponible)
<x-icon bold name="check" class="w-5 h-5 text-green-500" />
@else
<x-icon bold name="x" class="w-5 h-5 text-red-500" />
@endif