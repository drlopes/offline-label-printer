<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>{{ $title ?? 'Page Title' }}</title>
</head>

<body class="select-none">
    <x-main full-width>
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-zinc-100 dark:bg-base-300" collapse-text="Colapsar">

            <div class="ml-5 py-5 flex gap-x-1 h-6">
                <span>
                    <img src="{{ Vite::asset('resources/images/icon.png') }}" alt="Logo" class="h-5 min-w-7 pl-1">
                </span>
                <span x-cloak x-show="!collapsed" class="overflow-hidden font-bold w-full h-6">
                    Contingência {{ env('APP_VERSION', null) ? '(v'.env('APP_VERSION').')' : '' }}
                </span>
            </div>

            <x-menu activate-by-route>
                <x-menu-item title="Gerar Etiquetas" icon="o-printer" link="/" />

                <x-menu-sub title="Configurações" icon="o-cog-6-tooth">
                    <x-menu-item title="Impressoras" icon="o-printer" link="/printers" />
                </x-menu-sub>
            </x-menu>
        </x-slot:sidebar>

        <x-slot:content>
            <div class="fixed top-1 right-1 flex items-center gap-x-2 z-50">
                <x-theme-toggle class="btn btn-sm btn-circle btn-ghost" />
            </div>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    <x-toast />
</body>

</html>