<?php

namespace App\Models;

use Database\Factories\CampaignDeliveryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignDelivery extends Model
{
    /** @use HasFactory<CampaignDeliveryFactory> */
    use HasFactory;

    protected $fillable = [
        'email_campaign_id', 'contact_id', 'sent_by', 'contact_name', 'contact_company', 'contact_email',
        'status', 'attempt_token', 'error_message', 'attempted_at', 'sent_at',
    ];

    protected function casts(): array
    {
        return ['attempted_at' => 'datetime', 'sent_at' => 'datetime'];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class, 'email_campaign_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
