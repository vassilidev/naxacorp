<?php

namespace App\Models;

use App\Traits\ApiQuery;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use ApiQuery, Searchable;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function branchStaff(): BelongsTo
    {
        return $this->belongsTo(BranchStaff::class, 'branch_staff_id');
    }

    public function scopeReferralCommission($query)
    {
        return $query->where('remark', 'referral_commission');
    }

    public function scopePlus($query)
    {
        return $query->where('trx_type', '+');
    }

    public function scopeMinus($query)
    {
        return $query->where('trx_type', '-');
    }

    public function scopeSumAmount($query)
    {
        return $query->selectRaw("SUM(amount) as amount, DATE_FORMAT(created_at,'%Y-%m-%d') as date");
    }

    public function scopeLastDays($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
