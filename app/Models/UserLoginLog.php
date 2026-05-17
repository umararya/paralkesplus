<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLoginLog extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'login_at',
        'logout_at',
    ];

    protected $casts = [
        'login_at'  => 'datetime',
        'logout_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hitung durasi sesi dalam format human-readable
     */
    public function getDurasiAttribute(): string
    {
        if (!$this->login_at || !$this->logout_at) {
            return '—';
        }
        $diff = $this->login_at->diff($this->logout_at);
        if ($diff->h > 0) {
            return $diff->h . 'j ' . $diff->i . 'm';
        }
        if ($diff->i > 0) {
            return $diff->i . 'm ' . $diff->s . 'd';
        }
        return $diff->s . ' detik';
    }
}