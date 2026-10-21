<?php

use App\Mail\CampaignMessage;
use App\Models\EmailCampaign;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

it('uploads previews and embeds images in personalized email bodies', function (): void {
    Storage::fake('local');
    $this->actingAs(User::factory()->create());

    $this->post(route('campaigns.store'), [
        'name' => 'Image campaign', 'subject' => 'Hello {{ name }}',
        'body' => "Welcome {{ name }}\n<script>alert(1)</script>",
        'images' => [UploadedFile::fake()->image('banner.png')],
    ])->assertSessionHasNoErrors()->assertRedirect();

    $campaign = EmailCampaign::query()->sole();
    expect($campaign->images)->toHaveCount(1);
    Storage::disk('local')->assertExists($campaign->images[0]);
    $this->get(route('campaigns.edit', $campaign))->assertOk()
        ->assertSee(route('campaigns.images.show', [$campaign, 0]));
    $this->get(route('campaigns.images.show', [$campaign, 0]))->assertOk()->assertHeader('Content-Type', 'image/png');
    $this->get(route('campaigns.images.show', [$campaign, 1]))->assertNotFound();

    $message = new CampaignMessage($campaign, '<b>Jane</b>', 'Gold', 'Acme', 'jane@example.com', '123');
    Mail::to('jane@example.com')->send($message);
    $email = Mail::getSymfonyTransport()->messages()->sole()->getOriginalMessage();
    expect($email->getHtmlBody())->toContain('cid:', '&lt;b&gt;Jane&lt;/b&gt;', '&lt;script&gt;')
        ->not->toContain('<script>', '<b>Jane</b>');
    expect($email->getTextBody())->toContain('Welcome');
    expect($email->getAttachments())->toHaveCount(1);
    expect($email->getAttachments()[0]->getDisposition())->toBe('inline');
});

it('keeps copied images available after removing them from the original draft', function (): void {
    Storage::fake('local');
    $this->actingAs(User::factory()->create());
    $path = UploadedFile::fake()->image('banner.jpg')->store('campaign-images', 'local');
    $campaign = EmailCampaign::create(['name' => 'Original', 'subject' => 'News', 'body' => 'Hello', 'images' => [$path]]);

    $this->post(route('campaigns.copy', $campaign), ['name' => 'Copy'])->assertRedirect();
    $copy = EmailCampaign::where('name', 'Copy')->sole();
    expect($copy->images)->toBe([$path]);

    $this->put(route('campaigns.update', $campaign), [
        'name' => 'Original', 'subject' => 'News', 'body' => 'Hello',
        'remove_images' => [0], 'images' => [UploadedFile::fake()->image('new.png')],
    ])->assertSessionHasNoErrors()->assertRedirect();
    expect($campaign->refresh()->images)->toHaveCount(1)->not->toContain($path);
    $this->get(route('campaigns.images.show', [$copy, 0]))->assertOk();
    Storage::disk('local')->assertExists($path);

    $this->put(route('campaigns.update', $campaign), ['name' => 'Original', 'subject' => 'News', 'body' => 'Hello'])
        ->assertSessionHasNoErrors()->assertRedirect();
    expect($campaign->refresh()->images)->toHaveCount(1);
});

it('rejects invalid campaign images without saving a campaign or files', function (string $kind): void {
    Storage::fake('local');
    $this->actingAs(User::factory()->create());
    $images = match ($kind) {
        'document' => [UploadedFile::fake()->create('document.pdf', 10, 'application/pdf')],
        'oversized' => [UploadedFile::fake()->image('large.png')->size(2049)],
        'too many' => array_map(fn (): UploadedFile => UploadedFile::fake()->image('image.png'), range(1, 6)),
        'path injection' => ['../../.env'],
    };
    $this->post(route('campaigns.store'), ['name' => 'Invalid', 'subject' => 'News', 'body' => 'Hello', 'images' => $images])
        ->assertSessionHasErrors($kind === 'too many' ? 'images' : 'images.0');
    expect(EmailCampaign::count())->toBe(0);
    expect(Storage::disk('local')->allFiles())->toBeEmpty();
})->with(['document', 'oversized', 'too many', 'path injection']);

it('requires authentication to preview campaign images', function (): void {
    $campaign = EmailCampaign::create(['name' => 'Private', 'subject' => 'News', 'body' => 'Hello']);
    $this->get(route('campaigns.images.show', [$campaign, 0]))->assertRedirect(route('login'));
});

it('does not upload images to a campaign after sending starts', function (): void {
    Storage::fake('local');
    $this->actingAs(User::factory()->create());
    $campaign = EmailCampaign::create(['name' => 'Locked', 'subject' => 'News', 'body' => 'Hello', 'status' => 'sending']);
    $this->put(route('campaigns.update', $campaign), [
        'name' => 'Locked', 'subject' => 'News', 'body' => 'Hello', 'images' => [UploadedFile::fake()->image('new.png')],
    ])->assertConflict();
    expect($campaign->refresh()->images)->toBeNull();
    expect(Storage::disk('local')->allFiles())->toBeEmpty();
});
