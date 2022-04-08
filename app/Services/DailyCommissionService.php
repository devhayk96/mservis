<?php

namespace App\Services;

use App\Models\DailyCommission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Daily commissions service.
 */
class DailyCommissionService
{
    /**
     * Set commission for merchant.
     *
     * @param int    $merchantId
     * @param Carbon $date
     * @param float  $commissionAmount
     */
    public function setCommissionForMerchant(int $merchantId, Carbon $date, float $commissionAmount): void
    {
        $commission = DailyCommission::whereDate('date', $date)
            ->where('merchant_id', $merchantId)
            ->first();

        if ($commission) {
            if ($date->isToday()) {
                $date->addDay();
            }

            DailyCommission::updateOrCreate([
                'date' => $date,
                'merchant_id' => $merchantId,
            ], [
                'amount' => $commissionAmount,
                'type' => DailyCommission::TYPE_PERCENT
            ]);
        } elseif ($date->greaterThan(now()->endOfDay()->subDay())) {
            DailyCommission::create([
                'date' => $date,
                'merchant_id' => $merchantId,
                'amount' => $commissionAmount,
                'type' => DailyCommission::TYPE_PERCENT
            ]);
        }
    }

    /**
     * Get merchant commission for today.
     *
     * @return DailyCommission
     */
    public function getCommissionByMerchantId(int $merchantId): DailyCommission
    {
        $commissions = DailyCommission::query()
            ->where('merchant_id', $merchantId)
            ->where(function (Builder $query) use ($merchantId) {
                $query
                    ->whereDate('date', now())
                    ->orWhereRaw('date = (SELECT MAX(date) FROM `daily_commissions` where merchant_id = ?)', [$merchantId]);
            })
            ->get();

        if ($commissions->count() > 0) {
            $todayCommission = $commissions->filter(function ($value, $key) {
                return $value->date->format('Y-m-d') === now()->format('Y-m-d');
            })->first();
            $lastCommission = $commissions->where('date', ($commissions->max('date')))->first();

            $commission = $todayCommission ?: $lastCommission;
        } else {
            $commission = new DailyCommission();
            $commission->populateFallbackCommission();
        }

        return $commission;
    }
}
