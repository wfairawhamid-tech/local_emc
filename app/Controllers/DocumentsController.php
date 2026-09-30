<?php

namespace App\Controllers;

/**
 * DocumentsController
 * Serves the Medical Documents hub page — a landing page that lets the
 * doctor choose between Medical Certificate (MC) or Time Slip.
 * All actual document creation/listing/editing routes remain in
 * McController and TimeSlipController — this controller only shows
 * the selection page.
 */
class DocumentsController extends BaseController
{
    public function index(): string
    {
        $data = [
            'title' => 'Medical Documents',
        ];

        return view('layouts/main', array_merge($data, ['content_view' => 'documents/index']));
    }
}
