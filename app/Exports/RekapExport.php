<?php

namespace App\Exports;

use App\Models\Setoran;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RekapExport implements FromCollection, WithHeadings
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return ['Santri', 'NIS', 'Total Setoran', 'Rata-rata Nilai', 'Juz Unik'];
    }

    public function collection(): Collection
    {
        $query = Setoran::with('santri')->latest('tanggal_setoran');

        if (! empty($this->filters['santri_id'])) {
            $query->where('santri_id', $this->filters['santri_id']);
        }
        if (! empty($this->filters['jenis'])) {
            $query->where('jenis_setoran', $this->filters['jenis']);
        }
        if (! empty($this->filters['dari'])) {
            $query->whereDate('tanggal_setoran', '>=', $this->filters['dari']);
        }
        if (! empty($this->filters['sampai'])) {
            $query->whereDate('tanggal_setoran', '<=', $this->filters['sampai']);
        }
        if (! empty($this->filters['juz'])) {
            $juz = $this->filters['juz'];
            $query->where(fn ($w) => $w->where('juz_mulai', $juz)->orWhere('juz_selesai', $juz));
        }

        return $query->get()->groupBy('santri_id')->map(function ($items) {
            $santri = $items->first()->santri;
            return [
                $santri?->nama ?? '-',
                $santri?->nis ?? '-',
                $items->count(),
                round((float) $items->avg('nilai'), 1),
                $items->whereNotNull('juz_mulai')->unique('juz_mulai')->count(),
            ];
        })->values();
    }
}
