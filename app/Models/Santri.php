<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Santri extends Model
{
    use HasFactory;

    protected $table = 'santri';

    protected $fillable = [
        'nama', 'nis', 'jenis_kelamin', 'tanggal_lahir', 'alamat',
        'orang_tua_id', 'ustad_id', 'tanggal_masuk', 'target_hafalan', 'status',
    ];

    protected function casts(): array
    {
        return ['tanggal_lahir' => 'date', 'tanggal_masuk' => 'date'];
    }

    public function orangTua(): BelongsTo
    {
        return $this->belongsTo(OrangTua::class, 'orang_tua_id');
    }

    public function ustad(): BelongsTo
    {
        return $this->belongsTo(Ustad::class, 'ustad_id');
    }

    public function setoran(): HasMany
    {
        return $this->hasMany(Setoran::class, 'santri_id')->latest('tanggal_setoran');
    }

    public function totalJuz(): float
    {
        $juz = $this->setoran()->where('jenis_setoran', 'manzil')->sum('jumlah_halaman');
        return $juz;
    }

    public function progressPersen(): float
    {
        if (! $this->target_hafalan) return 0;
        $juz = $this->setoran()->whereIn('jenis_setoran', ['sabaq', 'sabqy'])
            ->distinct()->count();
        // estimasi sederhana: jumlah setoran sabaq unik juz
        $juzUnik = $this->setoran()->whereIn('jenis_setoran', ['sabaq', 'sabqy', 'manzil'])
            ->select('juz_mulai')->distinct()->count('juz_mulai');
        return min(100, round($juzUnik / max(1, $this->target_hafalan) * 100, 1));
    }
}
