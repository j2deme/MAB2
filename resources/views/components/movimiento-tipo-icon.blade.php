@props(['tipo'])

@if ($tipo == 'Alta')
<div class="flex justify-items-center">
  <span class="inline collapse md:visible">Alta </span>
  <x-icon bold name="arrow-up" class="w-5 h-5 text-blue-500" />
</div>
@else
<div class="flex">
  <span class="inline collapse md:visible">Baja </span>
  <x-icon bold name="arrow-down" class="w-5 h-5 text-red-500" />
</div>
@endif