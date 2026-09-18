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
<body class="min-h-screen bg-white text-slate-900 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[minmax(0,1.05fr)_minmax(440px,0.95fr)]">
        <section class="hidden place-items-center bg-slate-950 lg:grid">
            <a href="{{ url('/') }}" aria-label="Mailflow home">
                <img src="{{ asset('images/mailflow-envelope.svg') }}" alt="" class="size-28 rounded-3xl shadow-2xl shadow-black/30 xl:size-32">
            </a>
        </section>

        <section class="flex min-h-screen flex-col bg-slate-50 px-5 py-6 sm:px-10 lg:bg-white lg:px-14 xl:px-20">
            <div class="flex items-center justify-between lg:hidden">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5" aria-label="Mailflow home">
                    <img src="{{ asset('images/mailflow-envelope.svg') }}" alt="" class="size-9 rounded-lg">
                    <span class="font-bold tracking-tight">Mailflow</span>
                </a>
                <span class="text-xs font-medium text-slate-400">Email workspace</span>
            </div>

            <div class="mx-auto flex w-full max-w-md flex-1 items-center py-12 sm:py-16">
                {{ $slot }}
            </div>

            <p class="text-center text-xs text-slate-400 lg:text-left">Secure access to your Mailflow workspace</p>
        </section>
    </main>
</body>
</html>
