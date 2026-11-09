<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

/** Artikel contoh. Aman dijalankan berulang (tidak membuat duplikat). */
class PostSeeder extends Seeder
{
    public function run(): void
    {
        $img = fn (string $id) => "https://images.unsplash.com/{$id}?auto=format&fit=crop&w=1200&q=70";

        $posts = [
            [
                'slug'  => '5-kesalahan-pemula-di-gym',
                'title' => '5 Kesalahan Pemula di Gym yang Bikin Hasil Lambat',
                'excerpt' => 'Rajin ke gym tapi badan tidak berubah? Kemungkinan besar kamu melakukan salah satu dari lima kesalahan ini.',
                'image_url' => $img('photo-1534438327276-14e5300c3a48'),
                'days' => 3,
                'body' => "Banyak orang rajin datang ke gym tetapi hasilnya terasa jalan di tempat. Biasanya bukan karena kurang usaha, melainkan karena beberapa kebiasaan kecil yang diulang terus.\n\n## 1. Tidak punya program yang jelas\nGanti-ganti latihan setiap hari membuat tubuh tidak punya kesempatan beradaptasi. Pilih program sederhana, jalankan minimal 8 minggu, lalu evaluasi.\n\n## 2. Beban naik terlalu cepat\nMenambah beban sebelum teknik rapi adalah jalan tercepat menuju cedera. Kuasai gerakan dulu, baru naikkan beban sedikit demi sedikit.\n\n## 3. Mengabaikan pemanasan\nLima sampai sepuluh menit pemanasan dan mobilitas membuat sendi siap bekerja dan sesi latihan terasa lebih ringan.\n\n## 4. Kurang protein dan kurang tidur\nOtot tumbuh saat istirahat, bukan saat latihan. Tidur 7 jam dan asupan protein yang cukup sering lebih berpengaruh daripada tambahan satu set.\n\n## 5. Tidak mencatat progres\nTanpa catatan, kamu tidak tahu apakah latihan berjalan ke arah yang benar. Catat beban, repetisi, dan berat badan tiap minggu.",
            ],
            [
                'slug'  => 'cara-menghitung-kebutuhan-kalori-harian',
                'title' => 'Cara Menghitung Kebutuhan Kalori Harian dengan Mudah',
                'excerpt' => 'Mau turun berat badan atau menambah otot? Mulailah dari mengetahui berapa kalori yang tubuhmu butuhkan.',
                'image_url' => $img('photo-1490645935967-10de6ba17061'),
                'days' => 10,
                'body' => "Kebutuhan kalori adalah titik awal dari hampir semua target tubuh. Kalau tujuanmu turun berat badan, kamu perlu sedikit di bawahnya. Kalau ingin menambah otot, sedikit di atasnya.\n\n## Langkah 1: Hitung BMR\nBMR adalah energi yang dibutuhkan tubuh saat istirahat. Rumus Mifflin-St Jeor: 10 x berat (kg) + 6,25 x tinggi (cm) - 5 x usia, lalu tambah 5 untuk pria atau kurangi 161 untuk wanita.\n\n## Langkah 2: Kalikan dengan tingkat aktivitas\nJarang olahraga dikali 1,2. Olahraga 1-3 kali seminggu dikali 1,375. Olahraga 3-5 kali seminggu dikali 1,55. Olahraga 6-7 kali seminggu dikali 1,725.\n\n## Langkah 3: Sesuaikan dengan target\nUntuk turun berat badan, kurangi sekitar 300-500 kkal. Untuk menambah otot, tambah sekitar 200-300 kkal.\n\nHasil ini hanya perkiraan awal. Kamu juga bisa memakai kalkulator di halaman utama website ini, lalu kita sesuaikan bersama saat sesi konsultasi.",
            ],
            [
                'slug'  => 'tetap-konsisten-olahraga-saat-sibuk',
                'title' => 'Tetap Konsisten Olahraga Saat Jadwal Sedang Padat',
                'excerpt' => 'Konsistensi lebih penting daripada intensitas. Ini cara menjaga kebiasaan olahraga di minggu yang berantakan.',
                'image_url' => $img('photo-1517836357463-d25dfeac3438'),
                'days' => 17,
                'body' => "Yang sulit dari olahraga bukan latihannya, tetapi menjaga kebiasaannya. Saat pekerjaan menumpuk, olahraga biasanya yang pertama dikorbankan.\n\n## Turunkan standar, jangan berhenti\nTidak sempat 60 menit? Lakukan 20 menit. Sesi pendek tetap menjaga kebiasaan dan momentum.\n\n## Jadwalkan seperti rapat\nMasukkan jam latihan ke kalender dan perlakukan sebagai janji yang tidak bisa dipindah sembarangan.\n\n## Siapkan dari malam sebelumnya\nSiapkan pakaian dan botol minum. Semakin sedikit keputusan di pagi hari, semakin besar peluang kamu benar-benar berangkat.\n\n## Cari akuntabilitas\nBerlatih dengan teman atau pelatih membuatmu lebih sulit membatalkan di menit terakhir. Itulah alasan banyak klien merasa terbantu dengan pendampingan mingguan.",
            ],
        ];

        foreach ($posts as $p) {
            $days = $p['days'];
            unset($p['days']);
            Post::firstOrCreate(['slug' => $p['slug']], $p + ['published_at' => now()->subDays($days)]);
        }
    }
}
