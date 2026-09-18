<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title.' - Mailflow' : 'Mailflow' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/mailflow-envelope.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>
<body class="min-h-screen bg-slate-950 text-slate-900 antialiased">
    <main class="grid min-h-screen place-items-center px-5 py-10">
        <div class="mailflow-auth-card">
            <div class="mb-5 text-center text-white">
                <img src="{{ asset('images/mailflow-envelope.svg') }}" alt="" class="mx-auto size-10">
                <h1 class="mt-2 text-xl font-bold">Mailflow</h1>
                <p class="mt-1 text-sm text-slate-400">Email blaster workspace</p>
            </div>
            <div class="rounded-2xl bg-white p-6 shadow-xl">
                {{ $slot }}
            </div>
        </div>
    </main>
</body>
</html>
