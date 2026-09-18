<x-layouts.app title="Add User" heading="Add user">
    <div class="max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold">Account details</h2><p class="mb-6 mt-1 text-sm text-slate-500">Create a new account for this workspace.</p>
        <form method="POST" action="{{ route('users.store') }}">@csrf
            <div class="grid gap-5 sm:grid-cols-2">
                <label class="grid gap-2"><span class="text-sm font-medium">Name *</span><input name="name" value="{{ old('name') }}" required class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></label>
                <label class="grid gap-2"><span class="text-sm font-medium">Email *</span><input type="email" name="email" value="{{ old('email') }}" required class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></label>
                <label class="grid gap-2"><span class="text-sm font-medium">Password *</span><input type="password" name="password" required autocomplete="new-password" class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></label>
                <label class="grid gap-2"><span class="text-sm font-medium">Confirm password *</span><input type="password" name="password_confirmation" required autocomplete="new-password" class="rounded-xl border border-slate-300 px-3.5 py-2.5 outline-none focus:border-violet-500 focus:ring-4 focus:ring-violet-100"></label>
            </div>
            <label class="mt-5 flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4"><input type="checkbox" name="is_super_user" value="1" @checked(old('is_super_user')) class="mt-1 rounded border-slate-300"><span><strong class="block text-sm">Super user access</strong><span class="text-xs text-slate-500">Allow this user to create and remove other accounts.</span></span></label>
            <div class="mt-7 flex gap-3"><button class="rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">Create user</button><a href="{{ route('users.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a></div>
        </form>
    </div>
</x-layouts.app>
