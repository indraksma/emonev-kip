<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadanPublik extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        // Data Badan Publik
        'nama_badan_publik',
        'website',
        'telepon_badan_publik',
        'email_badan_publik',
        'alamat',
        // Data Responden Tambahan
        'telepon_responden',
        'jabatan',
        // Data PPID
        'nama_ppid',
        'telepon_ppid',
        'email_ppid',
    ];

    /**
     * Mendefinisikan bahwa setiap data Badan Publik dimiliki oleh satu User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}