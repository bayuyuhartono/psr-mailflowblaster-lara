<x-mail::message>
# Hello {{ $contactName }},

{!! nl2br(e($campaign->body)) !!}

Thanks,<br>
{{ config('mail.from.name') }}
</x-mail::message>
