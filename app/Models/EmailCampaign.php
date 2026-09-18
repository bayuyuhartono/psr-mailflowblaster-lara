<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    protected $fillable = ['subject', 'body', 'status', 'recipient_count', 'sent_count', 'failed_count'];
}
