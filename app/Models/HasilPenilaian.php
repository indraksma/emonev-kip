<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilPenilaian extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jadwal_id',
        'nilai_akhir',
        'klasifikasi_penilaian_id',
        'status_verifikasi',
        'verified_at',
    ];

    protected $casts = [
        'nilai_akhir' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function klasifikasiPenilaian(): BelongsTo
    {
        return $this->belongsTo(KlasifikasiPenilaian::class);
    }
}
