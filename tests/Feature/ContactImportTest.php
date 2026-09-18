<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('imports contacts from the csv template and updates matching emails', function (): void {
    $csv = "name,level,company,email,phone,on_hold\nJane Doe,Gold,Acme Ltd,jane@example.com,12345,yes\n";
    $file = UploadedFile::fake()->createWithContent('contacts.csv', $csv);

    $this->post(route('contacts.import'), ['file' => $file])
        ->assertRedirect(route('contacts.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('contacts', ['email' => 'jane@example.com', 'company' => 'Acme Ltd', 'is_on_hold' => true]);

    $updated = UploadedFile::fake()->createWithContent('contacts.csv', str_replace('Acme Ltd', 'New Company', $csv));
    $this->post(route('contacts.import'), ['file' => $updated])->assertSessionHasNoErrors();
    $this->assertDatabaseCount('contacts', 1);
    $this->assertDatabaseHas('contacts', ['email' => 'jane@example.com', 'company' => 'New Company']);
});

it('rejects a csv with an invalid header', function (): void {
    $file = UploadedFile::fake()->createWithContent('contacts.csv', "email,name\njane@example.com,Jane\n");

    $this->from(route('contacts.index'))->post(route('contacts.import'), ['file' => $file])
        ->assertRedirect(route('contacts.index'))
        ->assertSessionHasErrors('file');

    $this->assertDatabaseCount('contacts', 0);
});

it('downloads the contact import template', function (): void {
    $this->get(route('contacts.import-template'))
        ->assertOk()
        ->assertDownload('contact-import-template.csv');
});
