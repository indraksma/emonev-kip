<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KlasifikasiPenilaian extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'min_nilai',
        'max_nilai',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'min_nilai' => 'decimal:2',
        'max_nilai' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function hasilPenilaians(): HasMany
    {
        return $this->hasMany(HasilPenilaian::class);
    }
}
