<?php

/*
| Konten & pengaturan website Coach Dedi.
| Ubah teks, harga, foto, dan jam sesi di sini tanpa menyentuh Blade.
*/

$img = fn (string $id, int $w, int $q = 70) => "https://images.unsplash.com/{$id}?auto=format&fit=crop&w={$w}&q={$q}";

return [
    'name'     => 'Coach Dedi',
    'fullname' => 'Dedi Pratama',
    'city'     => 'Yogyakarta',

    // Subdomain khusus admin, mis. admin.personal-trainer-laravel.test (isi lewat .env: ADMIN_DOMAIN).
    // Kosongkan untuk memakai alamat biasa: /admin/masuk di domain yang sama.
    'admin_domain' => env('ADMIN_DOMAIN') ?: null,

    // Sesi admin berakhir otomatis setelah sekian menit tanpa aktivitas.
    'admin_idle_minutes' => (int) env('ADMIN_IDLE_MINUTES', 30),

    // Wajibkan 2FA untuk semua admin. Admin yang belum mengaktifkannya hanya bisa membuka
    // halaman Keamanan (untuk mengaktifkan) dan keluar, sampai 2FA aktif.
    'admin_require_2fa' => (bool) env('ADMIN_REQUIRE_2FA', true),

    // Kuota sesi baru per bulan (untuk label "Slot bulan ini tersisa")
    'monthly_quota' => 10,

    // Jam sesi: 0 = Minggu ... 6 = Sabtu (sesuai Carbon::dayOfWeek)
    'slots' => [
        'weekday' => ['07:00', '09:00', '11:00', '16:00', '18:00'],
        'weekend' => ['08:00', '10:00', '14:00', '16:00'],
    ],

    'hero' => [
        'photo' => $img('photo-1567013127542-490d757e51fc', 800),
    ],

    'about' => [
        'photo' => $img('photo-1583454110551-21f2fa2afe61', 700),
        'certs' => ['NASM-CPT', 'Precision Nutrition L1', 'FMS Level 1', 'CPR/AED'],
        'steps' => [
            ['01', 'Asesmen', 'Postur, kekuatan, dan target.'],
            ['02', 'Program', 'Latihan & nutrisi khusus.'],
            ['03', 'Pendampingan', 'Sesi + check-in mingguan.'],
            ['04', 'Evaluasi', 'Ukur, sesuaikan, naik level.'],
        ],
    ],

    'services' => [
        [
            'name'  => 'Personal Training',
            'icon'  => '🏋️',
            'desc'  => 'Sesi tatap muka 60 menit di gym pilihanmu dengan koreksi teknik langsung.',
            'price' => 'Mulai Rp 250rb / sesi',
            'img'   => $img('photo-1571019613454-1cb2f99b2d8b', 700),
        ],
        [
            'name'  => 'Online Coaching',
            'icon'  => '💻',
            'desc'  => 'Program mingguan lewat aplikasi, video koreksi form, dan chat harian.',
            'price' => 'Mulai Rp 600rb / bulan',
            'img'   => $img('photo-1571019614242-c5c5dee9f50b', 700),
        ],
        [
            'name'  => 'Nutrition Plan',
            'icon'  => '🥗',
            'desc'  => 'Panduan makan sesuai selera lokal, tanpa diet ekstrem atau suplemen mahal.',
            'price' => 'Mulai Rp 350rb / bulan',
            'img'   => $img('photo-1490645935967-10de6ba17061', 700),
        ],
    ],

    'transform' => [
        'before' => ['img' => $img('photo-1581009146145-b5ef050c2e1e', 1200), 'weight' => '94 kg', 'fat' => 'Lemak tubuh 31%'],
        'after'  => ['img' => $img('photo-1583454110551-21f2fa2afe61', 1200), 'weight' => '76 kg', 'fat' => 'Lemak tubuh 17%'],
        'caption' => 'Dimas, 34 th — program 16 minggu',
    ],

    'testimonials' => [
        [
            'quote'  => 'Baru kali ini saya bertahan olahraga lebih dari 3 bulan. Coach Dedi selalu tahu kapan harus mendorong dan kapan harus istirahat.',
            'who'    => 'Nadia, 29 — Karyawan swasta',
            'avatar' => $img('photo-1494790108377-be9c29b29330', 120),
        ],
        [
            'quote'  => 'Nyeri punggung saya hilang setelah 8 minggu. Program disesuaikan dengan jadwal shift saya yang berantakan.',
            'who'    => 'Andri, 37 — Perawat',
            'avatar' => $img('photo-1507003211169-0a1dd7228f2d', 120),
        ],
        [
            'quote'  => 'Nutrition plan-nya realistis, tetap bisa makan nasi padang. Turun 9 kg tanpa menderita.',
            'who'    => 'Putri, 31 — Desainer',
            'avatar' => $img('photo-1438761681033-6461ffad8d80', 120),
        ],
    ],

    // ⚠ Data contoh — GANTI dengan data asli Coach Dedi
    'contact' => [
        'wa'        => '6288216158488',          // format 62..., tanpa + dan tanpa spasi
        'phone'     => '+62 882-1615-8488',
        'email'     => 'halo@coachdedi.test',
        'address'   => 'Jl. Contoh No. 12, Yogyakarta',
        'hours'     => 'Senin–Sabtu, 07.00–19.00 WIB',
        'instagram' => 'coachdedi',
        'map'       => 'https://www.google.com/maps?q=Yogyakarta&output=embed',
    ],

    'faq' => [
        ['Apakah sesi pertama benar-benar gratis?', 'Ya. Sesi pertama berdurasi 30 menit berisi asesmen postur, mobilitas, dan diskusi target. Tanpa kewajiban berlangganan.'],
        ['Saya pemula dan belum pernah ke gym. Apakah cocok?', 'Sangat cocok. Program disusun dari kondisi awalmu, termasuk cedera masa lalu dan jadwal harianmu. Teknik dasar diajarkan dari nol.'],
        ['Berapa kali sebaiknya berlatih dalam seminggu?', 'Untuk kebanyakan orang 2–4 kali seminggu sudah cukup. Frekuensi pastinya kita tentukan saat asesmen.'],
        ['Apa bedanya Personal Training dan Online Coaching?', 'Personal Training adalah sesi tatap muka 60 menit dengan koreksi langsung. Online Coaching berupa program mingguan, video koreksi form, dan chat harian, cocok kalau jadwalmu fleksibel atau kamu berada di luar kota.'],
        ['Apakah ada diet ekstrem atau suplemen wajib?', 'Tidak. Nutrition Plan memakai makanan sehari-hari yang mudah ditemukan, tanpa diet ekstrem dan tanpa suplemen wajib.'],
        ['Bagaimana cara membatalkan atau mengubah jadwal?', 'Masuk ke akunmu, lalu batalkan booking dari bagian "Booking kamu". Untuk mengganti jadwal, batalkan lalu pilih jam baru, atau hubungi lewat WhatsApp.'],
        ['Bagaimana pembayaran dilakukan?', 'Pembayaran dibahas setelah sesi gratis, sesuai paket yang kamu pilih. Detailnya dikonfirmasi lewat WhatsApp.'],
    ],

    'gallery' => [
        ['img' => $img('photo-1534438327276-14e5300c3a48', 900), 'alt' => 'Latihan beban'],
        ['img' => $img('photo-1571019613454-1cb2f99b2d8b', 900), 'alt' => 'Sesi personal training'],
        ['img' => $img('photo-1517836357463-d25dfeac3438', 900), 'alt' => 'Latihan fungsional'],
        ['img' => $img('photo-1571019614242-c5c5dee9f50b', 900), 'alt' => 'Koreksi teknik'],
        ['img' => $img('photo-1583454110551-21f2fa2afe61', 900), 'alt' => 'Hasil transformasi klien'],
        ['img' => $img('photo-1544367567-0f2fcb009e0b', 900), 'alt' => 'Mobilitas dan peregangan'],
        ['img' => $img('photo-1490645935967-10de6ba17061', 900), 'alt' => 'Menu nutrisi sehat'],
        ['img' => $img('photo-1534258936925-c58bed479fcb', 900), 'alt' => 'Area latihan'],
    ],

    'goals' => ['Turun berat badan', 'Bangun otot', 'Rehabilitasi / nyeri', 'Stamina & kebugaran'],
];
