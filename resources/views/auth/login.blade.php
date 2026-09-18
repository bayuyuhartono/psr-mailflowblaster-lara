<x-layouts.guest title="Sign in">
    <h2 class="text-lg font-bold">Welcome back</h2>
    <p class="mt-1 text-sm text-slate-500">Sign in to manage contacts and campaigns.</p>
    @if ($errors->any()) <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div> @endif
    <form method="POST" action="{{ route('login.store') }}" class="mt-5 grid gap-4">
        @csrf
        <label class="grid gap-2"><span class="text-sm font-medium">Email address</span><input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></label>
        <label class="grid gap-2"><span class="text-sm font-medium">Password</span><input type="password" name="password" required autocomplete="current-password" class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></label>
        <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remember" value="1" class="rounded border-slate-300"> Remember me</label>
        <button class="rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Sign in</button>
    </form>
</x-layouts.guest>
