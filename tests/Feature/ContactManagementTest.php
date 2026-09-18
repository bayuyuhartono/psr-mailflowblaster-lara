<?php

use App\Models\Contact;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('creates a contact with all customer fields', function (): void {
    $response = $this->post(route('contacts.store'), [
        'name' => 'Jane Doe', 'level' => 'Gold', 'company' => 'Acme Ltd',
        'email' => 'jane@example.com', 'phone' => '+62 812 3456',
    ]);

    $response->assertRedirect(route('contacts.index'))->assertSessionHas('success');
    $this->assertDatabaseHas('contacts', ['email' => 'jane@example.com', 'level' => 'Gold']);
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

it('updates and deletes a contact', function (): void {
    $contact = Contact::factory()->create();

    $this->put(route('contacts.update', $contact), [
        'name' => 'Updated Name', 'level' => 'Platinum', 'company' => 'Updated Co',
        'email' => 'updated@example.com', 'phone' => '12345',
    ])->assertRedirect(route('contacts.index'));

    expect($contact->refresh()->name)->toBe('Updated Name');

    $this->delete(route('contacts.destroy', $contact))->assertRedirect(route('contacts.index'));
    $this->assertModelMissing($contact);
});
