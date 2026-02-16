<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingEmailVerification extends Model
{
    protected $fillable = [
        'email',
        'name',
        'password',
        'code',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function matchesCode(string $code): bool
    {
        return $this->code === $code;
    }
}
