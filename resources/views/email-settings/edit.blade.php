<x-layouts.app title="Email Configuration" heading="Email configuration">
    <div class="max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6"><h2 class="text-lg font-semibold">SMTP server</h2><p class="mt-1 text-sm text-slate-500">Credentials are encrypted before being stored. Leave password blank to keep the current password.</p></div>
        <form method="POST" action="{{ route('email-settings.update') }}">
            @csrf @method('PUT')
            <div class="grid gap-5 sm:grid-cols-2">
                <label class="grid gap-2 sm:col-span-2"><span class="text-sm font-medium">SMTP host *</span><input name="host" required value="{{ old('host', $setting?->host) }}" placeholder="smtp.example.com" class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></label>
                <label class="grid gap-2"><span class="text-sm font-medium">Port *</span><input type="number" name="port" required value="{{ old('port', $setting?->port ?? 587) }}" class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></label>
                <label class="grid gap-2"><span class="text-sm font-medium">Encryption</span><select name="encryption" class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"><option value="">Automatic / none</option><option value="tls" @selected(old('encryption', $setting?->encryption) === 'tls')>TLS</option><option value="ssl" @selected(old('encryption', $setting?->encryption) === 'ssl')>SSL</option></select></label>
                <label class="grid gap-2"><span class="text-sm font-medium">Username</span><input name="username" value="{{ old('username', $setting?->username) }}" autocomplete="username" class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></label>
                <label class="grid gap-2"><span class="text-sm font-medium">Password</span><input type="password" name="password" autocomplete="new-password" placeholder="{{ $setting?->exists ? 'Keep current password' : '' }}" class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></label>
                <label class="grid gap-2"><span class="text-sm font-medium">From email *</span><input type="email" name="from_address" required value="{{ old('from_address', $setting?->from_address) }}" placeholder="hello@example.com" class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></label>
                <label class="grid gap-2"><span class="text-sm font-medium">From name *</span><input name="from_name" required value="{{ old('from_name', $setting?->from_name) }}" placeholder="Acme Team" class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></label>
            </div>
            <button class="mt-7 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Save configuration</button>
        </form>
    </div>
</x-layouts.app>
