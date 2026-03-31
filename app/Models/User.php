<?php

namespace App\Models;

use App\Models\Concerns\PreservesUniqueValuesOnSoftDelete;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PreservesUniqueValuesOnSoftDelete, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'secret_code',
        'role',
        'stock_location_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'secret_code',
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
            'secret_code' => 'hashed',
        ];
    }

    protected function uniqueSoftDeleteColumns(): array
    {
        return ['email'];
    }

    public function stockLocation(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'stock_location_id');
    }

    public function isAdministrator(): bool
    {
        return $this->role === 'owner';
    }
}
