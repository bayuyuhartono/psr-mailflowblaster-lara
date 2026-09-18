<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Mailflow' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen lg:flex">
        <aside class="border-b border-slate-200 bg-slate-950 px-5 py-5 text-white lg:min-h-screen lg:w-64 lg:border-b-0">
            <a href="{{ route('campaigns.index') }}" class="flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-xl bg-violet-500 text-lg font-bold">M</span>
                <span><strong class="block text-lg">Mailflow</strong><small class="text-slate-400">Email blaster</small></span>
            </a>
            <nav class="mt-6 flex gap-2 overflow-x-auto lg:flex-col">
                <a href="{{ route('campaigns.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('campaigns.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">Campaigns</a>
                <a href="{{ route('contacts.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('contacts.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">Contacts</a>
                <a href="{{ route('email-settings.edit') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('email-settings.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">Email config</a>
                @if (auth()->user()->is_super_user)
                    <a href="{{ route('users.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('users.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">Users</a>
                @endif
            </nav>
        </aside>

        <main class="min-w-0 flex-1">
            <header class="border-b border-slate-200 bg-white px-4 py-4 sm:px-8">
                <div class="mx-auto flex max-w-6xl items-center justify-between">
                    <div><p class="text-xs font-semibold uppercase tracking-widest text-violet-600">Workspace</p><h1 class="text-xl font-semibold">{{ $heading ?? 'Email Blaster' }}</h1></div>
                    <div class="flex items-center gap-3">
                        <div class="min-w-0 text-right"><p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p><p class="text-xs text-slate-500">{{ auth()->user()->is_super_user ? 'Super user' : 'User' }}</p></div>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Sign out</button></form>
                    </div>
                </div>
            </header>
            <div class="mx-auto max-w-6xl px-4 py-5 sm:px-8">
                @if (session('success'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <ul class="list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
