<?php

namespace Database\Seeders;

use App\Models\OrangTua;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\User;
use App\Models\Ustad;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'password', 'role' => 'admin']);

        $ustadUsers = [];
        foreach ([
            ['Ustad Ahmad', 'ustadahmad@example.com'],
            ['Ustad Warno', 'ustadwarno@example.com'],
            ['Ustad Hanif', 'ustadhanif@example.com'],
            ['Ustad Wage', 'ustadwage@example.com'],
            ['Ustad Bowo', 'ustadbowo@example.com'],
        ] as [$nama, $email]) {
            $ustadUsers[] = User::create(['name' => $nama, 'email' => $email, 'password' => 'password', 'role' => 'ustad']);
        }
        $ustadList = [];
        foreach ($ustadUsers as $i => $u) {
            $ustadList[] = Ustad::create(['user_id' => $u->id, 'nama' => $u->name, 'nomor_hp' => '0812000000' . $i, 'alamat' => 'Pondok Pesantren', 'status' => 'aktif']);
        }

        $ortuList = [];
        $ortuDemo = User::create(['name' => 'Orang Tua Demo', 'email' => 'orangtua@example.com', 'password' => 'password', 'role' => 'orang_tua']);
        $ortuList[] = OrangTua::create(['user_id' => $ortuDemo->id, 'nama' => 'Orang Tua Demo', 'nomor_hp' => '081300000001', 'alamat' => 'Jl. Contoh 1']);
        for ($i = 1; $i <= 9; $i++) {
            $u = User::create(['name' => "Orang Tua $i", 'email' => "orangtua$i@example.com", 'password' => 'password', 'role' => 'orang_tua']);
            $ortuList[] = OrangTua::create(['user_id' => $u->id, 'nama' => "Orang Tua $i", 'nomor_hp' => '0813000000' . ($i + 10), 'alamat' => "Jl. Contoh $i"]);
        }

        $surat = ['Al-Fatihah', 'Al-Baqarah', 'Ali Imran', 'An-Nisa', 'Al-Maidah', 'Al-Anam', 'Al-Araf', 'Yasin', 'Al-Kahfi', 'Ar-Rahman', 'Al-Waqiah', 'Al-Mulk'];
        $santriList = [];
        for ($i = 1; $i <= 10; $i++) {
            $santriList[] = Santri::create([
                'nama' => "Santri $i",
                'nis' => 'NIS' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'jenis_kelamin' => $i % 2 ? 'L' : 'P',
                'tanggal_lahir' => now()->subYears(10 + ($i % 5))->toDateString(),
                'alamat' => "Jl. Santri $i",
                'orang_tua_id' => $ortuList[($i - 1) % 10]->id,
                'ustad_id' => $ustadList[($i - 1) % count($ustadList)]->id,
                'tanggal_masuk' => now()->subMonths($i)->toDateString(),
                'target_hafalan' => 5 + ($i % 3) * 5,
                'status' => 'aktif',
            ]);
        }

        $jenis = ['sabaq', 'sabqy', 'qiroah', 'manzil'];
        foreach ($santriList as $idx => $santri) {
            foreach ($jenis as $j => $jn) {
                for ($k = 0; $k < 2; $k++) {
                    $s = $surat[($idx + $j + $k) % count($surat)];
                    Setoran::create([
                        'santri_id' => $santri->id,
                        'ustad_id' => $santri->ustad_id,
                        'jenis_setoran' => $jn,
                        'tanggal_setoran' => now()->subDays(rand(0, 60))->toDateString(),
                        'juz_mulai' => $jn === 'qiroah' ? rand(1, 5) : rand(1, 10),
                        'juz_selesai' => $jn === 'manzil' ? rand(2, 11) : null,
                        'surat_mulai' => $jn === 'manzil' ? $surat[($idx + $k) % count($surat)] : $s,
                        'surat_selesai' => $jn === 'manzil' ? $surat[($idx + $k + 1) % count($surat)] : $s,
                        'ayat_mulai' => $jn === 'manzil' ? null : 1,
                        'ayat_selesai' => $jn === 'manzil' ? null : rand(5, 20),
                        'jumlah_halaman' => in_array($jn, ['sabqy']) ? rand(1, 5) : null,
                        'kelancaran' => rand(70, 100),
                        'tajwid' => rand(70, 100),
                        'makhraj' => rand(70, 100),
                        'jumlah_kesalahan' => $jn === 'qiroah' ? rand(0, 5) : null,
                        'nilai' => rand(70, 100),
                        'status' => $jn === 'qiroah' ? null : ['Lancar', 'Cukup', 'Mengulang'][rand(0, 2)],
                        'catatan' => "Setoran $jn santri {$santri->nama}",
                    ]);
                }
            }
        }
    }
}
