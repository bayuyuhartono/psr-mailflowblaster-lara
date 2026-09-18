<x-layouts.app title="Campaign Report" heading="Campaign report">
    <div class="mb-6 flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div><div class="flex items-center gap-2"><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ str($campaign->status)->replace('_', ' ')->title() }}</span><span class="text-xs text-slate-400">Created by {{ $campaign->creator?->name ?? 'Unknown' }}</span></div><h2 class="mt-2 text-2xl font-bold">{{ $campaign->name }}</h2><p class="mt-1 text-sm text-slate-500">{{ $campaign->subject }}</p></div>
        <a href="{{ route('campaigns.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">← All campaigns</a>
    </div>

    @if (session('start_sending'))
        <section id="sending-progress" class="mb-6 overflow-hidden rounded-2xl border border-amber-300 bg-amber-50 shadow-sm">
            <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div><p class="text-xs font-semibold uppercase tracking-widest text-amber-800">Sending in progress</p><h3 id="sending-message" class="mt-1 font-semibold text-slate-900">Preparing the first email…</h3><p class="mt-1 text-xs text-slate-600">Keep this page open. One recipient is processed securely per request.</p></div>
                <div class="shrink-0 text-left sm:text-right"><p id="sending-count" class="text-2xl font-bold text-slate-900">0 / {{ number_format($remainingCount) }}</p><button id="resume-sending" type="button" class="mt-2 hidden rounded-lg border border-amber-400 bg-white px-3 py-1.5 text-xs font-semibold text-amber-900 hover:bg-amber-100">Resume sending</button></div>
            </div>
            <div class="h-2 bg-amber-100"><div id="sending-bar" class="h-full w-0 bg-amber-500 transition-all duration-300"></div></div>
        </section>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Recipients</p><p class="mt-2 text-3xl font-bold">{{ number_format($campaign->recipient_count) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Successful</p><p class="mt-2 text-3xl font-bold text-emerald-700">{{ number_format($campaign->sent_count) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Failed</p><p class="mt-2 text-3xl font-bold text-red-600">{{ number_format($campaign->failed_count) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Not sent</p><p class="mt-2 text-3xl font-bold text-amber-800">{{ number_format($remainingCount) }}</p></div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1.2fr_.8fr]">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4"><h3 class="font-semibold">Delivery results</h3><p class="mt-1 text-xs text-slate-500">A permanent record of every recipient attempt.</p></div>
            <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3">Contact</th><th class="px-5 py-3">Result</th><th class="px-5 py-3">Sent by</th><th class="px-5 py-3">Time</th></tr></thead><tbody class="divide-y divide-slate-100">
                @forelse ($deliveries as $delivery)
                    <tr><td class="px-5 py-4"><strong class="block">{{ $delivery->contact_name }}</strong><span class="text-xs text-slate-500">{{ $delivery->contact_email }} · {{ $delivery->contact_company }}</span>@if ($delivery->error_message)<span class="mt-1 block text-xs text-red-600">{{ $delivery->error_message }}</span>@endif</td><td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $delivery->status === 'successful' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">{{ str($delivery->status)->title() }}</span></td><td class="px-5 py-4 text-slate-600">{{ $delivery->sender?->name ?? 'Unknown' }}</td><td class="px-5 py-4 text-slate-500">{{ ($delivery->sent_at ?? $delivery->attempted_at)->format('M j, Y H:i') }}</td></tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-12 text-center text-slate-500">No delivery attempts have been recorded.</td></tr>
                @endforelse
            </tbody></table></div>
            @if ($deliveries->hasPages()) <div class="border-t border-slate-200 px-5 py-4">{{ $deliveries->links() }}</div> @endif
        </section>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm"><h3 class="font-semibold">Retry unsent contacts</h3><p class="mt-2 text-sm text-slate-600">Only active contacts without a successful delivery will be attempted. Successful recipients are always skipped.</p><form method="POST" action="{{ route('campaigns.blast', $campaign) }}" class="mt-4" onsubmit="return confirm('Retry this campaign for unsent contacts only?')">@csrf<button @disabled(!$isConfigured || $remainingCount === 0) class="w-full rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-violet-700 disabled:cursor-not-allowed disabled:bg-slate-300">Retry {{ number_format($remainingCount) }} unsent</button></form></div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h3 class="font-semibold">Copy campaign</h3><p class="mt-2 text-sm text-slate-500">Reuse the subject and message in a new editable draft.</p><form method="POST" action="{{ route('campaigns.copy', $campaign) }}" class="mt-4 grid gap-3">@csrf<label class="grid gap-2"><span class="text-xs font-medium">New campaign name</span><input name="name" required maxlength="255" placeholder="New internal name" class="rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></label><button class="rounded-xl border border-slate-300 bg-violet-50 px-4 py-2.5 text-sm font-semibold text-violet-800 hover:bg-slate-100">Create copy</button></form></div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Original content</p><p class="mt-3 text-sm font-semibold">{{ $campaign->subject }}</p><p class="mt-2 text-sm text-slate-600">{{ str($campaign->body)->limit(240) }}</p></div>
        </aside>
    </div>
@if (session('start_sending'))
    <script>
        (() => {
            const total = {{ $remainingCount }};
            const endpoint = @json(route('campaigns.send-next', $campaign));
            const csrfToken = @json(csrf_token());
            const runToken = crypto.randomUUID();
            const message = document.getElementById('sending-message');
            const count = document.getElementById('sending-count');
            const bar = document.getElementById('sending-bar');
            const resume = document.getElementById('resume-sending');
            let isSending = false;

            const sendNext = async () => {
                if (isSending) return;
                isSending = true;
                resume.classList.add('hidden');

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ run_token: runToken }),
                    });
                    const result = await response.json();

                    if (!response.ok) {
                        throw new Error(result.message || 'Sending could not continue.');
                    }

                    const processed = Math.max(0, total - result.remaining);
                    const percentage = total === 0 ? 100 : Math.round((processed / total) * 100);
                    count.textContent = `${processed} / ${total}`;
                    bar.style.width = `${percentage}%`;

                    if (result.has_more) {
                        message.textContent = `Sent: ${result.sent} · Failed: ${result.failed} · Continuing…`;
                        isSending = false;
                        window.setTimeout(sendNext, 150);
                        return;
                    }

                    message.textContent = `Finished. Sent: ${result.sent} · Failed: ${result.failed}`;
                    bar.style.width = '100%';
                    window.setTimeout(() => window.location.reload(), 900);
                } catch (error) {
                    message.textContent = error.message + ' You can safely resume.';
                    resume.classList.remove('hidden');
                } finally {
                    isSending = false;
                }
            };

            resume.addEventListener('click', sendNext);
            sendNext();
        })();
    </script>
@endif
</x-layouts.app>
