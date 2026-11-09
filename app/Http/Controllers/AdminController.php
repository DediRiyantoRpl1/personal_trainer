<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        return view('admin.bookings', [
            'upcoming' => Booking::where('tanggal', '>=', $today)->orderBy('tanggal')->orderBy('jam')->get(),
            'past'     => Booking::where('tanggal', '<', $today)->orderByDesc('tanggal')->orderByDesc('jam')->limit(30)->get(),
        ]);
    }

    public function confirm(Booking $booking)
    {
        $booking->update(['status' => Booking::CONFIRMED]);

        return back()->with('ok', "Booking {$booking->nama} dikonfirmasi.");
    }

    /** Hapus semua booking yang tanggalnya sudah lewat (bagian Riwayat). */
    public function clearHistory()
    {
        $count = Booking::where('tanggal', '<', now()->toDateString())->delete();

        return back()->with('ok', "Riwayat booking dihapus ({$count} data).");
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return back()->with('ok', 'Booking dihapus.');
    }
}
