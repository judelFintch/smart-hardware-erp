<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Gestion Stock') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen">
            <livewire:layout.navigation />

            <main class="lg:pl-64">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
        @livewireScripts
        <script>
            document.addEventListener('keydown', (event) => {
                if (!event.altKey) return;
                const key = event.key.toLowerCase();
                const routes = {
                    f: 'focus-search',
                    p: @json(route('products.create')),
                    v: @json(route('sales.create')),
                    a: @json(route('purchases.create')),
                    i: @json(route('inventory-counts.create')),
                    t: @json(route('stock-transfers.create')),
                };

                if (!routes[key]) return;
                event.preventDefault();

                if (routes[key] === 'focus-search') {
                    const input = document.querySelector('[data-global-search]');
                    if (input) {
                        input.focus();
                        input.select();
                    }
                    return;
                }

                window.location.href = routes[key];
            });

            const autosaveForms = () => {
                document.querySelectorAll('form[data-autosave]').forEach((form) => {
                    const key = form.getAttribute('data-autosave-key') || window.location.pathname;
                    const storageKey = `autosave:${key}`;
                    const inputs = form.querySelectorAll('input, textarea, select');
                    const saved = localStorage.getItem(storageKey);

                    if (saved) {
                        try {
                            const data = JSON.parse(saved);
                            inputs.forEach((input) => {
                                if (input.type === 'file') return;
                                const fieldKey = input.getAttribute('wire:model') || input.getAttribute('wire:model.defer') || input.name || input.id;
                                if (!fieldKey || data[fieldKey] === undefined) return;
                                if (input.type === 'checkbox') {
                                    input.checked = Boolean(data[fieldKey]);
                                } else if (!input.value) {
                                    input.value = data[fieldKey];
                                }
                            });
                        } catch (error) {
                            localStorage.removeItem(storageKey);
                        }
                    }

                    let timer = null;
                    const save = () => {
                        const payload = {};
                        inputs.forEach((input) => {
                            if (input.type === 'file') return;
                            const fieldKey = input.getAttribute('wire:model') || input.getAttribute('wire:model.defer') || input.name || input.id;
                            if (!fieldKey) return;
                            payload[fieldKey] = input.type === 'checkbox' ? input.checked : input.value;
                        });
                        localStorage.setItem(storageKey, JSON.stringify(payload));
                    };

                    const schedule = () => {
                        clearTimeout(timer);
                        timer = setTimeout(save, 400);
                    };

                    inputs.forEach((input) => {
                        if (input.type === 'file') return;
                        input.addEventListener('input', schedule);
                        input.addEventListener('change', schedule);
                    });

                    form.addEventListener('submit', () => {
                        localStorage.removeItem(storageKey);
                    });
                });
            };

            document.addEventListener('DOMContentLoaded', autosaveForms);
            document.addEventListener('livewire:navigated', autosaveForms);
        </script>
    </body>
</html>
