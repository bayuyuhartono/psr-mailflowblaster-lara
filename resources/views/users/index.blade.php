<x-layouts.app title="Users" heading="User management">
    <div class="mb-6 flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div><h2 class="text-2xl font-bold">Workspace users</h2><p class="mt-1 text-sm text-slate-500">Only super users can create or remove accounts.</p></div>
        <a href="{{ route('users.create') }}" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-700">+ Add user</a>
    </div>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3">User</th><th class="px-5 py-3">Role</th><th class="px-5 py-3">Created</th><th class="px-5 py-3 text-right">Actions</th></tr></thead><tbody class="divide-y divide-slate-100">
            @foreach ($users as $user)
                <tr><td class="px-5 py-4"><strong class="block">{{ $user->name }}</strong><span class="text-slate-500">{{ $user->email }}</span></td><td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->is_super_user ? 'bg-violet-50 text-violet-700' : 'bg-slate-100 text-slate-700' }}">{{ $user->is_super_user ? 'Super user' : 'User' }}</span></td><td class="px-5 py-4 text-slate-500">{{ $user->created_at->format('M j, Y') }}</td><td class="px-5 py-4 text-right">@if (! $user->is(auth()->user()))<form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Delete this user account?')">@csrf @method('DELETE')<button class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-slate-100" aria-label="Delete {{ $user->name }}">Delete</button></form>@else<span class="text-xs text-slate-400">Current account</span>@endif</td></tr>
            @endforeach
        </tbody></table></div>
        @if ($users->hasPages()) <div class="border-t border-slate-200 px-5 py-4">{{ $users->links() }}</div> @endif
    </div>
</x-layouts.app>
