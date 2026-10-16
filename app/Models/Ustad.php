<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ustad extends Model
{
    use HasFactory;

    protected $table = 'ustad';

    protected $fillable = ['user_id', 'niu', 'nama', 'nomor_hp', 'alamat', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function santri(): HasMany
    {
        return $this->hasMany(Santri::class, 'ustad_id');
    }

    public function setoran(): HasMany
    {
        return $this->hasMany(Setoran::class, 'ustad_id');
    }
}
