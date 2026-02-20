<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Define the one-to-one relationship with BadanPublik.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function badanPublik(): HasOne
    {
        return $this->hasOne(BadanPublik::class);
    }

    public function pesans(): BelongsToMany
    {
        return $this->belongsToMany(Pesan::class, 'pesan_user')
                ->withTimestamps()
                ->withPivot('dibaca_pada')
                ->latest('pivot_created_at');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function hasilPenilaians(): HasMany
    {
        return $this->hasMany(HasilPenilaian::class);
    }

}
