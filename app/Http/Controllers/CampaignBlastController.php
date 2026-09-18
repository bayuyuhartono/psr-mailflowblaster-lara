<?php

namespace App\Http\Controllers;

use App\Models\CampaignDelivery;
use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailSetting;
use Illuminate\Http\RedirectResponse;

class CampaignBlastController extends Controller
{
    public function store(EmailCampaign $campaign): RedirectResponse
    {
        if (! EmailSetting::query()->exists()) {
            return redirect()->route('email-settings.edit')->withErrors(['email' => 'Configure your SMTP details before sending a campaign.']);
        }

        $successfulEmails = CampaignDelivery::query()
            ->select('contact_email')
            ->where('email_campaign_id', $campaign->id)
            ->where('status', 'successful');

        $recipientCount = Contact::query()
            ->where('is_on_hold', false)
            ->whereNotIn('email', $successfulEmails)
            ->count();

        if ($recipientCount === 0) {
            return redirect()->route('campaigns.show', $campaign)->withErrors(['contacts' => 'Every active contact has already received this campaign.']);
        }

        $campaign->update(['status' => 'sending']);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', "Sending {$recipientCount} contacts. Keep this page open until it finishes.")
            ->with('start_sending', true);
    }
}
