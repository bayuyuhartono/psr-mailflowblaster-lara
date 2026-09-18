<x-layouts.app title="Import Contacts" heading="Import contacts">
    <div class="settings-heading">
        <div><p class="text-xs font-semibold uppercase tracking-widest text-violet-600">Grow your audience</p><h2 class="mt-2 text-2xl font-bold">Bring your contacts together</h2><p class="mt-2 text-sm text-slate-500">Upload a CSV file to add customers or update existing contacts by email.</p></div>
        <a href="{{ route('contacts.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold">← Contact directory</a>
    </div>
    <div class="profile-layout">
        <section class="settings-panel">
            <div class="settings-section">
                <div class="settings-section-heading"><span class="settings-step">01</span><div><h3 class="font-semibold">Prepare your file</h3><p class="text-xs text-slate-500">Start with our template and keep the column headers unchanged.</p></div></div>
                <a href="{{ route('contacts.import-template') }}" class="inline-block rounded-xl border border-slate-300 bg-violet-50 px-4 py-2.5 text-sm font-semibold text-violet-800">Download CSV template</a>
            </div>
            <form method="POST" action="{{ route('contacts.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="settings-section">
                    <div class="settings-section-heading"><span class="settings-step">02</span><div><h3 class="font-semibold">Upload contacts</h3><p class="text-xs text-slate-500">Choose your completed CSV file.</p></div></div>
                    <label class="settings-field"><span>Contacts CSV file *</span><input type="file" name="file" accept=".csv,text/csv" required>@error('file')<small class="text-red-600">{{ $message }}</small>@enderror</label>
                </div>
                <div class="settings-footer"><a href="{{ route('contacts.index') }}" class="text-sm text-slate-600">Cancel</a><button class="rounded-xl bg-violet-600 px-5 py-3 text-sm font-semibold hover:bg-violet-700">Import contacts</button></div>
            </form>
        </section>
        <aside class="settings-guide">
            <h3 class="font-semibold">CSV checklist</h3>
            <p class="mt-2 text-sm text-slate-500">Include name, level, company, email, phone, and on_hold in that order. Phone may be blank.</p>
            <div class="settings-note"><strong>Hold status</strong><p>Use “yes” to skip a contact during campaigns, or “no” to keep them active.</p></div>
            <div class="settings-note"><strong>Matching email addresses</strong><p>An existing email updates that contact’s details, including their hold status. New email addresses create new contacts.</p></div>
        </aside>
    </div>
</x-layouts.app>
