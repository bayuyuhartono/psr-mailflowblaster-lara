<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(LazilyRefreshDatabase::class);

it('imports contacts from the csv template and updates matching emails', function (): void {
    $csv = "name,level,company,email,phone\nJane Doe,Gold,Acme Ltd,jane@example.com,12345\n";
    $file = UploadedFile::fake()->createWithContent('contacts.csv', $csv);

    $this->post(route('contacts.import'), ['file' => $file])
        ->assertRedirect(route('contacts.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('contacts', ['email' => 'jane@example.com', 'company' => 'Acme Ltd']);

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
