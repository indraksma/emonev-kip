<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penilaian extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'penilaians'; // Opsional jika nama tabel Anda 'penilaians'

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // ID dari user/dinas yang dinilai
        'status',  // Hasil penilaian (misal: 'Sangat Informatif')
        'total_skor', // Contoh kolom lain jika ada
        'submission_id',
        'nilai',
        'status_informatif',
    ];

    /**
     * Mendefinisikan relasi bahwa satu Penilaian dimiliki oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}