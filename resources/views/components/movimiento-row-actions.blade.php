@props(['model'])

@php
use App\Enums\MovesStatus;
@endphp

<div class="flex items-center justify-center space-x-1">
  <x-button wire:navigate flat secondary interaction:solid href="{{ route('movimientos.show', $model->id) }}"
    class="relative">
    @if(auth()->user()->es(['Estudiante','Coordinador']) and $model->respuesta_adicional != null)
    @php
    $color = match($model->estatus) {
    MovesStatus::AUTORIZADO, MovesStatus::AUTORIZADO_JEFE => 'green',
    MovesStatus::RECHAZADO, MovesStatus::RECHAZADO_JEFE => 'red',
    default => 'amber',
    };
    @endphp
    <span class="absolute top-0 right-0 flex w-3 h-3">
      <span
        class="absolute inline-flex w-full h-full rounded-full opacity-75 animate-ping bg-{{ $color }}-400 align-middle"></span>
      <span class="relative inline-flex w-3 h-3 rounded-full bg-{{ $color }}-500"></span>
    </span>
    @endif
    <x-icon name="eye" class="w-5 h-5 -mx-2" />
  </x-button>

  @if (auth()->user()->es(['Administrador','Jefe']) or (auth()->user()->es('Coordinador') and !$model->is_paralelo))
  <x-button wire:navigate flat blue interaction:solid href="{{ route('movimientos.edit', $model->id) }}">
    <x-icon name="pencil-simple" class="w-5 h-5 -mx-2" />
  </x-button>
  @endif

  @if ($model->estatus == \App\Enums\MovesStatus::REGISTRADO and
  auth()->user()->es(['Estudiante','Jefe','Administrador']))
  <x-mini-button flat red interaction:solid x-on:confirm="{
      title: 'Eliminar registro',
      description: 'Después de eliminar un registro no se puede recuperar. ¿Estás seguro de continuar?',
      icon: 'error',
      acceptLabel: 'Sí',
      rejectLabel: 'No',
      method: 'delete',
      params: '{{ $model->id }}',
  }">
    <x-icon name="trash" class="w-5 h-5" />
  </x-mini-button>
  @endif
</div>