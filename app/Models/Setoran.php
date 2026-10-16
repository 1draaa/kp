<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setoran extends Model
{
    use HasFactory;

    protected $table = 'setoran';

    protected $fillable = [
        'santri_id', 'ustad_id', 'jenis_setoran', 'tanggal_setoran',
        'juz_mulai', 'juz_selesai', 'surat_mulai', 'surat_selesai',
        'ayat_mulai', 'ayat_selesai', 'jumlah_halaman',
        'jumlah_kesalahan',
        'status', 'catatan',
    ];

    protected function casts(): array
    {
        return ['tanggal_setoran' => 'date'];
    }

    public const JENIS = ['sabaq', 'sabqy', 'qiroah', 'manzil'];

    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function ustad(): BelongsTo
    {
        return $this->belongsTo(Ustad::class, 'ustad_id');
    }

    public function labelJenis(): string
    {
        return strtoupper($this->jenis_setoran);
    }

    public function ringkasanMateri(): string
    {
        if ($this->jenis_setoran === 'manzil') {
            return "Juz {$this->juz_mulai}-{$this->juz_selesai}";
        }
        $s = $this->surat_mulai ?? '-';
        if ($this->surat_selesai && $this->surat_selesai !== $this->surat_mulai) $s .= " - {$this->surat_selesai}";
        if ($this->ayat_mulai !== null) $s .= " : {$this->ayat_mulai}-{$this->ayat_selesai}";
        if ($this->juz_mulai !== null) $s .= " (Juz {$this->juz_mulai})";
        return $s;
    }
}
