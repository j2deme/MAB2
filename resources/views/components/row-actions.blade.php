@props(['model','id'])

<div class="flex items-center justify-center space-x-1">
  <x-button wire:navigate flat secondary interaction:solid href="{{ route($model.'.show', $id) }}">
    <x-icon name="eye" class="w-5 h-5 -mx-2" />
  </x-button>
  {{-- Generic edit/delete for admins and jefes on any model --}}
  @if(Auth::user()->es(['Administrador','Jefe']))
  <x-button wire:navigate flat blue interaction:solid href="{{ route($model.'.edit', $id) }}">
    <x-icon name="pencil-simple" class="w-5 h-5 -mx-2" />
  </x-button>
  <x-mini-button flat red interaction:solid wire:click="delete({{ $id }})"
    wire:confirm="¿Estás seguro de eliminar este registro?" wire:key="row-delete-{{ $id }}">
    <x-icon name="trash" class="w-5 h-5" />
  </x-mini-button>
  @endif

  {{-- Special user-specific actions (impersonate, coordinator quick-password) --}}
  @if($model === 'users')
  @php $__targetUser = \App\Models\User::find($id); @endphp
  @if(Auth::user()->es('Administrador') && $__targetUser && ! $__targetUser->es('Administrador'))
  <x-mini-button flat info interaction:solid wire:click="impersonate({{ $id }})" wire:key="impersonate-{{ $id }}">
    <x-icon name="mask-happy" class="w-5 h-5" />
  </x-mini-button>
  @endif

  @if(Auth::user()->es('Coordinador'))
  <x-mini-button flat blue interaction:solid wire:click.prevent="openCoordinatorPasswordModal({{ $id }})">
    <x-icon name="key" class="w-5 h-5 -mx-2" />
  </x-mini-button>
  @endif
  @endif
</div>