<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportContactsRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ContactImportController extends Controller
{
    public function create(): View
    {
        return view('contacts.import');
    }

    public function store(ImportContactsRequest $request): RedirectResponse
    {
        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $expectedHeaders = ['name', 'level', 'company', 'email', 'phone', 'on_hold'];
        $headers = $handle === false ? false : fgetcsv($handle);

        if ($handle === false || $headers !== $expectedHeaders) {
            if (is_resource($handle)) {
                fclose($handle);
            }

            throw ValidationException::withMessages(['file' => 'The CSV header must be: '.implode(', ', $expectedHeaders).'.']);
        }

        $rows = [];
        $line = 1;

        while (($values = fgetcsv($handle)) !== false) {
            $line++;
            if (count($values) !== count($expectedHeaders)) {
                fclose($handle);
                throw ValidationException::withMessages(['file' => "Row {$line} must contain exactly six columns."]);
            }

            $row = array_combine($expectedHeaders, $values);
            Validator::make($row, [
                'name' => ['required', 'string', 'max:255'],
                'level' => ['required', 'string', 'max:100'],
                'company' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:50'],
                'on_hold' => ['required', 'in:yes,no'],
            ])->validate();
            $row['is_on_hold'] = $row['on_hold'] === 'yes';
            unset($row['on_hold']);
            $rows[] = $row;
        }

        fclose($handle);

        DB::transaction(function () use ($rows): void {
            foreach ($rows as $row) {
                Contact::updateOrCreate(['email' => $row['email']], $row);
            }
        });

        return redirect()->route('contacts.index')->with('success', count($rows).' contacts imported successfully.');
    }
}
