@csrf
@if ($contact->exists) @method('PUT') @endif
<div class="grid gap-5 sm:grid-cols-2">
    @foreach ([['name', 'Name', 'Jane Doe'], ['level', 'Level', 'Gold'], ['company', 'Company', 'Acme Ltd'], ['email', 'Email', 'jane@example.com'], ['phone', 'Phone', '+62 812 3456 7890']] as [$field, $label, $placeholder])
        <label class="grid gap-2 {{ $field === 'phone' ? 'sm:col-span-2' : '' }}">
            <span class="text-sm font-medium text-slate-700">{{ $label }}{{ $field !== 'phone' ? ' *' : '' }}</span>
            <input type="{{ $field === 'email' ? 'email' : 'text' }}" name="{{ $field }}" value="{{ old($field, $contact->{$field}) }}" placeholder="{{ $placeholder }}" {{ $field !== 'phone' ? 'required' : '' }} class="rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-violet-500 focus:ring-4 focus:ring-violet-100">
        </label>
    @endforeach
</div>
<div class="mt-7 flex gap-3">
    <button class="rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Save contact</button>
    <a href="{{ route('contacts.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
</div>
