<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Gateway extends Model
{
    use GlobalStatus;

    protected $casts = [
        'code' => 'string',
        'extra' => 'object',
        'input_form' => 'object',
        'supported_currencies' => 'object',
    ];

    public function currencies(): HasMany
    {
        return $this->hasMany(GatewayCurrency::class, 'method_code', 'code');
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function singleCurrency(): HasOne
    {
        return $this->hasOne(GatewayCurrency::class, 'method_code', 'code')->orderBy('id', 'desc');
    }

    public function scopeCrypto()
    {
        return $this->crypto == Status::ENABLE ? 'crypto' : 'fiat';
    }

    public function scopeAutomatic()
    {
        return $this->where('code', '<', 1000);
    }

    public function scopeManual()
    {
        return $this->where('code', '>=', 1000);
    }
}
