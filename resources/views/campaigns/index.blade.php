<x-layouts.app title="Campaigns" heading="Campaigns">
    <div class="grid gap-6 lg:grid-cols-[1.2fr_.8fr]">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6 flex items-start justify-between gap-4"><div><h2 class="text-xl font-bold">Create a blast</h2><p class="mt-1 text-sm text-slate-500">Send one email to every contact in your audience.</p></div><span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">{{ number_format($contactCount) }} recipients</span></div>
            @unless ($isConfigured)<div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">SMTP is not configured. <a class="font-semibold underline" href="{{ route('email-settings.edit') }}">Configure email first</a>.</div>@endunless
            <form method="POST" action="{{ route('campaigns.store') }}">
                @csrf
                <label class="grid gap-2"><span class="text-sm font-medium">Subject *</span><input name="subject" required maxlength="255" value="{{ old('subject') }}" placeholder="A special update for you" class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></label>
                <label class="mt-5 grid gap-2"><span class="text-sm font-medium">Message *</span><textarea name="body" required rows="11" maxlength="20000" placeholder="Write your email message…" class="resize-y rounded-xl border border-slate-300 px-3.5 py-3 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100">{{ old('body') }}</textarea><span class="text-xs text-slate-400">Each email starts with the contact’s name.</span></label>
                <button @disabled(!$isConfigured || $contactCount === 0) class="mt-6 w-full rounded-xl bg-violet-600 px-5 py-3 text-sm font-semibold text-white hover:bg-violet-700 disabled:cursor-not-allowed disabled:bg-slate-300">Queue email blast</button>
            </form>
        </section>

        <aside class="space-y-6">
            <div class="rounded-2xl bg-slate-950 p-6 text-white shadow-sm"><p class="text-sm text-slate-400">Audience size</p><p class="mt-2 text-4xl font-bold">{{ number_format($contactCount) }}</p><p class="mt-2 text-sm text-slate-400">Every saved contact will receive this campaign.</p><a href="{{ route('contacts.index') }}" class="mt-5 inline-block text-sm font-semibold text-violet-300 hover:text-violet-200">Manage contacts →</a></div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h3 class="font-semibold">Before sending</h3><ul class="mt-4 grid gap-3 text-sm text-slate-600"><li>✓ Confirm your SMTP configuration</li><li>✓ Review the subject and message</li><li>✓ Start a queue worker in production</li></ul></div>
        </aside>
    </div>

    <section class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4"><h2 class="font-semibold">Campaign history</h2></div>
        <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3">Subject</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Progress</th><th class="px-5 py-3">Created</th></tr></thead><tbody class="divide-y divide-slate-100">
            @forelse ($campaigns as $campaign)<tr><td class="px-5 py-4 font-medium">{{ $campaign->subject }}</td><td class="px-5 py-4"><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ str($campaign->status)->replace('_', ' ')->title() }}</span></td><td class="px-5 py-4 text-slate-600">{{ $campaign->sent_count }} sent · {{ $campaign->failed_count }} failed / {{ $campaign->recipient_count }}</td><td class="px-5 py-4 text-slate-500">{{ $campaign->created_at->diffForHumans() }}</td></tr>
            @empty <tr><td colspan="4" class="px-5 py-12 text-center text-slate-500">No campaigns sent yet.</td></tr> @endforelse
        </tbody></table></div>
        @if ($campaigns->hasPages()) <div class="border-t border-slate-200 px-5 py-4">{{ $campaigns->links() }}</div> @endif
    </section>
</x-layouts.app>
