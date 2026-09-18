<?php

use App\Jobs\SendCampaignEmail;
use App\Mail\CampaignMessage;
use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('renders the campaign composer with personalization placeholders', function (): void {
    $this->get(route('campaigns.index'))
        ->assertOk()
        ->assertSeeText('{{ name }}')
        ->assertSeeText('{{ level }}')
        ->assertSeeText('{{ company }}')
        ->assertSeeText('{{ email }}')
        ->assertSeeText('{{ phone }}');
});

it('queues one personalized email job for every contact', function (): void {
    EmailSetting::create([
        'host' => 'smtp.example.com', 'port' => 587, 'encryption' => 'tls',
        'username' => 'mailer', 'password' => 'secret',
        'from_address' => 'hello@example.com', 'from_name' => 'Acme Team',
    ]);
    $contacts = Contact::factory()->count(3)->create();
    $heldContact = Contact::factory()->create(['is_on_hold' => true]);
    Queue::fake([SendCampaignEmail::class]);

    $this->post(route('campaigns.store'), ['subject' => 'Big news', 'body' => 'Hello from Acme.'])
        ->assertRedirect(route('campaigns.index'))
        ->assertSessionHas('success');

    $campaign = EmailCampaign::query()->sole();
    expect($campaign->recipient_count)->toBe(3);
    Queue::assertPushed(SendCampaignEmail::class, 3);
    Queue::assertPushed(fn (SendCampaignEmail $job): bool => $job->campaignId === $campaign->id
        && $job->contactEmail === $contacts->first()->email
        && $job->contactLevel === $contacts->first()->level
        && $job->contactCompany === $contacts->first()->company);
    Queue::assertNotPushed(fn (SendCampaignEmail $job): bool => $job->contactEmail === $heldContact->email);
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

it('personalizes the email body with the contact name and company', function (): void {
    $campaign = EmailCampaign::create([
        'subject' => 'Welcome {{ name }}',
        'body' => '{{ name }} | {{ level }} | {{ company }} | {{ email }} | {{ phone }}',
        'recipient_count' => 1,
    ]);

    $message = new CampaignMessage($campaign, 'Jane Doe', 'Gold', 'Acme Ltd', 'jane@example.com', '12345');

    expect($message->personalizedBody())->toBe('Jane Doe | Gold | Acme Ltd | jane@example.com | 12345');
    expect($message->personalizedSubject())->toBe('Welcome Jane Doe');
});
