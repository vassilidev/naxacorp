<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class WithdrawMethod extends Model
{
    use GlobalStatus;

    protected $casts = [
        'user_data' => 'object',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function verifications(): MorphMany
    {
        return $this->morphMany(OtpVerification::class, 'verifiable');
    }
}
