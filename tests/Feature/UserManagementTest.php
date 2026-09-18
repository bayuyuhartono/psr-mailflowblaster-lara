<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('allows a super user to create another account', function (): void {
    $superUser = User::factory()->superUser()->create();

    $this->actingAs($superUser)->post(route('users.store'), [
        'name' => 'Team Member',
        'email' => 'member@example.com',
        'password' => 'secure-password',
        'password_confirmation' => 'secure-password',
        'is_super_user' => '0',
    ])->assertRedirect(route('users.index'))->assertSessionHas('success');

    $this->assertDatabaseHas('users', ['email' => 'member@example.com', 'is_super_user' => false]);
});

it('allows a super user to create another super user', function (): void {
    $superUser = User::factory()->superUser()->create();

    $this->actingAs($superUser)->post(route('users.store'), [
        'name' => 'Second Owner',
        'email' => 'owner2@example.com',
        'password' => 'secure-password',
        'password_confirmation' => 'secure-password',
        'is_super_user' => '1',
    ])->assertSessionHasNoErrors();

    expect(User::query()->where('email', 'owner2@example.com')->sole()->is_super_user)->toBeTrue();
});

it('forbids ordinary users from user management', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('users.index'))->assertForbidden();
    $this->actingAs($user)->post(route('users.store'), [
        'name' => 'Forbidden',
        'email' => 'forbidden@example.com',
        'password' => 'secure-password',
        'password_confirmation' => 'secure-password',
    ])->assertForbidden();

    $this->assertDatabaseMissing('users', ['email' => 'forbidden@example.com']);
});

it('prevents a super user from deleting their own account', function (): void {
    $superUser = User::factory()->superUser()->create();

    $this->actingAs($superUser)->delete(route('users.destroy', $superUser))
        ->assertSessionHasErrors('user');

    $this->assertModelExists($superUser);
});
