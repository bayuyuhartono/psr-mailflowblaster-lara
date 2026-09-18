<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmailSettingRequest;
use App\Models\EmailSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmailSettingController extends Controller
{
    public function edit(): View
    {
        return view('email-settings.edit', ['setting' => EmailSetting::query()->first()]);
    }

    public function update(UpdateEmailSettingRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('password');
        $setting = EmailSetting::query()->firstOrNew();
        $setting->fill($data);

        if ($request->filled('password')) {
            $setting->password = $request->string('password')->toString();
        }

        $setting->save();

        return redirect()->route('email-settings.edit')->with('success', 'Email configuration saved successfully.');
    }
}
