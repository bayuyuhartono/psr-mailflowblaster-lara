<?php

namespace App\Http\Controllers;

use App\Http\Requests\CopyEmailCampaignRequest;
use App\Models\EmailCampaign;
use Illuminate\Http\RedirectResponse;

class CampaignCopyController extends Controller
{
    public function store(CopyEmailCampaignRequest $request, EmailCampaign $campaign): RedirectResponse
    {
        $copy = EmailCampaign::create([
            'name' => $request->validated('name'),
            'subject' => $campaign->subject,
            'body' => $campaign->body,
            'status' => 'draft',
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('campaigns.edit', $copy)->with('success', 'Campaign copied into a new draft.');
    }
}
