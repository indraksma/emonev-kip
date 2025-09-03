<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pertanyaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_id', 
        'teks_pertanyaan',
        'tipe_jawaban',
        'butuh_link',
        'butuh_upload'
    ];
}