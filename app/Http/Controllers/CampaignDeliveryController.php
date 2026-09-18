<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendNextCampaignEmailRequest;
use App\Jobs\SendCampaignEmail;
use App\Models\CampaignDelivery;
use App\Models\Contact;
use App\Models\EmailCampaign;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CampaignDeliveryController extends Controller
{
    public function store(SendNextCampaignEmailRequest $request, EmailCampaign $campaign): JsonResponse
    {
        abort_unless($campaign->status === 'sending', Response::HTTP_CONFLICT, 'Start or retry this campaign before sending emails.');

        $lock = Cache::lock("campaign-send-next:{$campaign->id}", 120);

        if (! $lock->get()) {
            return response()->json([
                'message' => 'Another email is currently being sent. Please wait a moment.',
            ], Response::HTTP_CONFLICT);
        }

        try {
            return $this->sendNext($request, $campaign);
        } finally {
            $lock->release();
        }
    }

    private function sendNext(SendNextCampaignEmailRequest $request, EmailCampaign $campaign): JsonResponse
    {
        $runToken = $request->string('run_token')->toString();
        $contact = $this->remainingContacts($campaign, $runToken)->first();

        if ($contact === null) {
            return $this->finishedResponse($campaign);
        }

        $campaign->update(['status' => 'sending']);

        try {
            SendCampaignEmail::dispatchSync(
                $campaign->id,
                $contact->id,
                $request->user()->id,
                $runToken,
                $contact->name,
                $contact->level,
                $contact->company,
                $contact->email,
                $contact->phone ?? '',
            );
        } catch (Throwable $exception) {
            CampaignDelivery::query()->updateOrCreate(
                ['email_campaign_id' => $campaign->id, 'contact_email' => $contact->email],
                [
                    'contact_id' => $contact->id,
                    'sent_by' => $request->user()->id,
                    'contact_name' => $contact->name,
                    'contact_company' => $contact->company,
                    'status' => 'failed',
                    'attempt_token' => $runToken,
                    'error_message' => mb_substr($exception->getMessage(), 0, 2000),
                    'attempted_at' => now(),
                    'sent_at' => null,
                ],
            );
        }

        if ($this->remainingContacts($campaign, $runToken)->doesntExist()) {
            return $this->finishedResponse($campaign);
        }

        $counts = $this->updateCampaignCounts($campaign, 'sending');

        return response()->json([
            'status' => 'sending',
            'recipient' => $contact->email,
            'sent' => $counts['sent'],
            'failed' => $counts['failed'],
            'remaining' => $this->remainingContacts($campaign, $runToken)->count(),
            'has_more' => true,
        ]);
    }

    private function remainingContacts(EmailCampaign $campaign, string $runToken): Builder
    {
        $successfulEmails = CampaignDelivery::query()
            ->select('contact_email')
            ->where('email_campaign_id', $campaign->id)
            ->where('status', 'successful');

        $attemptedThisRun = CampaignDelivery::query()
            ->select('contact_email')
            ->where('email_campaign_id', $campaign->id)
            ->where('attempt_token', $runToken);

        return Contact::query()
            ->where('is_on_hold', false)
            ->whereNotIn('email', $successfulEmails)
            ->whereNotIn('email', $attemptedThisRun)
            ->orderBy('id');
    }

    private function finishedResponse(EmailCampaign $campaign): JsonResponse
    {
        $failedCount = $campaign->deliveries()->where('status', 'failed')->count();
        $counts = $this->updateCampaignCounts($campaign, $failedCount > 0 ? 'completed_with_errors' : 'completed');

        return response()->json([
            'status' => 'finished',
            'sent' => $counts['sent'],
            'failed' => $counts['failed'],
            'remaining' => 0,
            'has_more' => false,
        ]);
    }

    /**
     * @return array{sent: int, failed: int}
     */
    private function updateCampaignCounts(EmailCampaign $campaign, string $status): array
    {
        $successfulCount = $campaign->deliveries()->where('status', 'successful')->count();
        $failedCount = $campaign->deliveries()->where('status', 'failed')->count();

        $campaign->update([
            'status' => $status,
            'recipient_count' => $successfulCount + $failedCount,
            'sent_count' => $successfulCount,
            'failed_count' => $failedCount,
        ]);

        return ['sent' => $successfulCount, 'failed' => $failedCount];
    }
}
