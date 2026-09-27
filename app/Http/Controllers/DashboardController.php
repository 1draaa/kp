<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Ustad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function admin()
    {
        $totalSantri = Santri::count();
        $totalUstad = Ustad::count();
        $totalOrangTua = OrangTua::count();
        $totalSetoran = Setoran::count();

        $setoranHariIni = Setoran::whereDate('tanggal_setoran', today())->count();

        $countPerJenis = Setoran::select('jenis_setoran', DB::raw('count(*) as total'))
            ->groupBy('jenis_setoran')
            ->pluck('total', 'jenis_setoran')
            ->toArray();
        foreach (Setoran::JENIS as $jenis) {
            $countPerJenis[$jenis] = $countPerJenis[$jenis] ?? 0;
        }

        // Setoran per bulan 12 bulan terakhir (untuk chart)
        $labels = [];
        $dataBulanan = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);
            $labels[] = $bulan->translatedFormat('M Y');
            $dataBulanan[] = Setoran::whereYear('tanggal_setoran', $bulan->year)
                ->whereMonth('tanggal_setoran', $bulan->month)
                ->count();
        }

        $setoranByJenis = $countPerJenis;

        $setoranTerbaru = Setoran::with(['santri', 'ustad'])->latest('tanggal_setoran')->latest('id')->take(10)->get();
        $santriTerbaru = Santri::with(['ustad', 'orangTua'])->latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'totalSantri', 'totalUstad', 'totalOrangTua', 'totalSetoran',
            'setoranHariIni', 'countPerJenis', 'labels', 'dataBulanan',
            'setoranByJenis', 'setoranTerbaru', 'santriTerbaru'
        ));
    }

    public function ustad()
    {
        $ustad = Ustad::where('user_id', auth()->id())->firstOrFail();

        $santriDibimbing = $ustad->santri()->count();

        $setoranHariIni = $ustad->setoran()->whereDate('tanggal_setoran', today())->count();
        $setoranMingguIni = $ustad->setoran()
            ->whereBetween('tanggal_setoran', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
            ->count();

        $listSantri = $ustad->santri()
            ->with(['setoran' => fn ($q) => $q->latest('tanggal_setoran')->latest('id')->take(1)])
            ->orderBy('nama')
            ->get();

        return view('dashboard.ustad', compact(
            'ustad', 'santriDibimbing', 'setoranHariIni', 'setoranMingguIni', 'listSantri'
        ));
    }

    public function orangtua()
    {
        // firstOrCreate agar akun lama yang belum punya baris orang_tua tidak 404
        $orangTua = OrangTua::firstOrCreate(
            ['user_id' => auth()->id()],
            ['nama' => auth()->user()->name]
        );

        $anakList = $orangTua->santri()->with(['ustad', 'setoran' => fn ($q) => $q->latest('tanggal_setoran')->latest('id')])->get()
            ->map(function (Santri $anak) {
                $anak->total_setoran = $anak->setoran()->count();
                $anak->nilai_rata2 = round((float) $anak->setoran()->avg('nilai'), 1);
                $anak->setoran_terakhir = $anak->setoran()->latest('tanggal_setoran')->latest('id')->first();
                $anak->juz_unik = $anak->setoran()->whereNotNull('juz_mulai')->distinct()->count('juz_mulai');
                return $anak;
            });

        return view('dashboard.orangtua', compact('orangTua', 'anakList'));
    }
}
