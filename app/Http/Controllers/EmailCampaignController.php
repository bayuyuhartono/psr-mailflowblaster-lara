<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailCampaignRequest;
use App\Http\Requests\UpdateEmailCampaignRequest;
use App\Models\CampaignDelivery;
use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class EmailCampaignController extends Controller
{
    public function index(): View
    {
        return view('campaigns.index', [
            'campaigns' => EmailCampaign::query()->with('creator')->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('campaigns.create', [
            'contactCount' => Contact::query()->where('is_on_hold', false)->count(),
            'heldContactCount' => Contact::query()->where('is_on_hold', true)->count(),
            'isConfigured' => EmailSetting::query()->exists(),
        ]);
    }

    public function store(StoreEmailCampaignRequest $request): RedirectResponse
    {
        $campaign = EmailCampaign::create([
            ...$request->validated(),
            'status' => 'draft',
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('campaigns.edit', $campaign)->with('success', 'Campaign draft created. Review it before blasting.');
    }

    public function edit(EmailCampaign $campaign): View
    {
        abort_unless($campaign->status === 'draft', Response::HTTP_CONFLICT, 'Sent campaigns cannot be edited.');

        return view('campaigns.edit', [
            'campaign' => $campaign,
            'contactCount' => Contact::query()->where('is_on_hold', false)->count(),
            'heldContactCount' => Contact::query()->where('is_on_hold', true)->count(),
            'isConfigured' => EmailSetting::query()->exists(),
        ]);
    }

    public function show(EmailCampaign $campaign): View
    {
        $successfulEmails = CampaignDelivery::query()
            ->select('contact_email')
            ->where('email_campaign_id', $campaign->id)
            ->where('status', 'successful');

        return view('campaigns.show', [
            'campaign' => $campaign->load('creator'),
            'deliveries' => $campaign->deliveries()->with('sender')->latest('attempted_at')->paginate(15),
            'remainingCount' => Contact::query()
                ->where('is_on_hold', false)
                ->whereNotIn('email', $successfulEmails)
                ->count(),
            'isConfigured' => EmailSetting::query()->exists(),
        ]);
    }

    public function update(UpdateEmailCampaignRequest $request, EmailCampaign $campaign): RedirectResponse
    {
        abort_unless($campaign->status === 'draft', Response::HTTP_CONFLICT, 'Sent campaigns cannot be edited.');

        $campaign->update($request->validated());

        return redirect()->route('campaigns.edit', $campaign)->with('success', 'Campaign draft updated successfully.');
    }

    public function destroy(EmailCampaign $campaign): RedirectResponse
    {
        abort_unless($campaign->status === 'draft', Response::HTTP_CONFLICT, 'Sent campaigns cannot be deleted.');

        $campaign->delete();

        return redirect()->route('campaigns.index')->with('success', 'Campaign draft deleted successfully.');
    }
}
