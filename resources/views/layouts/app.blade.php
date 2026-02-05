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
</body>

</html>