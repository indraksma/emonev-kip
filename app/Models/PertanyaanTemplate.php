<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PertanyaanTemplate extends Model
{
    use HasFactory;

    protected $table = 'pertanyaan_templates';

    protected $fillable = [
        'kategori_id',
        'teks_pertanyaan',
        'tipe_jawaban',
        'butuh_link',
        'butuh_upload',
        'is_active',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function jadwalPertanyaans(): HasMany
    {
        return $this->hasMany(JadwalPertanyaan::class);
    }
}
