<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Constants\Status;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use GlobalStatus, Searchable;

    public function assignStaff(): BelongsToMany
    {
        return $this->belongsToMany(BranchStaff::class, 'assign_branch_staff', 'branch_id', 'staff_id');
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class, 'branch_id')->where('status', Status::PAYMENT_SUCCESS);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class, 'branch_id')->where('status', Status::PAYMENT_SUCCESS);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
