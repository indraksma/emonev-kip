<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Pesan extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'isi',
    ];

    protected $casts = [
        'created_at' => 'datetime:d F Y H:i',
        'updated_at' => 'datetime:d F Y H:i',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pesan_user')
                    ->withTimestamps()
                    ->withPivot('dibaca_pada');
    }
}
