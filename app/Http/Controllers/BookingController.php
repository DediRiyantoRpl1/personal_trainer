<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    /** Semua jam sesi untuk satu tanggal (yang sudah lewat tidak ikut). */
    private function slotsFor(string $date): array
    {
        $day = Carbon::createFromFormat('Y-m-d', $date)->dayOfWeek;
        $slots = config('trainer.slots.' . (in_array($day, [0, 6], true) ? 'weekend' : 'weekday'));

        if ($date === Carbon::now()->toDateString()) {
            $now = Carbon::now()->format('H:i');
            $slots = array_values(array_filter($slots, fn ($s) => $s > $now));
        }

        return $slots;
    }

    /** GET /slot?tanggal=2026-11-10 */
    public function slots(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date_format:Y-m-d'],
        ]);

        $taken = Booking::where('tanggal', $data['tanggal'])->pluck('jam')->all();

        return response()->json([
            'slots' => array_map(fn ($s) => ['jam' => $s, 'taken' => in_array($s, $taken, true)],
                $this->slotsFor($data['tanggal'])),
        ]);
    }

    /** GET /booking-saya  -> booking akun yang sedang login (yang akan datang) */
    public function mine(Request $request): JsonResponse
    {
        $items = Booking::where('user_id', $request->user()->id)
            ->where('tanggal', '>=', Carbon::now()->toDateString())
            ->orderBy('tanggal')->orderBy('jam')->get()
            ->map(fn (Booking $b) => $this->present($b))->values();

        return response()->json(['bookings' => $items]);
    }

    /** POST /booking */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'paket'   => ['required', Rule::in(array_column(config('trainer.services'), 'name'))],
            'nama'    => ['required', 'string', 'min:2', 'max:100'],
            'wa'      => ['required', 'regex:/^(\+62|62|0)8\d{7,12}$/'],
            'tanggal' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'jam'     => ['required', 'date_format:H:i'],
            'target'  => ['required', Rule::in(config('trainer.goals'))],
        ], [
            'nama.min'              => 'Nama minimal 2 huruf.',
            'wa.regex'              => 'Nomor WhatsApp tidak valid (contoh 0812xxxxxxx).',
            'tanggal.required'      => 'Pilih tanggal dulu.',
            'tanggal.after_or_equal' => 'Tanggal tidak boleh sudah lewat.',
            'jam.required'          => 'Pilih jam dulu.',
        ]);

        // hapus spasi/strip pada nomor WA sebelum disimpan
        $data['wa'] = preg_replace('/[\s-]/', '', $data['wa']);

        if (! in_array($data['jam'], $this->slotsFor($data['tanggal']), true)) {
            return response()->json([
                'message' => 'Jam tersebut tidak tersedia.',
                'errors'  => ['jam' => ['Jam tersebut tidak tersedia.']],
            ], 422);
        }

        try {
            $booking = Booking::create($data + ['user_id' => $request->user()->id]);
        } catch (UniqueConstraintViolationException) {
            return response()->json([
                'message' => 'Slot ini baru saja dipesan orang lain.',
                'errors'  => ['jam' => ['Slot ini baru saja dipesan orang lain.']],
            ], 409);
        }

        return response()->json(['booking' => $this->present($booking)], 201);
    }

    /** DELETE /booking/{booking}  -> hanya pemilik booking */
    public function destroy(Request $request, Booking $booking): JsonResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 403);

        $booking->delete();

        return response()->json(['ok' => true]);
    }

    private function present(Booking $b): array
    {
        return [
            'id'      => $b->id,
            'tanggal' => Carbon::parse($b->tanggal)->toDateString(),
            'jam'     => $b->jam,
            'paket'   => $b->paket,
            'target'  => $b->target,
            'nama'    => $b->nama,
        ];
    }
}
