<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    use Searchable;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
