<x-layouts.app title="Add Contact" heading="Add contact">
    <div class="mb-6 flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div><p class="text-xs font-semibold uppercase tracking-widest text-violet-600">New audience member</p><h2 class="mt-1 text-2xl font-bold">Add a customer</h2><p class="mt-1 text-sm text-slate-500">Create a complete contact profile for future campaigns.</p></div>
        <a href="{{ route('contacts.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">← Contact directory</a>
    </div>

    <div class="profile-layout">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <form method="POST" action="{{ route('contacts.store') }}">@include('contacts._form', ['contact' => new \App\Models\Contact])</form>
        </section>
        <aside class="rounded-2xl bg-slate-950 p-5 text-white shadow-sm lg:w-72">
            <p class="text-xs font-semibold uppercase tracking-widest text-violet-300">Good to know</p>
            <h3 class="mt-2 font-semibold">Ready for personalization</h3>
            <p class="mt-2 text-sm leading-6 text-slate-400">These contact fields become personalization tokens you can use inside campaign subjects and messages.</p>
            <div class="mt-5 grid gap-2 text-sm text-slate-300"><p>✓ Name and company</p><p>✓ Customer level</p><p>✓ Email and phone</p></div>
        </aside>
    </div>
</x-layouts.app>
