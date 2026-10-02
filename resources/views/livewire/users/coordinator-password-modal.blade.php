<div>
  @if($show)
  <div class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" wire:click="$set('show', false)"></div>
    <div class="relative w-full max-w-lg mx-4 bg-white rounded-lg shadow-lg overflow-hidden">
      <div class="p-4 border-b">
        <h3 class="text-lg font-semibold">Cambiar contraseña</h3>
      </div>
      <div class="p-4">
        <div class="text-sm text-gray-700 mb-4">
          <div><strong>Nombre:</strong> {{ $user?->name }}</div>
          <div><strong>Usuario:</strong> {{ $user?->username }}</div>
          <div><strong>Carreras:</strong> {{ $user?->carreras->pluck('nombre')->join(', ') }}</div>
        </div>

        <div>
          <x-password wire:model.defer="newPassword" id="modalNewPassword" :label="__('Nueva contraseña')"
            placeholder="Nueva contraseña" autocomplete="off" />
        </div>
      </div>
      <div class="p-4 border-t flex justify-end gap-2">
        <x-button flat secondary wire:click.prevent="$set('show', false)">Cancelar</x-button>
        <x-primary-button wire:click.prevent="savePassword">Cambiar contraseña</x-primary-button>
      </div>
    </div>
  </div>
  @endif
</div>