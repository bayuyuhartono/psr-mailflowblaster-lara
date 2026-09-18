<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailCampaignRequest;
use App\Jobs\SendCampaignEmail;
use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmailCampaignController extends Controller
{
    public function index(): View
    {
        return view('campaigns.index', [
            'campaigns' => EmailCampaign::query()->latest()->paginate(10),
            'contactCount' => Contact::query()->where('is_on_hold', false)->count(),
            'heldContactCount' => Contact::query()->where('is_on_hold', true)->count(),
            'isConfigured' => EmailSetting::query()->exists(),
        ]);
    }

    public function store(StoreEmailCampaignRequest $request): RedirectResponse
    {
        if (! EmailSetting::query()->exists()) {
            return redirect()->route('email-settings.edit')->withErrors(['email' => 'Configure your SMTP details before sending a campaign.']);
        }

        $recipientCount = Contact::query()->where('is_on_hold', false)->count();
        if ($recipientCount === 0) {
            return back()->withErrors(['contacts' => 'Add at least one contact before sending a campaign.'])->withInput();
        }

        $campaign = EmailCampaign::create([
            ...$request->validated(),
            'status' => 'queued',
            'recipient_count' => $recipientCount,
        ]);

        Contact::query()
            ->where('is_on_hold', false)
            ->select(['id', 'name', 'level', 'company', 'email', 'phone'])
            ->chunkById(500, function ($contacts) use ($campaign): void {
                foreach ($contacts as $contact) {
                    SendCampaignEmail::dispatch(
                        $campaign->id,
                        $contact->name,
                        $contact->level,
                        $contact->company,
                        $contact->email,
                        $contact->phone ?? '',
                    );
                }
            });

        return redirect()->route('campaigns.index')->with('success', "Campaign queued for {$recipientCount} contacts.");
    }
}
