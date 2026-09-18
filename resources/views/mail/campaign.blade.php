<x-mail::message>
# Hello {{ $contactName }},

{!! nl2br(e($personalizedBody())) !!}

Thanks,<br>
{{ config('mail.from.name') }}
</x-mail::message>
