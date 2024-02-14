<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Dps;
use App\Models\Fdr;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\MiningConfig;
use App\Models\MiningHistory;
use App\Models\MiningStack;
use App\Models\Transaction;
use Carbon\Carbon;

class CronController extends Controller
{
    public function dps()
    {
        $general = gs();
        $general->last_dps_cron = now();
        $general->save();

        $installments = Installment::where('installmentable_type', Dps::class)
            ->whereDate('installment_date', '<=', today())
            ->whereNull('given_at')
            ->whereHas('installmentable', function ($q) {
                return $q->where('status', Status::DPS_RUNNING);
            })
            ->with('installmentable')
            ->get();

        foreach ($installments as $installment) {
            $dps = $installment->installmentable;
            $amount = $dps->per_installment;
            $user = $dps->user;

            $shortCodes = $dps->shortCodes();

            if ($user->balance < $amount) {
                $lastNotification = $dps->due_notification_sent ?? now()->subHours(10);

                if ($lastNotification->diffInHours(now()) >= 10) { // Notify user after 10 hours from the previous one.
                    $shortCodes['installment_date'] = showDateTime($installment->installment_date, 'd M, Y');
                    notify($user, 'DPS_INSTALLMENT_DUE', $shortCodes);
                    $dps->due_notification_sent = now();
                    $dps->save();
                }
            } else {

                $delayedDays = $installment->installment_date->diffInDays(today());
                $charge = 0;

                if ($delayedDays >= $dps->delay_value) {
                    $charge = $dps->charge_per_installment * $delayedDays;
                    $dps->delay_charge += $charge;
                }

                $dps->given_installment += 1;

                if ($dps->given_installment == $dps->total_installment) {
                    $dps->status = Status::DPS_MATURED;
                    notify($user, 'DPS_MATURED', $shortCodes);
                }

                $dps->save();

                $user->balance -= $amount;
                $user->save();

                $installment->given_at = now();
                $installment->delay_charge = $charge;
                $installment->save();

                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->amount = $amount;
                $transaction->post_balance = $user->balance;
                $transaction->charge = 0;
                $transaction->trx_type = '-';
                $transaction->details = 'DPS installment paid';
                $transaction->remark = 'dps_installment';
                $transaction->trx = $dps->dps_number;
                $transaction->save();
            }
        }
    }

    public function loan()
    {
        $general = gs();
        $general->last_loan_cron = now();
        $general->save();

        $installments = Installment::where('installmentable_type', Loan::class)
            ->whereDate('installment_date', '<=', today())
            ->whereNull('given_at')
            ->whereHas('installmentable', function ($q) {
                return $q->where('status', Status::LOAN_RUNNING);
            })
            ->with('installmentable')
            ->get();

        foreach ($installments as $installment) {
            $loan = $installment->installmentable;
            $amount = $loan->per_installment;
            $user = $loan->user;

            $shortCodes = $loan->shortCodes();

            if ($user->balance < $amount) {
                $lastNotification = $loan->due_notification_sent ?? now()->subHours(10);

                if ($lastNotification->diffInHours(now()) >= 10) {
                    // Notify user after 10 hours from the previous one.
                    $shortCodes['installment_date'] = showDateTime($installment->installment_date, 'd M, Y');
                    notify($user, 'LOAN_INSTALLMENT_DUE', $shortCodes);
                    $loan->due_notification_sent = now();
                    $loan->save();
                }
            } else {
                $delayedDays = $installment->installment_date->diffInDays(today());
                $charge = 0;

                if ($delayedDays >= $loan->delay_value) {
                    $charge = $loan->charge_per_installment * $delayedDays;
                    $loan->delay_charge += $charge;
                }

                $loan->given_installment += 1;

                if ($loan->given_installment == $loan->total_installment) {
                    $loan->status = Status::LOAN_PAID;
                    notify($user, 'LOAN_PAID', $shortCodes);
                }

                $loan->save();

                $amount += $charge;

                $user->balance -= $amount;
                $user->save();

                $installment->given_at = now();
                $installment->delay_charge = $charge;
                $installment->save();

                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->amount = $amount;
                $transaction->post_balance = $user->balance;
                $transaction->charge = $charge;
                $transaction->trx_type = '-';
                $transaction->details = 'Loan installment paid';
                $transaction->remark = 'loan_installment';
                $transaction->trx = $loan->loan_number;
                $transaction->save();
            }
        }
    }

    public function fdr()
    {
        $general = gs();
        $now = now()->format('y-m-d');
        $general->last_fdr_cron = now();
        $general->save();

        $allFdr = Fdr::running()->whereDate('next_installment_date', '<=', $now)->with('user:id,username,balance')->get();

        foreach ($allFdr as $fdr) {
            self::payFdrInstallment($fdr);
        }
    }

