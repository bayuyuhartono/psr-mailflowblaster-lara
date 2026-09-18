<?php

use App\Jobs\SendCampaignEmail;
use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailSetting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(LazilyRefreshDatabase::class);

it('queues one personalized email job for every contact', function (): void {
    EmailSetting::create([
        'host' => 'smtp.example.com', 'port' => 587, 'encryption' => 'tls',
        'username' => 'mailer', 'password' => 'secret',
        'from_address' => 'hello@example.com', 'from_name' => 'Acme Team',
    ]);
    $contacts = Contact::factory()->count(3)->create();
    Queue::fake([SendCampaignEmail::class]);

    $this->post(route('campaigns.store'), ['subject' => 'Big news', 'body' => 'Hello from Acme.'])
        ->assertRedirect(route('campaigns.index'))
        ->assertSessionHas('success');

    $campaign = EmailCampaign::query()->sole();
    expect($campaign->recipient_count)->toBe(3);
    Queue::assertPushed(SendCampaignEmail::class, 3);
    Queue::assertPushed(fn (SendCampaignEmail $job): bool => $job->campaignId === $campaign->id && $job->contactEmail === $contacts->first()->email);
});

it('does not create a campaign until smtp is configured', function (): void {
    Contact::factory()->create();
    Queue::fake([SendCampaignEmail::class]);

    $this->post(route('campaigns.store'), ['subject' => 'Big news', 'body' => 'Hello'])
        ->assertRedirect(route('email-settings.edit'))
        ->assertSessionHasErrors('email');

    $this->assertDatabaseCount('email_campaigns', 0);
    Queue::assertNothingPushed();
});
