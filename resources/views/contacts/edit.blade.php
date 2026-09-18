<x-layouts.app title="Edit Contact" heading="Edit contact">
    <div class="mb-6 flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div><p class="text-xs font-semibold uppercase tracking-widest text-violet-600">Customer profile</p><h2 class="mt-1 text-2xl font-bold">Edit {{ $contact->name }}</h2><p class="mt-1 text-sm text-slate-500">Keep their details and campaign availability up to date.</p></div>
        <a href="{{ route('contacts.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">← Contact directory</a>
    </div>

    <div class="profile-layout">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <form method="POST" action="{{ route('contacts.update', $contact) }}">@include('contacts._form')</form>
        </section>
        <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:w-72">
            <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Current status</p>
            <div class="mt-3 flex items-center gap-2"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $contact->is_on_hold ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-700' }}">{{ $contact->is_on_hold ? 'On hold' : 'Active' }}</span><span class="text-sm text-slate-500">{{ $contact->is_on_hold ? 'Campaigns skip this contact' : 'Available for campaigns' }}</span></div>
            <div class="mt-5 border-t border-slate-200 pt-4"><p class="text-xs text-slate-500">Created</p><p class="mt-1 text-sm font-semibold">{{ $contact->created_at->format('M j, Y') }}</p></div>
        </aside>
    </div>
</x-layouts.app>
