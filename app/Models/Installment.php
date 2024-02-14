<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Installment extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    protected $casts = [
        'given_at' => 'datetime',
        'installment_date' => 'datetime',
    ];

    public function installmentable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function saveInstallments($parent, $prevInstallment = null)
    {
        $installments = [];
        $prevInstallment = $prevInstallment ?? now();
        for ($i = 0; $i < $parent->total_installment; $i++) {
            $installment = new Installment();
            $installment->installment_date = $prevInstallment->format('Y-m-d');
            $installments[] = $installment;
            $prevInstallment->addDays($parent->installment_interval);
        }

        $parent->installments()->saveMany($installments);
    }
}
