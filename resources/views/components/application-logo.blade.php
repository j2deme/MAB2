@php
$cols = ($slot->isEmpty()) ? 1 : 2;
@endphp
<div class="grid grid-cols-{{ $cols }}">
    <div class="flex flex-col px-3">
        <x-icon name="caret-up" fill {{ $attributes }} class="text-blue-500 dark:text-blue-400" />
        <x-icon name="caret-down" fill {{ $attributes }} class="text-red-500 dark:text-red-400" />
    </div>
    @if (!$slot->isEmpty())
    <div class="flex items-center justify-end">
        <span class="text-xl font-bold">
            {{ $slot }}
        </span>
    </div>
    @endif
</div>