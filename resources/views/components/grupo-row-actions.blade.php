@props(['grupo'])

@php $isTrashed = $grupo->trashed(); @endphp

<div class="flex items-center justify-center space-x-1">
  <x-button flat secondary interaction:solid href="{{ route('grupos.show', $grupo->id) }}" title="Ver detalle">
    <x-icon name="eye" class="w-5 h-5 -mx-2" />
  </x-button>

  <x-button flat blue interaction:solid href="{{ route('grupos.edit', $grupo->id) }}" title="Editar">
    <x-icon name="pencil-simple" class="w-5 h-5 -mx-2" />
  </x-button>

  @if($isTrashed)
  <x-mini-button flat green interaction:solid wire:click="restore({{ $grupo->id }})"
    wire:confirm="¿Restaurar este grupo y conservar movimientos?" wire:key="restore-{{ $grupo->id }}" title="Restaurar">
    <x-icon name="arrow-counter-clockwise" class="w-5 h-5" />
  </x-mini-button>

  <x-mini-button flat red interaction:solid wire:click="forceDelete({{ $grupo->id }})"
    wire:confirm="¿Eliminar definitivamente este grupo? Esta acción es irreversible."
    wire:key="force-delete-{{ $grupo->id }}" title="Eliminar definitivamente">
    <x-icon name="trash" class="w-5 h-5" />
  </x-mini-button>
  @else
  <x-mini-button flat red interaction:solid wire:click="delete({{ $grupo->id }})"
    wire:confirm="¿Estás seguro de eliminar este grupo?" wire:key="delete-{{ $grupo->id }}" title="Eliminar">
    <x-icon name="trash" class="w-5 h-5" />
  </x-mini-button>
  @endif
</div>