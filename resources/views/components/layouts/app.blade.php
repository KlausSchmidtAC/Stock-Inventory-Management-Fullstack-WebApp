<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'TechGear Inventory' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <!-- App Header mit Titel rechts oben -->
    <div class="app-header">
        <h1 class="app-title">TechGear Inventory-Manager</h1>
    </div>

    <!-- Main Content mit Padding -->
    <div class="app-container">
        <main>
            <div class="max-w-7xl mx-auto">
                <div class="content-wrapper p-6">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>
</body>
</html>
