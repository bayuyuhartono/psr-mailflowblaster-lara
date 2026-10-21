<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaign;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CampaignImageController extends Controller
{
    public function __invoke(EmailCampaign $campaign, int $image): StreamedResponse
    {
        $path = $campaign->images[$image] ?? null;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, headers: ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store']);
    }
}
