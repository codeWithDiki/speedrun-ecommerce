<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config("app.name") }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <livewire:ui.navbar-component/>
    <main class="max-w-6xl mx-auto px-2 mt-20 py-10">
        {{ $slot }}
    </main>
    @stack("bottom-js")
</body>
</html>