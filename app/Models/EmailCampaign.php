<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailCampaign extends Model
{
    protected $attributes = ['status' => 'draft'];

    protected $fillable = ['name', 'subject', 'body', 'images', 'status', 'recipient_count', 'sent_count', 'failed_count', 'created_by'];

    protected function casts(): array
    {
        return ['images' => 'array'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(CampaignDelivery::class);
    }
}
