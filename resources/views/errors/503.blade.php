<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Mantenimiento</title>
    @vite(['resources/css/app.css'])
    <style>
        /* Pequeno ajuste para centrar el cuadro en pantallas pequeñas */
        body {
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen antialiased bg-gray-50">
    <div class="w-full max-w-md px-6 py-12 mx-4 text-center bg-white rounded-lg shadow-md sm:mx-0">
        <div class="flex items-center justify-center mb-6">
            <x-application-logo class="w-24 h-24 -m-6">{{ config('app.name') }}</x-application-logo>
        </div>

        <h1 class="mb-2 text-2xl font-semibold text-gray-800">Sitio en mantenimiento</h1>
        <p class="mb-6 text-sm text-gray-600">Estamos realizando algunos ajustes y mejoras, una disculpa por las
            molestias.</p>
        <p class="mb-6 text-sm text-gray-600">El servicio volverá a estar
            disponible a la brevedad; gracias por tu paciencia.</p>

        <div class="flex items-center justify-center">
            <a href="/" class="inline-block px-6 py-2 text-white rounded bg-primary-600 hover:bg-primary-700">Volver al
                inicio</a>
        </div>

        <div class="mt-6 text-xs text-gray-400">Código: 503 — Modo mantenimiento</div>
    </div>