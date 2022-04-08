<?php

namespace App\Scopes;

use App\Enums\TransactionsInternalStatusesEnum;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Http\Request;

class TransactionsRegistryScope implements Scope
{
    /**
     * Amount from.
     *
     * @var float|null
     */
    protected $amountFrom;

    /**
     * Amount to
     *
     * @var float|null
     */
    protected $amountTo;

    /**
     * Date from.
     *
     * @var Carbon
     */
    protected $dateFrom;

    /**
     * Date to.
     *
     * @var Carbon
     */
    protected $dateTo;

    /**
     * Status.
     *
     * @var int
     */
    protected $status;

    /**
     * Searching string.
     *
     * @var string
     */
    protected $search;

    /**
     * Manager string.
     *
     * @var string
     */
    protected $manager;

    /**
     * Sort direction.
     *
     * @var string
     */
    protected $direction = 'desc';

    /**
     * Sort column.
     *
     * @var string
     */
    protected $column = 'date';

    /**
     * Constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->amountFrom = $request->amount_from;
        $this->amountTo = $request->amount_to;
        $this->dateFrom = $this->parseDate($request->date_from);
        $this->dateTo = $this->parseDate($request->date_to);
        $this->status = $this->sanitizeStatus($request->status);
        $this->manager = $request->manager;
        $this->search = (string) $request->search;
        $this->direction = $request->direction ?? $this->direction;
        $this->column = $request->column ?? $this->column;
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder
            ->when($this->status, function (Builder $query) {
                $query->where('status', $this->status);
            })
            ->when($this->dateFrom, function (Builder $query) {
                $query->where('date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function (Builder $query) {
                $query->where('date', '<=', $this->dateTo->endOfDay());
            })
            ->when($this->amountFrom, function (Builder $query) {
                $query->where('amount', '>=', $this->amountFrom);
            })
            ->when($this->amountTo, function (Builder $query) {
                $query->where('amount', '<=', $this->amountTo);
            })
            ->when($this->search, function (Builder $query) {
                $query
                    ->where('card_number', $this->search)
                    ->orWhere('external_id', $this->search);
            })
            ->when($this->column, function (Builder $query) {
                $query->orderBy($this->column, $this->direction);
            })
            ->whereHas('processing', function (Builder $processingQuery) {
                $processingQuery->where('is_processing', true);
            })
            ->when($this->status, function (Builder $query) {
                $query->where(['status' => $this->status]);
            });

        if (request()->has('manager') && request()->get('manager') == '') {
            $builder->where('manager_name', '');
        }
    }

    /**
     * Sanitize incoming status.
     *
     * @param  string $status
     *
     * @return int|null
     */
    protected function sanitizeStatus(?string $status): ?int
    {
        $statusInt = (int) $status;
        $statuses_count = count(TransactionsInternalStatusesEnum::all());

        return ($statusInt > 0 && $statusInt < $statuses_count)
            ? $statusInt
            : null;
    }

    /**
     * Parse date string.
     *
     * @param  string $dateStr
     *
     * @return Carbon|null
     */
    protected function parseDate(?string $dateStr): ?Carbon
    {
        if ($dateStr) {
            try {
                return Carbon::parse($dateStr);
            } catch (Exception $e) {
                return null;
            }
        }

        return null;
    }
}
