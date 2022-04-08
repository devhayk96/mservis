<?php

namespace App\Scopes;

use App\Enums\TransactionsInternalStatusesEnum;
use App\Services\Presenters\Presenter;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class BaseTransactionsRegistryScope
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
        $this->amountFrom = $request->get('amount_from');
        $this->amountTo = $request->get('amount_to');
        $this->dateFrom = $this->parseDate($request->get('date_from'));
        $this->dateTo = $this->parseDate($request->get('date_to'));
        $this->status = $this->sanitizeStatus($request->get('status'));
        $this->search = (string) $request->get('search');
        $this->direction = $request->get('direction') ?? $this->direction;
        $this->column = $request->get('column') ?? $this->column;
    }

    /**
     * Sanitize incoming status.
     *
     * @param ?string $status
     *
     * @return int|null
     */
    protected function sanitizeStatus(?string $status): ?int
    {
        $statusInt = (int) $status;
        $statuses_count = count(Presenter::getTransactionsStatuses());

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
