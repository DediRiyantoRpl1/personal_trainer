<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about', ['t' => config('trainer')]);
    }

    public function services()
    {
        return view('pages.services', ['t' => config('trainer')]);
    }

    public function booking()
    {
        return view('pages.booking', ['t' => config('trainer')]);
    }

    public function contact()
    {
        return view('pages.contact', ['c' => config('trainer.contact')]);
    }

    public function contactStore(Request $request)
    {
        // jebakan bot: kolom "website" disembunyikan, manusia tidak mengisinya
        if ($request->filled('website')) {
            return redirect()->route('contact')->with('toast', 'Pesan terkirim. Terima kasih!');
        }

        $data = $request->validate([
            'nama'  => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email:filter', 'max:150'],
            'telp'  => ['nullable', 'regex:/^(\+62|62|0)8\d{7,12}$/'],
            'pesan' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'nama.min'   => 'Nama minimal 2 huruf.',
            'telp.regex' => 'Nomor WhatsApp tidak valid (contoh 0812xxxxxxx).',
            'pesan.min'  => 'Pesan minimal 10 karakter.',
        ]);

        Message::create($data);

        return redirect()->route('contact')->with('toast', 'Pesan terkirim. Coach Dedi akan membalas secepatnya!');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }
}
