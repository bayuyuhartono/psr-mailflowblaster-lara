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
        <section class="relative hidden overflow-hidden bg-slate-950 px-12 py-10 text-white lg:flex lg:flex-col lg:justify-between xl:px-20 xl:py-14">
            <div class="absolute -left-24 top-1/3 size-80 rounded-full bg-yellow-300/10 blur-3xl"></div>
            <div class="absolute -right-32 -top-20 size-96 rounded-full bg-white/5 blur-3xl"></div>

            <a href="{{ url('/') }}" class="relative flex w-fit items-center gap-3" aria-label="Mailflow home">
                <img src="{{ asset('images/mailflow-envelope.svg') }}" alt="" class="size-11 rounded-xl shadow-lg shadow-black/20">
                <span class="text-xl font-bold tracking-tight">Mailflow</span>
            </a>

            <div class="relative max-w-xl py-16">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-yellow-300">Email made manageable</p>
                <h1 class="mt-5 text-4xl font-bold leading-tight tracking-tight xl:text-5xl">Send every campaign with confidence.</h1>
                <p class="mt-6 max-w-lg text-base leading-7 text-slate-300 xl:text-lg">Keep your contacts, campaigns, and delivery results together in one focused workspace.</p>

                <div class="mt-10 grid max-w-lg grid-cols-3 gap-3">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <svg class="size-5 text-yellow-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-16 9h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <p class="mt-3 text-sm font-semibold">Create</p>
                        <p class="mt-1 text-xs leading-5 text-slate-400">Build focused campaigns.</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <svg class="size-5 text-yellow-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.36-1.86M17 20H7m10 0v-2c0-.66-.13-1.29-.36-1.86M7 20H2v-2a3 3 0 015.36-1.86M7 20v-2c0-.66.13-1.29.36-1.86m0 0a5 5 0 019.28 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <p class="mt-3 text-sm font-semibold">Organize</p>
                        <p class="mt-1 text-xs leading-5 text-slate-400">Keep contacts ready.</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <svg class="size-5 text-yellow-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V9m5 10V5m5 14v-7m5 7V8"/></svg>
                        <p class="mt-3 text-sm font-semibold">Track</p>
                        <p class="mt-1 text-xs leading-5 text-slate-400">See delivery results.</p>
                    </div>
                </div>
            </div>

            <p class="relative text-xs text-slate-500">A private workspace for the PSR communications team.</p>
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
