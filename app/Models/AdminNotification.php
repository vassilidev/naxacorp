<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
