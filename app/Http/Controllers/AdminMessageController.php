<?php

namespace App\Http\Controllers;

use App\Models\Message;

class AdminMessageController extends Controller
{
    public function index()
    {
        return view('admin.messages', [
            'unread' => Message::where('dibaca', false)->latest()->limit(100)->get(),
            'read'   => Message::where('dibaca', true)->latest()->limit(30)->get(),
        ]);
    }

    public function read(Message $message)
    {
        $message->update(['dibaca' => true]);

        return back();
    }

    /** Hapus semua pesan yang sudah dibaca (bagian Riwayat). Pesan baru tidak disentuh. */
    public function clearHistory()
    {
        $count = Message::where('dibaca', true)->delete();

        return back()->with('ok', "Riwayat pesan dihapus ({$count} pesan).");
    }

    public function destroy(Message $message)
    {
        $message->delete();

        return back()->with('ok', 'Pesan dihapus.');
    }
}
