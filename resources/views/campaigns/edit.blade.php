<x-layouts.app title="Campaign Draft" heading="Campaign draft">
    <div class="mb-6 flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div><div class="flex items-center gap-2"><span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800">Draft</span><span class="text-xs text-slate-400">Last saved {{ $campaign->updated_at->diffForHumans() }}</span></div><h2 class="mt-2 text-2xl font-bold">Review before you blast</h2><p class="mt-1 text-sm text-slate-500">Fine-tune your content, check the audience, then send when ready.</p></div>
        <a href="{{ route('campaigns.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">← All campaigns</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.2fr_.8fr]">
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5"><p class="text-xs font-semibold uppercase tracking-widest text-violet-600">Email content</p><h3 class="mt-1 text-lg font-semibold">Subject and message</h3></div>
            <form method="POST" action="{{ route('campaigns.update', $campaign) }}" class="p-6">
                @csrf @method('PUT')
                <label class="grid gap-2"><span class="text-sm font-medium">Campaign name *</span><input name="name" required maxlength="255" value="{{ old('name', $campaign->name) }}" class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"><span class="text-xs text-slate-400">Internal only. Recipients will not see this name.</span></label>
                <label class="mt-5 grid gap-2"><span class="text-sm font-medium">Email subject *</span><input name="subject" required maxlength="255" value="{{ old('subject', $campaign->subject) }}" class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></label>
                <label class="mt-5 grid gap-2"><span class="text-sm font-medium">Message *</span><textarea name="body" required rows="15" maxlength="20000" class="resize-y rounded-xl border border-slate-300 px-3.5 py-3 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100">{{ old('body', $campaign->body) }}</textarea></label>
                <div class="mt-6 flex flex-wrap items-center gap-3"><button class="rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Save changes</button><span class="text-xs text-slate-400">Save before blasting to include your latest edits.</span></div>
            </form>
        </section>

        <aside class="space-y-6">
            <div class="overflow-hidden rounded-2xl bg-slate-950 text-white shadow-sm">
                <div class="p-6"><p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Active audience</p><p class="mt-2 text-4xl font-bold">{{ number_format($contactCount) }}</p><p class="mt-2 text-sm text-slate-400">Ready to receive this campaign</p></div>
                <div class="border-t border-slate-200 px-6 py-4"><p class="text-sm text-slate-400"><strong class="text-white">{{ number_format($heldContactCount) }}</strong> on hold will be skipped</p><a href="{{ route('contacts.index') }}" class="mt-2 inline-block text-sm font-semibold text-violet-300 hover:text-violet-200">Manage audience →</a></div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start gap-3"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-violet-600 text-sm font-bold text-white">&#123;&#125;</span><div><h3 class="font-semibold">Personalization</h3><p class="mt-1 text-xs leading-5 text-slate-500">Copy these tokens into the subject or message.</p></div></div>
                <div class="mt-4 grid gap-2">
                    @verbatim
                        <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-amber-50 px-3 py-2"><span class="text-xs text-slate-500">Contact name</span><code class="text-xs font-semibold text-violet-800">{{ name }}</code></div>
                        <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-amber-50 px-3 py-2"><span class="text-xs text-slate-500">Customer level</span><code class="text-xs font-semibold text-violet-800">{{ level }}</code></div>
                        <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-amber-50 px-3 py-2"><span class="text-xs text-slate-500">Company</span><code class="text-xs font-semibold text-violet-800">{{ company }}</code></div>
                        <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-amber-50 px-3 py-2"><span class="text-xs text-slate-500">Email</span><code class="text-xs font-semibold text-violet-800">{{ email }}</code></div>
                        <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-amber-50 px-3 py-2"><span class="text-xs text-slate-500">Phone</span><code class="text-xs font-semibold text-violet-800">{{ phone }}</code></div>
                    @endverbatim
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <h3 class="font-semibold text-slate-900">Before sending</h3>
                <ul class="mt-3 grid gap-2 text-sm text-slate-600"><li>✓ Review subject and message</li><li>✓ Save your latest changes</li><li>✓ Keep this page open while sending</li></ul>
                @unless ($isConfigured)<div class="mt-4 rounded-lg border border-amber-200 bg-white p-3 text-xs text-amber-800">SMTP is not configured. <a href="{{ route('email-settings.edit') }}" class="font-semibold underline">Configure email first</a>.</div>@endunless
                <form method="POST" action="{{ route('campaigns.blast', $campaign) }}" class="mt-5" onsubmit="return confirm('Blast this campaign to all active contacts? This cannot be undone.')">
                    @csrf
                    <button @disabled(!$isConfigured || $contactCount === 0) class="w-full rounded-xl bg-violet-600 px-5 py-3 text-sm font-semibold text-white hover:bg-violet-700 disabled:cursor-not-allowed disabled:bg-slate-300">Blast email now</button>
                </form>
                <p class="mt-3 text-center text-xs text-slate-500">Sending permanently locks editing and deletion.</p>
            </div>
        </aside>
    </div>
</x-layouts.app>
