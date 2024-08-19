@props(['paralelo'])

@if ($paralelo)
<x-icon bold name="letter-circle-p" class="w-5 h-5 text-blue-500" />
@else
<x-icon bold name="prohibit" class="w-5 h-5 text-gray-500" />
@endif