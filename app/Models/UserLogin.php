<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLogin extends Model
{
    use Searchable;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
