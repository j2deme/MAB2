<div>
  <h3 class="text-lg font-semibold text-primary-600">Solicitudes</h3>
  <p class="text-sm text-gray-600">Resumen rápido</p>
  <div class="mt-3 space-y-2">
    @forelse($form->movimientos->take(10) as $m)
    @php
    $tipoText = is_object($m->tipo) && property_exists($m->tipo,'value') ? $m->tipo->value : (is_scalar($m->tipo) ?
    (string)$m->tipo : 'N/A');
    $estatusVal = is_object($m->estatus) && property_exists($m->estatus,'value') ? $m->estatus->value :
    (is_scalar($m->estatus) ? (string)$m->estatus : '');
    $estatusEnum = \App\Enums\MovesStatus::tryFrom($estatusVal) ?: null;
    $materiaClave = $m->grupo->materia->clave ?? '---';
    $materiaNombre = $m->grupo->materia->nombre_corto ?? '';
    $carrera = $m->grupo->materia->carrera ?? null;
    $cColor = trim((string)($carrera->color ?? ''));
    $isHex = preg_match('/^#?[0-9A-Fa-f]{3,6}$/', $cColor);
    $bgClass = !$isHex && $cColor !== '' ? 'bg-' . preg_replace('/[^a-zA-Z]/', '', $cColor) . '-500' : '';
    $borderClass = 'border-l-4';
    $borderStyle = '';
    if ($isHex && $cColor !== '') {
    $hex = strpos($cColor, '#') === 0 ? $cColor : '#'.$cColor;
    $borderStyle = "border-left:4px solid {$hex}";
    } elseif ($cColor !== '') {
    $borderClass .= ' border-' . preg_replace('/[^a-zA-Z]/', '', $cColor) . '-500';
    }
    $materiaCarreraId = $m->grupo->materia->carrera->id ?? null;
    $isParalelo = ($m->is_paralelo ?? false) || (!empty($form->student_carreras_ids) && $materiaCarreraId &&
    !in_array($materiaCarreraId, $form->student_carreras_ids));
    @endphp

    <div class="flex items-start gap-3 p-3 bg-white rounded shadow-sm {{ $borderClass }}" @if($borderStyle)
      style="{{ $borderStyle }}" @endif>
      <div class="flex-1 text-sm pl-3">
        <div class="flex items-center justify-between">
          <div class="font-medium">{{ $materiaClave }} — {{ $materiaNombre }}</div>
          <div class="flex items-center gap-2">
            @if($isParalelo)
            @include('components.paralelo-icon', ['paralelo' => true])
            @endif
            @if($estatusEnum)
            @include('components.movimiento-estatus-badge', ['estatus' => $estatusEnum])
            @else
            <div class="text-xs text-gray-600">{{ $estatusVal }}</div>
            @endif
          </div>
        </div>
        <div class="text-xs text-gray-500">{{ $m->created_at?->format('Y-m-d H:i') ?? '' }}</div>
      </div>
      <div class="flex items-center">
        <div class="text-sm text-gray-600">{{ $tipoText }}</div>
      </div>
    </div>
    @empty
    <div class="p-3 bg-white border rounded text-sm text-gray-600">No hay movimientos registrados para este estudiante.
    </div>
    @endforelse
    {{-- Altas registradas removidas (redundantes) --}}
  </div>
</div>