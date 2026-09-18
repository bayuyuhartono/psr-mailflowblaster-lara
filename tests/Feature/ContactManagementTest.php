<?php

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('creates a contact with all customer fields', function (): void {
    $response = $this->post(route('contacts.store'), [
        'name' => 'Jane Doe', 'level' => 'Gold', 'company' => 'Acme Ltd',
        'email' => 'jane@example.com', 'phone' => '+62 812 3456', 'is_on_hold' => '1',
    ]);

    $response->assertRedirect(route('contacts.index'))->assertSessionHas('success');
    $this->assertDatabaseHas('contacts', ['email' => 'jane@example.com', 'level' => 'Gold', 'is_on_hold' => true]);
});

it('rejects a duplicate contact email', function (): void {
    Contact::factory()->create(['email' => 'jane@example.com']);

    $response = $this->from(route('contacts.create'))->post(route('contacts.store'), [
        'name' => 'Another Jane', 'level' => 'Silver', 'company' => 'Other Ltd',
        'email' => 'jane@example.com', 'phone' => '',
    ]);

    $response->assertRedirect(route('contacts.create'))->assertSessionHasErrors('email');
    expect(Contact::query()->count())->toBe(1);
});

it('shows active and held audience totals on the contact dashboard', function (): void {
    Contact::factory()->count(2)->create(['is_on_hold' => false]);
    Contact::factory()->create(['is_on_hold' => true]);

    $this->get(route('contacts.index'))
        ->assertOk()
        ->assertViewHas('totalContactCount', 3)
        ->assertViewHas('activeContactCount', 2)
        ->assertViewHas('heldContactCount', 1)
        ->assertSee('Contact directory');
});

it('updates and deletes a contact', function (): void {
    $contact = Contact::factory()->create();

    $this->put(route('contacts.update', $contact), [
        'name' => 'Updated Name', 'level' => 'Platinum', 'company' => 'Updated Co',
        'email' => 'updated@example.com', 'phone' => '12345', 'is_on_hold' => '1',
    ])->assertRedirect(route('contacts.index'));

    expect($contact->refresh()->name)->toBe('Updated Name')
        ->and($contact->is_on_hold)->toBeTrue();

    $this->delete(route('contacts.destroy', $contact))->assertRedirect(route('contacts.index'));
    $this->assertModelMissing($contact);
});

it('holds and releases a contact from the contact list', function (): void {
    $contact = Contact::factory()->create(['name' => 'Jane Doe', 'is_on_hold' => false]);

    $this->from(route('contacts.index'))->post(route('contact-holds.store', $contact))
        ->assertRedirect(route('contacts.index'))
        ->assertSessionHas('success');
    expect($contact->refresh()->is_on_hold)->toBeTrue();

    $this->get(route('contacts.index'))
        ->assertSee('On hold')
        ->assertSee('Release');

    $this->from(route('contacts.index'))->delete(route('contact-holds.destroy', $contact))
        ->assertRedirect(route('contacts.index'))
        ->assertSessionHas('success');
    expect($contact->refresh()->is_on_hold)->toBeFalse();
});
