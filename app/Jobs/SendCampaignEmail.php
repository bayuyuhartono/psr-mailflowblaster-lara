<?php

namespace App\Jobs;

use App\Mail\CampaignMessage;
use App\Models\CampaignDelivery;
use App\Models\EmailCampaign;
use App\Models\EmailSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendCampaignEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public int $campaignId,
        public int $contactId,
        public int $sentBy,
        public string $attemptToken,
        public string $contactName,
        public string $contactLevel,
        public string $contactCompany,
        public string $contactEmail,
        public string $contactPhone,
    ) {}

    public function handle(): void
    {
        $campaign = EmailCampaign::query()->findOrFail($this->campaignId);
        $setting = EmailSetting::query()->firstOrFail();

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $setting->host,
            'mail.mailers.smtp.port' => $setting->port,
            'mail.mailers.smtp.scheme' => $setting->encryption === 'ssl' ? 'smtps' : null,
            'mail.mailers.smtp.username' => $setting->username,
            'mail.mailers.smtp.password' => $setting->password,
            'mail.from.address' => $setting->from_address,
            'mail.from.name' => $setting->from_name,
        ]);
        Mail::purge('smtp');

        Mail::to($this->contactEmail, $this->contactName)->send(new CampaignMessage(
            $campaign,
            $this->contactName,
            $this->contactLevel,
            $this->contactCompany,
            $this->contactEmail,
            $this->contactPhone,
        ));

        CampaignDelivery::query()->updateOrCreate(
            ['email_campaign_id' => $campaign->id, 'contact_email' => $this->contactEmail],
            [
                'contact_id' => $this->contactId,
                'sent_by' => $this->sentBy,
                'contact_name' => $this->contactName,
                'contact_company' => $this->contactCompany,
                'status' => 'successful',
                'attempt_token' => $this->attemptToken,
                'error_message' => null,
                'attempted_at' => now(),
                'sent_at' => now(),
            ],
        );
    }
}
