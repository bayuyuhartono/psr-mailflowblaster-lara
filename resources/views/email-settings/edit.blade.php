<x-layouts.app title="Email Configuration" heading="Email configuration">
    <div class="settings-heading">
        <div><p class="text-xs font-semibold uppercase tracking-widest text-violet-600">Delivery settings</p><h2 class="mt-2 text-2xl font-bold">Connect your mailbox</h2><p class="mt-2 text-sm text-slate-500">Set up the sender behind every customer conversation.</p></div>
        <span class="settings-state">{{ $setting?->exists ? 'Configuration saved' : 'Setup needed' }}</span>
    </div>
    <div class="profile-layout">
        <section class="settings-panel">
            <form method="POST" action="{{ route('email-settings.update') }}">
                @csrf @method('PUT')
                <section class="settings-section"><div class="settings-section-heading"><span class="settings-step">01</span><div><h3 class="font-semibold">Server connection</h3><p class="text-xs text-slate-500">Use the SMTP details supplied by your email provider.</p></div></div><div class="settings-fields">
                    <label class="settings-field"><span>SMTP host *</span>
                        <input type="text" name="host" required maxlength="255" value="{{ old('host', $setting?->host) }}" placeholder="smtp.example.com">
                        @error('host')<small class="text-red-600">{{ $message }}</small>@enderror
                    </label>
                    <label class="settings-field"><span>Port *</span>
                        <input type="number" name="port" required min="1" max="65535" value="{{ old('port', $setting?->port ?? 587) }}" placeholder="587">
                        @error('port')<small class="text-red-600">{{ $message }}</small>@enderror
                    </label>
                    <label class="settings-field"><span>Encryption</span>
                        <select name="encryption"><option value="" @selected(old('encryption', $setting?->encryption) === null || old('encryption', $setting?->encryption) === '')>Automatic / none</option><option value="tls" @selected(old('encryption', $setting?->encryption) === 'tls')>TLS / STARTTLS</option><option value="ssl" @selected(old('encryption', $setting?->encryption) === 'ssl')>SSL / TLS</option></select>
                        @error('encryption')<small class="text-red-600">{{ $message }}</small>@enderror
                    </label>
                </div></section>
                <section class="settings-section"><div class="settings-section-heading"><span class="settings-step">02</span><div><h3 class="font-semibold">Mailbox credentials</h3><p class="text-xs text-slate-500">Sign in with your sending mailbox.</p></div></div><div class="settings-fields">
                    <label class="settings-field"><span>Username</span>
                        <input type="text" name="username" maxlength="255" value="{{ old('username', $setting?->username) }}" placeholder="hello@example.com" autocomplete="username">
                        @error('username')<small class="text-red-600">{{ $message }}</small>@enderror
                    </label>
                    <label class="settings-field"><span>Password</span>
                        <input type="password" name="password" maxlength="255" autocomplete="new-password" placeholder="{{ $setting?->exists ? 'Leave blank to keep password' : 'Mailbox password' }}"><small>Leave blank to keep your saved password.</small>
                        @error('password')<small class="text-red-600">{{ $message }}</small>@enderror
                    </label>
                </div></section>
                <section class="settings-section"><div class="settings-section-heading"><span class="settings-step">03</span><div><h3 class="font-semibold">Sender identity</h3><p class="text-xs text-slate-500">The name and address your customers will see.</p></div></div><div class="settings-fields">
                    <label class="settings-field"><span>From name *</span>
                        <input type="text" name="from_name" required maxlength="255" value="{{ old('from_name', $setting?->from_name) }}" placeholder="Your company">
                        @error('from_name')<small class="text-red-600">{{ $message }}</small>@enderror
                    </label>
                    <label class="settings-field"><span>From email *</span>
                        <input type="email" name="from_address" required maxlength="255" value="{{ old('from_address', $setting?->from_address) }}" placeholder="hello@example.com">
                        @error('from_address')<small class="text-red-600">{{ $message }}</small>@enderror
                    </label>
                </div></section>
                <div class="settings-footer"><span class="text-xs text-slate-500">Changes apply to future sends.</span><button class="rounded-xl bg-violet-600 px-5 py-3 text-sm font-semibold hover:bg-violet-700">Save configuration</button></div>
            </form>
        </section>
        <aside class="settings-guide">
            <img src="{{ asset('images/mailflow-envelope.svg') }}" width="44" height="44" alt="">
            <h3 class="mt-4 font-semibold">A recognizable sender</h3>
            <p class="mt-2 text-sm text-slate-500">Use your company name and a mailbox your customers recognize.</p>
            <div class="settings-note"><strong>Mailbox password</strong><p>Credentials are encrypted when stored. Your saved password is never shown here.</p></div>
            <div class="settings-note"><strong>Before you send</strong><p>Confirm the host, port and encryption with your provider. Saving these settings does not test the connection.</p></div>
        </aside>
    </div>
</x-layouts.app>
