<?php

use App\Models\EmailSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('stores smtp configuration and encrypts the password', function (): void {
    $this->put(route('email-settings.update'), [
        'host' => 'smtp.example.com', 'port' => 587, 'encryption' => 'tls',
        'username' => 'mailer', 'password' => 'secret-value',
        'from_address' => 'hello@example.com', 'from_name' => 'Acme Team',
    ])->assertRedirect(route('email-settings.edit'))->assertSessionHas('success');

    $setting = EmailSetting::query()->sole();
    expect($setting->password)->toBe('secret-value');
    expect($setting->getRawOriginal('password'))->not->toContain('secret-value');
});

it('keeps the current password when the password field is blank', function (): void {
    $setting = EmailSetting::create([
        'host' => 'old.example.com', 'port' => 587, 'encryption' => 'tls',
        'username' => 'mailer', 'password' => 'existing-secret',
        'from_address' => 'hello@example.com', 'from_name' => 'Acme Team',
    ]);

    $this->put(route('email-settings.update'), [
        'host' => 'new.example.com', 'port' => 587, 'encryption' => 'tls',
        'username' => 'mailer', 'password' => '',
        'from_address' => 'hello@example.com', 'from_name' => 'Acme Team',
    ])->assertSessionHasNoErrors();

    expect($setting->refresh()->password)->toBe('existing-secret');
});
