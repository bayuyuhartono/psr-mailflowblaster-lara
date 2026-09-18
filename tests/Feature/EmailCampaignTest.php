<?php

use App\Jobs\SendCampaignEmail;
use App\Mail\CampaignMessage;
use App\Models\CampaignDelivery;
use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('renders the campaign composer with personalization placeholders', function (): void {
    $campaign = EmailCampaign::create(['name' => 'Draft campaign', 'subject' => 'Draft', 'body' => 'Message', 'status' => 'draft']);

    $this->get(route('campaigns.edit', $campaign))
        ->assertOk()
        ->assertSeeText('{{ name }}')
        ->assertSeeText('{{ level }}')
        ->assertSeeText('{{ company }}')
        ->assertSeeText('{{ email }}')
        ->assertSeeText('{{ phone }}');
});

it('renders the create screen with audience and personalization guidance', function (): void {
    Contact::factory()->count(2)->create();
    Contact::factory()->create(['is_on_hold' => true]);

    $this->get(route('campaigns.create'))
        ->assertOk()
        ->assertSee('Active audience')
        ->assertSee('2')
        ->assertSee('1')
        ->assertSeeText('{{ name }}')
        ->assertSee('What happens next');
});

it('saves a new campaign as an editable draft', function (): void {
    Queue::fake([SendCampaignEmail::class]);

    $response = $this->post(route('campaigns.store'), ['name' => 'Big news campaign', 'subject' => 'Big news', 'body' => 'Hello from Acme.']);

    $campaign = EmailCampaign::query()->sole();
    $response->assertRedirect(route('campaigns.edit', $campaign))->assertSessionHas('success');
    expect($campaign->status)->toBe('draft');
    expect($campaign->created_by)->toBe(auth()->id());
    Queue::assertNothingPushed();

    $this->put(route('campaigns.update', $campaign), ['name' => 'Updated campaign', 'subject' => 'Updated news', 'body' => 'Updated body'])
        ->assertRedirect(route('campaigns.edit', $campaign));
    expect($campaign->refresh()->subject)->toBe('Updated news');
});

it('sends one server-selected active contact per browser request', function (): void {
    EmailSetting::create([
        'host' => 'smtp.example.com', 'port' => 587, 'encryption' => 'tls',
        'username' => 'mailer', 'password' => 'secret',
        'from_address' => 'hello@example.com', 'from_name' => 'Acme Team',
    ]);
    Contact::factory()->count(3)->create();
    $heldContact = Contact::factory()->create(['is_on_hold' => true]);
    Mail::fake();

    $campaign = EmailCampaign::create(['name' => 'Blast campaign', 'subject' => 'Big news', 'body' => 'Hello from Acme.', 'status' => 'draft']);

    $this->post(route('campaigns.blast', $campaign))
        ->assertRedirect(route('campaigns.show', $campaign))
        ->assertSessionHas('success');

    expect($campaign->refresh()->status)->toBe('sending')
        ->and($campaign->deliveries()->count())->toBe(0);

    $runToken = '11111111-1111-4111-8111-111111111111';
    $this->postJson(route('campaigns.send-next', $campaign), [
        'run_token' => $runToken,
        'email' => 'attacker@example.com',
    ])->assertOk()->assertJson(['status' => 'sending', 'has_more' => true, 'remaining' => 2]);
    $this->postJson(route('campaigns.send-next', $campaign), ['run_token' => $runToken])
        ->assertOk()->assertJson(['status' => 'sending', 'has_more' => true, 'remaining' => 1]);
    $this->postJson(route('campaigns.send-next', $campaign), ['run_token' => $runToken])
        ->assertOk()->assertJson(['status' => 'finished', 'has_more' => false, 'remaining' => 0]);

    expect($campaign->refresh()->recipient_count)->toBe(3)
        ->and($campaign->status)->toBe('completed')
        ->and($campaign->deliveries()->where('status', 'successful')->count())->toBe(3)
        ->and($campaign->deliveries()->where('attempt_token', $runToken)->count())->toBe(3)
        ->and($campaign->deliveries()->where('sent_by', auth()->id())->count())->toBe(3)
        ->and($campaign->deliveries()->where('contact_email', 'attacker@example.com')->exists())->toBeFalse()
        ->and($campaign->deliveries()->where('contact_email', $heldContact->email)->exists())->toBeFalse();
    Mail::assertSent(CampaignMessage::class, 3);
});

