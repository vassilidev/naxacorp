<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\ApiQuery;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    use ApiQuery, Searchable;

    protected $casts = [
        'detail' => 'object',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class, 'method_code', 'code');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function branchStaff(): BelongsTo
    {
        return $this->belongsTo(BranchStaff::class, 'branch_staff_id');
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: fn () => $this->badgeData(),
        );
    }

    public function badgeData()
    {
        $badge = '';
        if ($this->status == Status::PAYMENT_PENDING) {
            $badge = createBadge('warning', 'Pending');
        } elseif ($this->status == Status::PAYMENT_SUCCESS && $this->method_code >= 1000) {
            $badge = createBadge('success', 'Approved').'<br>'.diffForHumans($this->updated_at);
        } elseif ($this->status == Status::PAYMENT_SUCCESS && $this->method_code < 1000) {
            $badge = createBadge('success', 'Succeeded');
        } elseif ($this->status == Status::PAYMENT_REJECT) {
            $badge = createBadge('danger', 'Rejected').'<br>'.diffForHumans($this->updated_at);
        } else {
            $badge = createBadge('dark', 'Initiated');
        }

        return $badge;
    }

    // scope
    public function scopeGatewayCurrency()
    {
        return GatewayCurrency::where('method_code', $this->method_code)->where('currency', $this->method_currency)->first();
    }

    public function scopeBaseCurrency()
    {
        return $this->gateway->crypto == Status::ENABLE ? 'USD' : $this->method_currency;
    }

    public function scopePending($query)
    {
        return $query->where('method_code', '>=', 1000)->where('status', Status::PAYMENT_PENDING);
    }

    public function scopeRejected($query)
    {
        return $query->where('method_code', '>=', 1000)->where('status', Status::PAYMENT_REJECT);
    }

    public function scopeApproved($query)
    {
        return $query->where('method_code', '>=', 1000)->where('status', Status::PAYMENT_SUCCESS);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', Status::PAYMENT_SUCCESS);
    }

    public function scopeInitiated($query)
    {
        return $query->where('status', Status::PAYMENT_INITIATE);
    }

    public function scopeLastDays($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeSumAmount($query)
    {
        $query->selectRaw('SUM(amount) as depositAmount');
    }
}
