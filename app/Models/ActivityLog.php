<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'module',
        'action',
        'subject',
        'page_url',
        'old_value',
        'new_value',
        'ip_address',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper static untuk mencatat aktivitas dari mana saja
     */
    public static function record(
        string $module,
        string $action,
        string $subject  = '',
        array  $oldValue = [],
        array  $newValue = [],
        string $pageUrl  = ''
    ): void {
        if (!auth()->check()) return;

        static::create([
            'user_id'    => auth()->id(),
            'module'     => $module,
            'action'     => $action,
            'subject'    => $subject,
            'page_url'   => $pageUrl ?: request()->path(),
            'old_value'  => $oldValue ?: null,
            'new_value'  => $newValue ?: null,
            'ip_address' => request()->ip(),
        ]);
    }
}