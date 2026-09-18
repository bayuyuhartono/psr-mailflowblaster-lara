<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactTemplateController extends Controller
{
    public function __invoke(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, ['name', 'level', 'company', 'email', 'phone', 'on_hold']);
            fputcsv($stream, ['Jane Doe', 'Gold', 'Acme Ltd', 'jane@example.com', '+62 812 3456 7890', 'no']);
            fclose($stream);
        }, 'contact-import-template.csv', ['Content-Type' => 'text/csv']);
    }
}
