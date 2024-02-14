<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OtpVerification extends Model
{
    public $timestamps = false;

    protected $casts = [
        'additional_data' => 'object',
        'send_at' => 'datetime',
        'used_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function verifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
