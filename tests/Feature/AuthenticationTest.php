<?php

use App\Models\User;
use Database\Seeders\SuperUserSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('seeds the configured super user account', function (): void {
    $this->seed(SuperUserSeeder::class);

    $user = User::query()->where('email', 'super@gmail.com')->sole();
    expect($user->is_super_user)->toBeTrue();
    $this->assertCredentials(['email' => 'super@gmail.com', 'password' => 'adminpass']);
});

it('redirects guests to sign in', function (): void {
    $this->get(route('campaigns.index'))->assertRedirect(route('login'));
});

it('signs in with valid credentials and regenerates the session', function (): void {
    $user = User::factory()->create(['password' => 'secure-password']);
    $oldSessionId = session()->getId();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'secure-password',
    ])->assertRedirect(route('campaigns.index'));

    $this->assertAuthenticatedAs($user);
    expect(session()->getId())->not->toBe($oldSessionId);
});

it('rejects invalid credentials', function (): void {
    $user = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('signs out and invalidates the authenticated session', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('logout'))->assertRedirect(route('login'));

    $this->assertGuest();
});
