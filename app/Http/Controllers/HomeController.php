<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $used = Booking::whereBetween('tanggal', [
            $now->copy()->startOfMonth()->toDateString(),
            $now->copy()->endOfMonth()->toDateString(),
        ])->count();

        return view('home', [
            't'        => config('trainer'),
            'slotLeft' => max(1, config('trainer.monthly_quota') - $used),
        ]);
    }
}
