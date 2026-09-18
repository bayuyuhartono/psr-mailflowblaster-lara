<x-layouts.guest title="Sign in">
    <div class="w-full">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-violet-600">Welcome back</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Sign in to Mailflow</h2>
            <p class="mt-3 text-sm leading-6 text-slate-500">Enter your account details to manage contacts and campaigns.</p>
        </div>

        @if ($errors->any())
            <div class="mt-6 flex gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9L2.6 17.2A2 2 0 004.3 20h15.4a2 2 0 001.7-2.8L13.7 3.9a2 2 0 00-3.4 0z"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="mt-8 grid gap-5">
            @csrf
            <label class="grid gap-2">
                <span class="text-sm font-semibold text-slate-700">Email address</span>
                <span class="relative">
                    <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-16 9h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="name@company.com" class="w-full rounded-xl border border-slate-300 bg-white py-3.5 pl-12 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-violet-500 focus:ring-4 focus:ring-violet-100">
                </span>
            </label>

            <label class="grid gap-2">
                <span class="text-sm font-semibold text-slate-700">Password</span>
                <span class="relative">
                    <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 11V8a5 5 0 0110 0v3m-11 0h12a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6a2 2 0 012-2z"/></svg>
                    <input type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" class="w-full rounded-xl border border-slate-300 bg-white py-3.5 pl-12 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-violet-500 focus:ring-4 focus:ring-violet-100">
                </span>
            </label>

            <label class="flex w-fit items-center gap-3 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1" class="size-4 rounded border-slate-300 accent-yellow-500">
                <span>Keep me signed in</span>
            </label>

            <button class="flex items-center justify-center gap-2 rounded-xl bg-violet-600 px-5 py-3.5 text-sm font-bold shadow-sm transition hover:bg-violet-700 focus:outline-none focus:ring-4 focus:ring-violet-100">
                <span>Sign in to workspace</span>
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6l6 6-6 6"/></svg>
            </button>
        </form>
    </div>
</x-layouts.guest>
