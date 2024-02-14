<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class FdrPlan extends Model
{
    use GlobalStatus;

    public function verifications(): MorphMany
    {
        return $this->morphMany(OtpVerification::class, 'verifiable');
    }
}
