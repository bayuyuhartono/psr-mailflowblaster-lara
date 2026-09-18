<x-layouts.app title="Contacts" heading="Contacts">
    <div class="contact-directory">
    <section class="overflow-hidden rounded-2xl bg-slate-950 text-white shadow-sm">
        <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-violet-300">Audience directory</p>
                <h2 class="mt-2 text-2xl font-bold">Your customer community</h2>
                <p class="mt-2 text-sm text-slate-400">Keep customer details organized, control who receives campaigns, and grow your audience with CSV imports.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('contacts.import.create') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold">Import contacts</a>
                <a href="{{ route('contacts.create') }}" class="rounded-xl bg-violet-600 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-violet-700">+ Add contact</a>
            </div>
        </div>
        <div class="grid border-t border-slate-200 sm:grid-cols-3">
            <div class="border-b border-slate-200 px-6 py-4 sm:border-b-0 sm:border-r"><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">All contacts</p><p class="mt-1 text-2xl font-bold">{{ number_format($totalContactCount) }}</p></div>
            <div class="border-b border-slate-200 px-6 py-4 sm:border-b-0 sm:border-r"><p class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Active audience</p><p class="mt-1 text-2xl font-bold">{{ number_format($activeContactCount) }}</p></div>
            <div class="px-6 py-4"><p class="text-xs font-semibold uppercase tracking-wider text-red-400">On hold</p><p class="mt-1 text-2xl font-bold">{{ number_format($heldContactCount) }}</p></div>
        </div>
    </section>

    <div class="contact-tools mt-6 grid gap-4">
        <form action="{{ route('contacts.index') }}" class="flex gap-2 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
            <div class="grid min-w-0 flex-1 gap-1">
                <label for="contact-search" class="px-1 text-xs font-semibold text-slate-500">Find a contact</label>
                <input id="contact-search" name="search" value="{{ request('search') }}" placeholder="Name, email, or company" class="min-w-0 rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100">
            </div>
            <button class="self-end rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Search</button>
        </form>

    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-2 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div><h3 class="font-semibold">Contact directory</h3><p class="mt-1 text-xs text-slate-500">Only active contacts are included when you blast a campaign.</p></div>
            @if (request('search'))
                <a href="{{ route('contacts.index') }}" class="text-sm font-semibold text-violet-800 hover:text-violet-800">Clear search</a>
            @endif
        </div>

        <div class="grid gap-3 bg-slate-50 p-3 sm:p-4">
            @forelse ($contacts as $contact)
                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex min-w-0 items-start gap-3">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-violet-50 text-sm font-bold text-violet-800">{{ str($contact->name)->substr(0, 1)->upper() }}</div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-semibold text-slate-900">{{ $contact->name }}</h4>
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $contact->is_on_hold ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-700' }}">{{ $contact->is_on_hold ? 'On hold' : 'Active' }}</span>
                                    <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">{{ $contact->level }}</span>
                                </div>
                                <p class="mt-1 text-sm text-slate-600">{{ $contact->company }}</p>
                                <div class="mt-2 flex flex-wrap gap-3 text-xs text-slate-500"><a href="mailto:{{ $contact->email }}" class="hover:text-violet-800">{{ $contact->email }}</a><span>{{ $contact->phone ?: 'No phone number' }}</span></div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 lg:justify-end">
                            @if ($contact->is_on_hold)
                                <form method="POST" action="{{ route('contact-holds.destroy', $contact) }}">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-slate-100" aria-label="Release {{ $contact->name }} from hold">Release</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('contact-holds.store', $contact) }}">
                                    @csrf
                                    <button class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800 hover:bg-slate-100" aria-label="Hold {{ $contact->name }}">Hold</button>
                                </form>
                            @endif
                            <form method="GET" action="{{ route('contacts.edit', $contact) }}"><button class="rounded-lg border border-slate-300 bg-violet-50 px-3 py-2 text-xs font-semibold text-violet-800 hover:bg-slate-100" aria-label="Edit {{ $contact->name }}">Edit</button></form>
                            <form method="POST" action="{{ route('contacts.destroy', $contact) }}" onsubmit="return confirm('Delete this contact?')">
                                @csrf @method('DELETE')
                                <button class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-slate-100" aria-label="Delete {{ $contact->name }}">Delete</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-slate-200 bg-white px-5 py-14 text-center">
                    <div class="mx-auto grid h-10 w-10 place-items-center rounded-xl bg-violet-50 font-bold text-violet-800">+</div>
                    <p class="mt-4 font-semibold text-slate-700">{{ request('search') ? 'No matching contacts' : 'No contacts yet' }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ request('search') ? 'Try a different name, email, or company.' : 'Add your first customer or import a CSV file.' }}</p>
                    @unless (request('search'))
                        <a href="{{ route('contacts.create') }}" class="mt-4 inline-block rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Add contact</a>
                    @endunless
                </div>
            @endforelse
        </div>

        @if ($contacts->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $contacts->links() }}</div>
        @endif
    </section>
    </div>
</x-layouts.app>
