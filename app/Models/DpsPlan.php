<?php

namespace App\Models;

use App\Traits\ApiQuery;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DpsPlan extends Model
{
    use ApiQuery, GlobalStatus;

    public function verifications(): MorphMany
    {
        return $this->morphMany(OtpVerification::class, 'verifiable');
    }

    public function delayCharge(): Attribute
    {
        return Attribute::make(get: fn () => $this->fixed_charge + ($this->per_installment * $this->percent_charge / 100));
    }
}
