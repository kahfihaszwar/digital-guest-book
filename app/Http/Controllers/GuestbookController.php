<?php

namespace App\Http\Controllers;

use App\Models\GuestbookEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestbookController extends Controller
{
    public function index(): View
    {
        return view('guestbook', [
            'entries' => GuestbookEntry::latest()->paginate(10),
            'total' => GuestbookEntry::count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Honeypot: field ini tersembunyi untuk manusia, biasanya hanya diisi bot.
        if (filled($request->input('website'))) {
            return back()->with('success', 'Terima kasih sudah mengisi buku tamu!');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'message' => ['required', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 60 karakter.',
            'message.required' => 'Pesan wajib diisi.',
            'message.max' => 'Pesan maksimal 500 karakter.',
        ]);

        GuestbookEntry::create([
            ...$validated,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Terima kasih, pesanmu sudah tersimpan!');
    }
}
