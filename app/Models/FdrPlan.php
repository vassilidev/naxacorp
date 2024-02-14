<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class FdrPlan extends Model
{
    use GlobalStatus;

    public function verifications(): MorphMany
    {
        return $this->morphMany(OtpVerification::class, 'verifiable');
    }
}
