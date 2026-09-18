<x-layouts.app title="Add Contact" heading="Add contact">
    <div class="max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold">Contact details</h2>
        <p class="mb-6 mt-1 text-sm text-slate-500">Add a customer to your campaign audience.</p>
        <form method="POST" action="{{ route('contacts.store') }}">@include('contacts._form', ['contact' => new \App\Models\Contact])</form>
    </div>
</x-layouts.app>
