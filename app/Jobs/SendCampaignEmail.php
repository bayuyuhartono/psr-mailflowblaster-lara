<?php

namespace App\Jobs;

use App\Mail\CampaignMessage;
use App\Models\EmailCampaign;
use App\Models\EmailSetting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendCampaignEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public int $campaignId,
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

        $campaign->update(['status' => 'sending']);
        Mail::to($this->contactEmail, $this->contactName)->send(new CampaignMessage(
            $campaign,
            $this->contactName,
            $this->contactLevel,
            $this->contactCompany,
            $this->contactEmail,
            $this->contactPhone,
        ));
        $campaign->increment('sent_count');
        $this->markCompleteWhenFinished($campaign);
    }

    public function failed(Throwable $exception): void
    {
        $campaign = EmailCampaign::query()->find($this->campaignId);
        if ($campaign === null) {
            return;
        }

        $campaign->increment('failed_count');
        $this->markCompleteWhenFinished($campaign);
    }

    private function markCompleteWhenFinished(EmailCampaign $campaign): void
    {
        $campaign->refresh();
        if (($campaign->sent_count + $campaign->failed_count) >= $campaign->recipient_count) {
            $campaign->update(['status' => $campaign->failed_count > 0 ? 'completed_with_errors' : 'completed']);
        }
    }
}
