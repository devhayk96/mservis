<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Services\Transactions\TransactionImage;
use App\Services\Transactions\TransactionImageFactory;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * An CSV export handler for transactions.
 */
class TransactionsExportCSV implements FromQuery, Responsable, WithHeadings, WithMapping
{
    use Exportable;

    /**
     * Name of the file.
     *
     * @var string
     */
    private $fileName = 'transactions.csv';

    /**
     * Type of the writer.
     *
     * @var string
     */
    private $writerType = Excel::CSV;

    /**
     * Headers.
     *
     * @var array
     */
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    /**
     * Earliest date in the file.
     *
     * @var string
     */
    private $dateFrom;

    /**
     * Latest date in the file.
     *
     * @var string
     */
    private $dateTo;

    /**
     * Constructor
     *
     * @param int $year [description]
     */
    public function __construct(?string $dateFrom, ?string $dateTo)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    /**
     * Prepare a transactions query.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        // dd($this->dateFrom, $this->dateTo);
        return Transaction::query()
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->with('merchant', 'processing');
    }

    /**
     * Map transactions before sending them to a table.
     *
     * @param  Transaction $transaction
     *
     * @return array
     */
    public function map($transaction): array
    {
        $image = TransactionImageFactory::createFromModel($transaction);
        return $image->getRowArray();
    }

    /**
     * Names of the columns.
     *
     * @return array
     */
    public function headings(): array
    {
        return TransactionImage::getHeadingNames();
    }
}
