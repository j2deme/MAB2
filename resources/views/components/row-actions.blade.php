@props(['model','id'])

<div class="flex items-center justify-center space-x-1">
  <x-button wire:navigate flat secondary interaction:solid href="{{ route($model.'.show', $id) }}">
    <x-icon name="eye" class="w-5 h-5 -mx-2" />
  </x-button>
  <x-button wire:navigate flat blue interaction:solid href="{{ route($model.'.edit', $id) }}">
    <x-icon name="pencil-simple" class="w-5 h-5 -mx-2" />
  </x-button>
  <x-mini-button flat red interaction:solid x-on:confirm="{
      title: 'Eliminar registro',
      description: 'Después de eliminar un registro no se puede recuperar. ¿Estás seguro de continuar?',
      icon: 'error',
      acceptLabel: 'Sí',
      rejectLabel: 'No',
      method: 'delete',
      params: '{{ $id }}',
  }">
    <x-icon name="trash" class="w-5 h-5" />
  </x-mini-button>
</div>