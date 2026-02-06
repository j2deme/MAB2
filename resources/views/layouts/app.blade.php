<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <wireui:scripts />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <x-notifications />
    <x-dialog />
    @if (session()->has('wireui:notification') and false)
    <script>
        Wireui.hook('notifications:load', () => {
            $title = '{{ session('wireui:notification.options.title') }}';
            $description = '{{ session('wireui:notification.options.description') }}';
            $icon = '{{ session('wireui:notification.options.icon') }}';

            $wireui.notify({
                title: $title,
                description: $description,
                icon: $icon,
            });
        });
    </script>
    @endif
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <livewire:layout.navigation />

        {{-- Modal show component for movimientos (skip mounting during AJAX requests) --}}
        @unless(request()->ajax())
        <livewire:movimiento.modal-show />
        @endunless

        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow dark:bg-gray-800">
            <div class="px-4 py-3 mx-auto max-w-7xl sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        @if(session('admin_impersonating'))
        <div class="p-4 border-l-4 border-yellow-400 bg-yellow-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-yellow-800">Estás impersonando como <strong>{{ auth()->user()->rol->value
                        }}</strong> - {{ auth()->user()->username ?? auth()->user()->name }}</div>
                <form method="POST" action="{{ route('impersonate.stop') }}">
                    @csrf
                    <button type="submit" class="text-sm text-yellow-800 underline">Regresar al rol
                        administrador</button>
                </form>
            </div>
        </div>
        @endif
        <main>
            {{ $slot }}
        </main>
    </div>
    @include('layouts.footer')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Robust helper to emit Livewire events from inline onclick handlers.
        // If Livewire is not yet available (scripts load later), it will retry a few times.
        window.emitLivewire = function(eventName, payload) {
            var attempts = 0;
            var maxAttempts = 12; // ~2.4s total with 200ms interval

            var tryEmit = function() {
                attempts++;
                try {
                    if (window.Livewire && typeof window.Livewire.emit === 'function') {
                        window.Livewire.emit(eventName, payload);
                        return true;
                    }
                    if (window.livewire && typeof window.livewire.emit === 'function') {
                        window.livewire.emit(eventName, payload);
                        return true;
                    }
                } catch (e) {
                    console.error('emitLivewire error', e);
                    return true;
                }

                if (attempts < maxAttempts) {
                    setTimeout(tryEmit, 200);
                } else {
                    console.warn('Livewire emit function not found. Event ' + eventName + ' not delivered.');
                }
                return false;
            };

            tryEmit();
        };
    </script>
    <!-- Simple JS-driven modal for progressive testing -->
    <div id="simple-movimiento-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/20 dark:bg-white/10"
            style="backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);"
            onclick="hideSimpleMovimientoModal()"></div>
        <div class="relative w-full max-w-2xl mx-4 bg-white rounded-lg shadow-lg overflow-auto max-h-[85vh]">
            {{-- thin accent bar for movimiento status (applied dynamically) --}}
            <div id="simple-movimiento-modal-accent" class="hidden h-1 w-full rounded-sm mb-0"></div>
            <div class="flex items-center justify-between p-4 border-b relative">
                <h3 id="simple-movimiento-modal-title" class="text-lg font-semibold">Detalle de la solicitud</h3>
                <div>
                    <button onclick="hideSimpleMovimientoModal()" aria-label="Cerrar"
                        class="p-2 text-gray-600 rounded hover:bg-gray-100">
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>
            </div>
            <div id="simple-movimiento-modal-body" class="p-4">
                <!-- Intentionally empty for progressive test -->
            </div>
        </div>
    </div>
    <script>
        // Show simple modal (used by fetchMovimientoPartial)
        window.showSimpleMovimientoModal = function() {
            try {
                var modal = document.getElementById('simple-movimiento-modal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } catch (e) { console.error(e); }
        };

        // Fetch partial HTML for movimiento and show modal
        window.fetchMovimientoPartial = function(id) {
            try {
                var url = '/solicitudes/partial/' + id;
                var body = document.getElementById('simple-movimiento-modal-body');
                var title = document.getElementById('simple-movimiento-modal-title');
                title.textContent = 'Detalle de la solicitud ' + (id ?? '');
                body.innerHTML = '<div class="py-8 text-center text-sm text-gray-500">Cargando…</div>';
                window.showSimpleMovimientoModal();

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(res) { return res.text(); })
                    .then(function(html) {
                        body.innerHTML = html;
                        // apply accent color from partial meta (if present)
                        try {
                            var meta = body.querySelector('#movimiento-status-meta');
                            var accent = document.getElementById('simple-movimiento-modal-accent');
                            if (meta && meta.dataset && meta.dataset.status && accent) {
                                var status = meta.dataset.status;
                                // set class to use existing Tailwind color utility (e.g. bg-red-50)
                                accent.className = 'h-1 w-full rounded-sm mb-0 bg-' + status + '-50';
                                accent.classList.remove('hidden');
                            } else if (accent) {
                                accent.classList.add('hidden');
                            }
                        } catch (e) { console.error(e); }
                    })
                    .catch(function(err) {
                        console.error('fetchMovimientoPartial error', err);
                        body.innerHTML = '<div class="py-8 text-center text-sm text-red-500">Error al cargar el detalle.</div>';
                    });
            } catch (e) { console.error(e); }
        };
        window.hideSimpleMovimientoModal = function() {
            try {
                var modal = document.getElementById('simple-movimiento-modal');
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                // hide accent when closing
                try { var accent = document.getElementById('simple-movimiento-modal-accent'); if (accent) accent.classList.add('hidden'); } catch (e) {}
            } catch (e) { console.error(e); }
        };
    </script>
</body>

</html>