    public static function payFdrInstallment($fdr)
    {
        $amount = $fdr->per_installment;
        $user = $fdr->user;
        $fdr->next_installment_date = $fdr->next_installment_date->addDays($fdr->installment_interval);
        $fdr->profit += $amount;
        $fdr->save();

        $user->balance += $amount;
        $user->save();

        $installment = new Installment();
        $installment->installment_date = $fdr->next_installment_date->subDays($fdr->installment_interval);
        $installment->given_at = now();
        $fdr->installments()->save($installment);

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '+';
        $transaction->details = 'FDR installment received';
        $transaction->remark = 'fdr_installment';
        $transaction->trx = $fdr->fdr_number;
        $transaction->save();
    }

    public function miningByDaily()
    {

        $period = $this->getPeriodTime();
        $stacks = $this->getStacksByDaily();

        if ($period->daily !== null && $stacks->daily !== null) {
            // run daily rewards
            foreach ($stacks->daily as $stack) {
                MiningHistory::create([
                    'user_id' => $stack->user_id,
                    'mining_config_id' => $period->daily->id,
                    'mining_stack_id' => $stack->id,
                    'earned' => $this->calcuatePercentage($stack->mount, $period->daily->rates),
                ]);
                $stack->updated_at = Carbon::now();
                $stack->save();
            }
        }

        return response()->json('ok');

    }

    public function miningByWeek()
    {
        $period = $this->getPeriodTime();
        $stacks = $this->getStacksByWeek();
        if ($period->weekly !== null && $stacks->weekly !== null) {
            // weekly rewards
            foreach ($stacks->weekly as $stack) {
                MiningHistory::create([
                    'user_id' => $stack->user_id,
                    'mining_config_id' => $period->weekly->id,
                    'mining_stack_id' => $stack->id,
                    'earned' => $this->calcuatePercentage($stack->mount, $period->weekly->rates),
                ]);
                $stack->updated_at = Carbon::now();
                $stack->save();
            }
        }

        return response()->json('ok');
    }

    public function miningByMonth()
    {
        $period = $this->getPeriodTime();
        $stacks = $this->getStacksByMonth();
        if ($period->monthly !== null && $stacks->monthly !== null) {
            // run monthly rewards
            foreach ($stacks->monthly as $stack) {
                MiningHistory::create([
                    'user_id' => $stack->user_id,
                    'mining_config_id' => $period->monthly->id,
                    'mining_stack_id' => $stack->id,
                    'earned' => $this->calcuatePercentage($stack->mount, $period->monthly->rates),
                ]);
                $stack->updated_at = Carbon::now();
                $stack->save();
            }
        }

        return response()->json('ok');
    }

    public function miningByYear()
    {
        $period = $this->getPeriodTime();
        $stacks = $this->getStacksByYear();

        if ($period->yearly !== null && $stacks->yearly !== null) {
            // run yearly rewards
            foreach ($stacks->yearly as $stack) {
                MiningHistory::create([
                    'user_id' => $stack->user_id,
                    'mining_config_id' => $period->yearly->id,
                    'mining_stack_id' => $stack->id,
                    'earned' => $this->calcuatePercentage($stack->mount, $period->yearly->rates),
                ]);
                $stack->updated_at = Carbon::now();
                $stack->save();
            }
        }

        return response()->json('ok');
    }

    protected function getPeriodTime()
    {
        $config = [];
        $config['daily'] = MiningConfig::where('timers', 'daily')->first();
        $config['weekly'] = MiningConfig::where('timers', 'weekly')->first();
        $config['monthly'] = MiningConfig::where('timers', 'monthly')->first();
        $config['yearly'] = MiningConfig::where('timers', 'yearly')->first();

        return (object) $config;
    }

    protected function getStacksByDaily()
    {
        $stack = [];
        $stacks = MiningStack::where('mount', '>', 0);

        $stack['daily'] = $stacks->where('updated_at', '<', Carbon::now()->subDays(1))->get();

        return (object) $stack;
    }

    protected function getStacksByWeek()
    {
        $stack = [];
        $stacks = MiningStack::where('mount', '>', 0);

        $stack['weekly'] = $stacks->whereHas('log', function ($q) {
            return $q->where('created_at', '<', Carbon::now()->subDays(7));
        })->get();

        return (object) $stack;
    }

    protected function getStacksByMonth()
    {
        $stack = [];
        $stacks = MiningStack::where('mount', '>', 0);

        $stack['monthly'] = $stacks->whereHas('log', function ($q) {
            return $q->where('created_at', '<', Carbon::now()->subDays(30));
        })->get();

        return (object) $stack;
    }

    protected function getStacksByYear()
    {
        $stack = [];
        $stacks = MiningStack::where('mount', '>', 0);

        $stack['yearly'] = $stacks->whereHas('log', function ($q) {
            return $q->where('created_at', '<', Carbon::now()->subDays(365));
        })->get();

        return (object) $stack;
    }

    protected function calcuatePercentage($mount, $percentage)
    {
        $calc = ($percentage / 100) * $mount;

        return $calc;
    }
}
