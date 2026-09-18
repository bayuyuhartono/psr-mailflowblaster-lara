<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\RedirectResponse;

class ContactHoldController extends Controller
{
    public function store(Contact $contact): RedirectResponse
    {
        $contact->update(['is_on_hold' => true]);

        return back()->with('success', "{$contact->name} is now on hold and will be skipped by campaigns.");
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->update(['is_on_hold' => false]);

        return back()->with('success', "{$contact->name} is active and can receive campaigns again.");
    }
}
