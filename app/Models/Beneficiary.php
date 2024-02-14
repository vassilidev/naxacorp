<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use ApiQuery;

    protected $casts = [
        'details' => 'object',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifications(): MorphMany
    {
        return $this->morphMany(OtpVerification::class, 'verifiable');
    }

    public function beneficiaryOf(): MorphTo
    {
        return $this->morphTo('beneficiaryOf', 'beneficiary_type', 'beneficiary_id');
    }

    public function scopeOwnBank()
    {
        return $this->where('beneficiary_type', User::class);
    }

    public function scopeOtherBank()
    {
        return $this->where('beneficiary_type', OtherBank::class);
    }
}
