<x-layouts.app title="Campaigns" heading="Campaigns">
    <div class="mb-6 flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div><h2 class="text-2xl font-bold">Email campaigns</h2><p class="mt-1 text-sm text-slate-500">Create drafts, review content, and track every blast.</p></div>
        <a href="{{ route('campaigns.create') }}" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">+ Create campaign</a>
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4"><h2 class="font-semibold">All campaigns</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3">Campaign</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Delivery</th><th class="px-5 py-3">Updated</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($campaigns as $campaign)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4"><strong class="block">{{ $campaign->name }}</strong><span class="block text-xs text-slate-500">{{ $campaign->subject }}</span><span class="text-xs text-slate-400">by {{ $campaign->creator?->name ?? 'Unknown' }}</span></td>
                            <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $campaign->status === 'draft' ? 'bg-amber-50 text-amber-800' : 'bg-slate-100 text-slate-700' }}">{{ str($campaign->status)->replace('_', ' ')->title() }}</span></td>
                            <td class="px-5 py-4 text-slate-600">{{ $campaign->status === 'draft' ? 'Not sent' : $campaign->sent_count.' sent · '.$campaign->failed_count.' failed / '.$campaign->recipient_count }}</td>
                            <td class="px-5 py-4 text-slate-500">{{ $campaign->updated_at->diffForHumans() }}</td>
                            <td class="px-5 py-4">
                                @if ($campaign->status === 'draft')
                                    <div class="flex justify-end gap-2">
                                        <form method="GET" action="{{ route('campaigns.edit', $campaign) }}"><button class="rounded-lg border border-slate-300 bg-violet-50 px-3 py-2 text-xs font-semibold text-violet-800 hover:bg-slate-100">Open draft</button></form>
                                        <form method="POST" action="{{ route('campaigns.destroy', $campaign) }}" onsubmit="return confirm('Delete this campaign draft?')">@csrf @method('DELETE')<button class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-slate-100">Delete</button></form>
                                    </div>
                                @else
                                    <div class="flex justify-end"><form method="GET" action="{{ route('campaigns.show', $campaign) }}"><button class="rounded-lg border border-slate-300 bg-violet-50 px-3 py-2 text-xs font-semibold text-violet-800 hover:bg-slate-100">Open report</button></form></div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-14 text-center"><p class="font-semibold text-slate-700">No campaigns yet</p><p class="mt-1 text-sm text-slate-500">Create your first campaign to get started.</p><a href="{{ route('campaigns.create') }}" class="mt-4 inline-block rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Create campaign</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($campaigns->hasPages()) <div class="border-t border-slate-200 px-5 py-4">{{ $campaigns->links() }}</div> @endif
    </section>
</x-layouts.app>
