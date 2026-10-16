<?php

namespace App\Http\Controllers;

use App\Exports\RekapExport;
use App\Models\Santri;
use App\Models\Setoran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RekapController extends Controller
{
    private function filteredQuery(Request $request)
    {
        $query = Setoran::with(['santri', 'ustad'])->latest('tanggal_setoran');

        if ($request->filled('santri_id')) {
            $query->where('santri_id', $request->query('santri_id'));
        }
        if ($request->filled('jenis')) {
            $query->where('jenis_setoran', $request->query('jenis'));
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal_setoran', '>=', $request->query('dari'));
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_setoran', '<=', $request->query('sampai'));
        }
        if ($request->filled('juz')) {
            $query->where(function ($w) use ($request) {
                $w->where('juz_mulai', $request->query('juz'))
                    ->orWhere('juz_selesai', $request->query('juz'));
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $setoran = $this->filteredQuery($request)->paginate(15)->withQueryString();

        // Rekap per santri dari hasil filter
        $rekap = $this->filteredQuery($request)->get()
            ->groupBy('santri_id')
            ->map(function ($items) {
                $first = $items->first();
                return (object) [
                    'santri' => $first->santri,
                    'total_setoran' => $items->count(),
                    'juz_unik' => $items->whereNotNull('juz_mulai')->unique('juz_mulai')->count(),
                ];
            })->values();

        $santriList = Santri::orderBy('nama')->get();

        return view('rekap.index', compact('setoran', 'rekap', 'santriList'));
    }

    public function exportPdf(Request $request)
    {
        $rekap = $this->filteredQuery($request)->get()
            ->groupBy('santri_id')
            ->map(function ($items) {
                $first = $items->first();
                return [
                    'santri' => $first->santri?->nama ?? '-',
                    'total_setoran' => $items->count(),
                    'juz_unik' => $items->whereNotNull('juz_mulai')->unique('juz_mulai')->count(),
                ];
            })->values()->toArray();

        $pdf = Pdf::loadView('rekap.pdf', compact('rekap'));

        return $pdf->download('rekap-setoran.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['santri_id', 'jenis', 'dari', 'sampai', 'juz']);

        return Excel::download(new RekapExport($filters), 'rekap-setoran.xlsx');
    }
}
