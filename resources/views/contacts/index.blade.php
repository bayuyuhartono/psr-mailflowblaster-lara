<x-layouts.app title="Contacts" heading="Contacts">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div><h2 class="text-2xl font-bold">Your audience</h2><p class="mt-1 text-sm text-slate-500">Manage customers and import them in bulk.</p></div>
        <a href="{{ route('contacts.create') }}" class="rounded-xl bg-violet-600 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-violet-700">+ Add contact</a>
    </div>

    <div class="mb-6 grid gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:grid-cols-[1fr_auto]">
        <form action="{{ route('contacts.index') }}" class="flex gap-2">
            <input name="search" value="{{ request('search') }}" placeholder="Search name, email, or company" class="min-w-0 flex-1 rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100">
            <button class="rounded-xl border border-slate-300 px-4 text-sm font-semibold hover:bg-slate-50">Search</button>
        </form>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('contacts.import-template') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold hover:bg-slate-50">Download template</a>
            <form method="POST" action="{{ route('contacts.import') }}" enctype="multipart/form-data" class="flex min-w-0 flex-1 flex-wrap gap-2">
                @csrf
                <input type="file" name="file" accept=".csv,text/csv" required class="min-w-0 flex-1 rounded-xl border border-slate-300 px-2 py-2 text-xs file:mr-2 file:rounded-md file:border-0 file:bg-slate-100 file:px-2 file:py-1">
                <button class="rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Import</button>
            </form>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3">Contact</th><th class="px-5 py-3">Level</th><th class="px-5 py-3">Company</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Phone</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($contacts as $contact)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4"><strong class="block">{{ $contact->name }}</strong><span class="text-slate-500">{{ $contact->email }}</span></td>
                            <td class="px-5 py-4"><span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">{{ $contact->level }}</span></td>
                            <td class="px-5 py-4 text-slate-600">{{ $contact->company }}</td><td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $contact->is_on_hold ? 'bg-amber-50 text-amber-800' : 'bg-emerald-50 text-emerald-700' }}">{{ $contact->is_on_hold ? 'On hold' : 'Active' }}</span></td><td class="px-5 py-4 text-slate-600">{{ $contact->phone ?: '—' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <form method="GET" action="{{ route('contacts.edit', $contact) }}">
                                        <button class="rounded-lg border border-slate-300 bg-violet-50 px-3 py-2 text-xs font-semibold text-violet-800 hover:bg-slate-100" aria-label="Edit {{ $contact->name }}">Edit</button>
                                    </form>
                                    <form method="POST" action="{{ route('contacts.destroy', $contact) }}" onsubmit="return confirm('Delete this contact?')">
                                        @csrf @method('DELETE')
                                        <button class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-slate-100" aria-label="Delete {{ $contact->name }}">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-14 text-center text-slate-500">No contacts yet. Add one or import a CSV file.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($contacts->hasPages()) <div class="border-t border-slate-200 px-5 py-4">{{ $contacts->links() }}</div> @endif
    </div>
</x-layouts.app>
