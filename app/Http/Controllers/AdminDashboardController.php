<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Message;
use Illuminate\Support\Carbon;

/** Halaman Ringkasan: papan jadwal, angka penting, dan hal yang perlu ditindak. */
class AdminDashboardController extends Controller
{
    public function index()
    {
        $now   = now();
        $today = $now->copy()->startOfDay();

        // Papan jadwal: hari ini dan besok
        $boards = [
            'today'    => $this->board($today, $now),
            'tomorrow' => $this->board($today->copy()->addDay(), null),
        ];

        // Grafik 14 hari ke depan: tinggi batang = terisi / kapasitas slot hari itu
        $from   = $today->copy();
        $to     = $today->copy()->addDays(13);
        $counts = Booking::whereBetween('tanggal', [$from->toDateString(), $to->toDateString()])
            ->get()->groupBy(fn ($b) => Carbon::parse($b->tanggal)->toDateString())->map->count();

        $days = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $cap    = count($this->slotTimes($d));
            $n      = (int) ($counts[$d->toDateString()] ?? 0);
            $days[] = [
                'date'  => $d->copy(),
                'n'     => $n,
                'cap'   => $cap,
                'pct'   => $cap ? min(100, (int) round($n / $cap * 100)) : 0,
                'today' => $d->isSameDay($today),
                'off'   => in_array($d->dayOfWeek, [0, 6], true),
            ];
        }

        $monthCount = Booking::whereBetween('tanggal', [
            $today->copy()->startOfMonth()->toDateString(),
            $today->copy()->endOfMonth()->toDateString(),
        ])->count();
        $quota = (int) config('trainer.monthly_quota', 10);

        $hour     = (int) $now->format('G');
        $greeting = match (true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 18 => 'Selamat sore',
            default    => 'Selamat malam',
        };

        $pendingAll = Booking::where('status', Booking::PENDING)->where('tanggal', '>=', $today->toDateString());

        return view('admin.dashboard', [
            'greeting'     => $greeting,
            'first'        => explode(' ', trim((string) auth('admin')->user()->name))[0],
            'dateLabel'    => $now->locale('id')->translatedFormat('l, j F Y'),
            'boards'       => $boards,
            'days'         => $days,
            'todayCount'   => collect($boards['today']['slots'])->filter(fn ($s) => $s['booking'])->count(),
            'todaySlots'   => count($boards['today']['slots']),
            'pendingCount' => (clone $pendingAll)->count(),
            'pending'      => (clone $pendingAll)->orderBy('tanggal')->orderBy('jam')->limit(5)->get(),
            'monthCount'   => $monthCount,
            'quota'        => $quota,
            'unreadCount'  => Message::where('dibaca', false)->count(),
            'messages'     => Message::orderBy('dibaca')->latest()->limit(4)->get(),
        ]);
    }

    /** Daftar jam sesi menurut hari (sama dengan aturan di form booking publik). */
    private function slotTimes(Carbon $date): array
    {
        return config('trainer.slots.' . (in_array($date->dayOfWeek, [0, 6], true) ? 'weekend' : 'weekday'), []);
    }

    /** Satu hari: semua slot + siapa yang mengisi. $now diisi hanya untuk hari ini. */
    private function board(Carbon $date, ?Carbon $now): array
    {
        $booked = Booking::where('tanggal', $date->toDateString())->get()->keyBy('jam');
        $times  = collect($this->slotTimes($date))->merge($booked->keys())->unique()->sort()->values();

        $nextFound = false;
        $slots = $times->map(function ($jam) use ($booked, $now, &$nextFound) {
            $b     = $booked->get($jam);
            $state = 'later';

            if ($now) {
                if ($jam < $now->format('H:i')) {
                    $state = 'done';
                } elseif ($b && ! $nextFound) {
                    $state     = 'next';
                    $nextFound = true;
                }
            }

            return ['jam' => $jam, 'booking' => $b, 'state' => $state];
        })->all();

        return ['date' => $date, 'slots' => $slots];
    }
}