it('copies campaign content into a differently named draft', function (): void {
    $campaign = EmailCampaign::create(['name' => 'Original campaign', 'subject' => 'Original subject', 'body' => 'Original body', 'status' => 'completed']);

    $response = $this->post(route('campaigns.copy', $campaign), ['name' => 'Copied campaign']);

    $copy = EmailCampaign::query()->where('name', 'Copied campaign')->sole();
    $response->assertRedirect(route('campaigns.edit', $copy));
    expect($copy->subject)->toBe('Original subject')
        ->and($copy->body)->toBe('Original body')
        ->and($copy->status)->toBe('draft')
        ->and($copy->created_by)->toBe(auth()->id());
});

it('retries only contacts without a successful delivery', function (): void {
    EmailSetting::create([
        'host' => 'smtp.example.com', 'port' => 587, 'encryption' => 'tls',
        'username' => 'mailer', 'password' => 'secret',
        'from_address' => 'hello@example.com', 'from_name' => 'Acme Team',
    ]);
    $successfulContact = Contact::factory()->create();
    $failedContact = Contact::factory()->create();
    $campaign = EmailCampaign::create(['name' => 'Retry campaign', 'subject' => 'Retry', 'body' => 'Hello', 'status' => 'completed_with_errors']);
    CampaignDelivery::create([
        'email_campaign_id' => $campaign->id,
        'contact_id' => $successfulContact->id,
        'sent_by' => auth()->id(),
        'contact_name' => $successfulContact->name,
        'contact_company' => $successfulContact->company,
        'contact_email' => $successfulContact->email,
        'status' => 'successful',
        'attempted_at' => now(),
        'sent_at' => now(),
    ]);
    CampaignDelivery::create([
        'email_campaign_id' => $campaign->id,
        'contact_id' => $failedContact->id,
        'sent_by' => auth()->id(),
        'contact_name' => $failedContact->name,
        'contact_company' => $failedContact->company,
        'contact_email' => $failedContact->email,
        'status' => 'failed',
        'error_message' => 'Previous failure',
        'attempted_at' => now(),
    ]);
    Mail::fake();

    $this->post(route('campaigns.blast', $campaign))->assertRedirect(route('campaigns.show', $campaign));
    $this->postJson(route('campaigns.send-next', $campaign), [
        'run_token' => '22222222-2222-4222-8222-222222222222',
    ])->assertOk()->assertJson(['status' => 'finished']);

    Mail::assertSent(CampaignMessage::class, 1);
    expect($campaign->refresh()->status)->toBe('completed')
        ->and($campaign->sent_count)->toBe(2)
        ->and($campaign->failed_count)->toBe(0)
        ->and($campaign->deliveries()->where('contact_email', $successfulContact->email)->count())->toBe(1);
});

it('paginates the campaign delivery ledger', function (): void {
    $campaign = EmailCampaign::create(['name' => 'Report campaign', 'subject' => 'Report', 'body' => 'Body', 'status' => 'completed']);

    foreach (range(1, 16) as $number) {
        CampaignDelivery::create([
            'email_campaign_id' => $campaign->id,
            'sent_by' => auth()->id(),
            'contact_name' => "Contact {$number}",
            'contact_company' => 'Acme',
            'contact_email' => "contact{$number}@example.com",
            'status' => 'successful',
            'attempted_at' => now(),
            'sent_at' => now(),
        ]);
    }

    $this->get(route('campaigns.show', $campaign))
        ->assertOk()
        ->assertViewHas('deliveries', fn ($deliveries): bool => $deliveries->count() === 15 && $deliveries->total() === 16);
});

it('does not blast a campaign until smtp is configured', function (): void {
    Contact::factory()->create();
    $campaign = EmailCampaign::create(['name' => 'SMTP campaign', 'subject' => 'Big news', 'body' => 'Hello', 'status' => 'draft']);
    Queue::fake([SendCampaignEmail::class]);

    $this->post(route('campaigns.blast', $campaign))
        ->assertRedirect(route('email-settings.edit'))
        ->assertSessionHasErrors('email');

    expect($campaign->refresh()->status)->toBe('draft');
    Queue::assertNothingPushed();
});

