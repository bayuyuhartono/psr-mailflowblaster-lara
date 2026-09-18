@csrf
@if ($contact->exists) @method('PUT') @endif

<div class="profile-fields grid gap-6">
    <section>
        <div class="mb-4 flex items-center gap-3">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-violet-50 text-sm font-bold text-violet-800">01</span>
            <div><h3 class="font-semibold">Identity</h3><p class="text-xs text-slate-500">Who this customer is and where they work.</p></div>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <label class="grid gap-2">
                <span class="text-sm font-medium text-slate-700">Full name *</span>
                <input name="name" required maxlength="255" value="{{ old('name', $contact->name) }}" placeholder="Jane Doe" class="rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100">
                @error('name')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
            </label>
            <label class="grid gap-2">
                <span class="text-sm font-medium text-slate-700">Customer level *</span>
                <input name="level" required maxlength="100" value="{{ old('level', $contact->level) }}" placeholder="Gold" class="rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100">
                @error('level')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
            </label>
            <label class="grid gap-2 sm:col-span-2">
                <span class="text-sm font-medium text-slate-700">Company *</span>
                <input name="company" required maxlength="255" value="{{ old('company', $contact->company) }}" placeholder="Acme Ltd" class="rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100">
                @error('company')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
            </label>
        </div>
    </section>

    <div class="border-t border-slate-200"></div>

    <section>
        <div class="mb-4 flex items-center gap-3">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-violet-50 text-sm font-bold text-violet-800">02</span>
            <div><h3 class="font-semibold">Contact details</h3><p class="text-xs text-slate-500">Where campaigns and follow-ups can reach them.</p></div>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <label class="grid gap-2">
                <span class="text-sm font-medium text-slate-700">Email address *</span>
                <input type="email" name="email" required maxlength="255" value="{{ old('email', $contact->email) }}" placeholder="jane@example.com" class="rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100">
                @error('email')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
            </label>
            <label class="grid gap-2">
                <span class="text-sm font-medium text-slate-700">Phone number</span>
                <input name="phone" maxlength="50" value="{{ old('phone', $contact->phone) }}" placeholder="+62 812 3456 7890" class="rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100">
                @error('phone')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
            </label>
        </div>
    </section>

    <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
        <input type="hidden" name="is_on_hold" value="0">
        <input type="checkbox" name="is_on_hold" value="1" @checked(old('is_on_hold', $contact->is_on_hold)) class="mt-1 rounded border-slate-300">
        <span><strong class="block text-sm text-slate-900">Place this contact on hold</strong><span class="mt-1 block text-xs leading-5 text-slate-500">The contact remains saved, but every campaign will skip this email address until released.</span></span>
    </label>
</div>

<div class="mt-7 flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
    <a href="{{ route('contacts.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
    <button class="rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">{{ $contact->exists ? 'Save changes' : 'Create contact' }}</button>
</div>
