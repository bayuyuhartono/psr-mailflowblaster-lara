<x-layouts.app title="Edit Contact" heading="Edit contact">
    <div class="max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold">Contact details</h2>
        <p class="mb-6 mt-1 text-sm text-slate-500">Update customer information.</p>
        <form method="POST" action="{{ route('contacts.update', $contact) }}">@include('contacts._form')</form>
    </div>
</x-layouts.app>