it('locks a campaign against editing and deletion after sending starts', function (): void {
    $campaign = EmailCampaign::create(['name' => 'Sent campaign', 'subject' => 'Sent news', 'body' => 'Sent body', 'status' => 'sending']);

    $this->get(route('campaigns.edit', $campaign))->assertStatus(409);
    $this->put(route('campaigns.update', $campaign), ['name' => 'Changed campaign', 'subject' => 'Changed', 'body' => 'Changed'])->assertStatus(409);
    $this->delete(route('campaigns.destroy', $campaign))->assertStatus(409);
    $this->assertModelExists($campaign);
});

it('resumes a campaign left in sending status and sends only unsent contacts', function (): void {
    EmailSetting::create([
        'host' => 'smtp.example.com', 'port' => 587, 'encryption' => 'tls',
        'username' => 'mailer', 'password' => 'secret',
        'from_address' => 'hello@example.com', 'from_name' => 'Acme Team',
    ]);
    Contact::factory()->create();
    $campaign = EmailCampaign::create(['name' => 'Interrupted campaign', 'subject' => 'Resume', 'body' => 'Hello', 'status' => 'sending']);
    Mail::fake();

    $this->post(route('campaigns.blast', $campaign))
        ->assertRedirect(route('campaigns.show', $campaign))
        ->assertSessionHas('success');

    $this->postJson(route('campaigns.send-next', $campaign), [
        'run_token' => '33333333-3333-4333-8333-333333333333',
    ])->assertOk()->assertJson(['status' => 'finished']);

    expect($campaign->refresh()->status)->toBe('completed')
        ->and($campaign->sent_count)->toBe(1);
    Mail::assertSent(CampaignMessage::class, 1);
});

it('requires a valid browser run token before sending', function (): void {
    $campaign = EmailCampaign::create(['name' => 'Token campaign', 'subject' => 'Token', 'body' => 'Hello', 'status' => 'sending']);

    $this->postJson(route('campaigns.send-next', $campaign), ['run_token' => 'not-a-uuid'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('run_token');
});

it('does not send through the per-recipient endpoint before a campaign is started', function (): void {
    $campaign = EmailCampaign::create(['name' => 'Draft campaign', 'subject' => 'Draft', 'body' => 'Hello', 'status' => 'draft']);

    $this->postJson(route('campaigns.send-next', $campaign), [
        'run_token' => '44444444-4444-4444-8444-444444444444',
    ])->assertConflict();
});

it('shows the blast button only after opening a draft', function (): void {
    $campaign = EmailCampaign::create(['name' => 'Button campaign', 'subject' => 'Draft news', 'body' => 'Draft body', 'status' => 'draft']);

    $this->get(route('campaigns.index'))
        ->assertOk()
        ->assertSee('Open draft')
        ->assertDontSee('Blast email now');

    $this->get(route('campaigns.edit', $campaign))
        ->assertOk()
        ->assertSee('Blast email now');
});

it('deletes a campaign while it is still a draft', function (): void {
    $campaign = EmailCampaign::create(['name' => 'Delete campaign', 'subject' => 'Draft news', 'body' => 'Draft body', 'status' => 'draft']);

    $this->delete(route('campaigns.destroy', $campaign))
        ->assertRedirect(route('campaigns.index'));

    $this->assertModelMissing($campaign);
});

it('personalizes the email body with the contact name and company', function (): void {
    $campaign = EmailCampaign::create([
        'name' => 'Personalized campaign',
        'subject' => 'Welcome {{ name }}',
        'body' => '{{ name }} | {{ level }} | {{ company }} | {{ email }} | {{ phone }}',
        'recipient_count' => 1,
    ]);

    $message = new CampaignMessage($campaign, 'Jane Doe', 'Gold', 'Acme Ltd', 'jane@example.com', '12345');

    expect($message->personalizedBody())->toBe('Jane Doe | Gold | Acme Ltd | jane@example.com | 12345');
    expect($message->personalizedSubject())->toBe('Welcome Jane Doe');
    expect($message->render())
        ->toContain('Jane Doe | Gold | Acme Ltd | jane@example.com | 12345')
        ->not->toContain('<html')
        ->not->toContain('Hello Jane Doe')
        ->not->toContain('{{ name }}');
});
