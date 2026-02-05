@props(['model','id'])

<div class="flex items-center justify-center space-x-1">
  <x-button wire:navigate flat secondary interaction:solid href="{{ route($model.'.show', $id) }}">
    <x-icon name="eye" class="w-5 h-5 -mx-2" />
  </x-button>
  <x-button wire:navigate flat blue interaction:solid href="{{ route($model.'.edit', $id) }}">
    <x-icon name="pencil-simple" class="w-5 h-5 -mx-2" />
  </x-button>
  @if($model === 'users')
  @php $__targetUser = \App\Models\User::find($id); @endphp
  @if($__targetUser && Auth::user()->es('Administrador') && ! $__targetUser->es('Administrador'))
  <x-mini-button flat info interaction:solid wire:click="impersonate({{ $id }})" wire:key="impersonate-{{ $id }}">
    <x-icon name="mask-happy" class="w-5 h-5" />
  </x-mini-button>
  @endif
  @endif
  <x-mini-button flat red interaction:solid wire:click="delete({{ $id }})"
    wire:confirm="¿Estás seguro de eliminar este registro?" wire:key="row-delete-{{ $id }}">
    <x-icon name="trash" class="w-5 h-5" />
  </x-mini-button>
</div>