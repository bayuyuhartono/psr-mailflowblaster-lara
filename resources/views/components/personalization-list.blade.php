@php
    $personalizationTokens = [
        'Contact name' => '{'.'{ name }'.'}',
        'Customer level' => '{'.'{ level }'.'}',
        'Company' => '{'.'{ company }'.'}',
        'Email' => '{'.'{ email }'.'}',
        'Phone' => '{'.'{ phone }'.'}',
    ];
@endphp

<div class="mt-4 grid gap-2" data-personalization-list>
    @foreach ($personalizationTokens as $label => $token)
        <button type="button" data-copy-token="{{ $token }}" class="flex w-full items-center justify-between rounded-lg border border-slate-200 bg-amber-50 px-3 py-2 text-left hover:bg-amber-100" title="Copy {{ $token }}">
            <span class="text-xs text-slate-500">{{ $label }}</span>
            <span class="flex items-center gap-2">
                <code class="text-xs font-semibold text-violet-800">{{ $token }}</code>
                <span data-copy-label class="text-xs font-semibold text-slate-500">Copy</span>
            </span>
        </button>
    @endforeach
</div>

@once
    <script>
        document.addEventListener('click', async (event) => {
            const button = event.target.closest('[data-copy-token]');
            if (!button) return;

            const token = button.dataset.copyToken;
            const label = button.querySelector('[data-copy-label]');

            try {
                await navigator.clipboard.writeText(token);
            } catch (error) {
                const input = document.createElement('textarea');
                input.value = token;
                input.style.position = 'fixed';
                input.style.opacity = '0';
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                input.remove();
            }

            label.textContent = 'Copied!';
            window.setTimeout(() => label.textContent = 'Copy', 1200);
        });
    </script>
@endonce
