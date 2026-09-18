<?php

namespace App\Mail;

use App\Models\EmailCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EmailCampaign $campaign,
        public string $contactName,
        public string $contactLevel,
        public string $contactCompany,
        public string $contactEmail,
        public string $contactPhone,
    ) {}

    public function personalizedBody(): string
    {
        return $this->personalize($this->campaign->body);
    }

    public function personalizedSubject(): string
    {
        return $this->personalize($this->campaign->subject);
    }

    private function personalize(string $content): string
    {
        return str_replace(
            ['{{ name }}', '{{ level }}', '{{ company }}', '{{ email }}', '{{ phone }}'],
            [$this->contactName, $this->contactLevel, $this->contactCompany, $this->contactEmail, $this->contactPhone],
            $content,
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->personalizedSubject());
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.campaign',
            with: ['personalizedBody' => $this->personalizedBody()],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
