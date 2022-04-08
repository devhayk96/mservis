<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Scopes\TransactionsRegistryAdminScope;
use App\Scopes\TransactionsRegistryMerchantScope;
use App\Services\Presenters\Presenter;
use App\Services\Presenters\AdminExcelPresenter;
use App\Services\Presenters\MerchantExcelPresenter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Storage;
use App\Services\Transactions\TransactionImageFactory;

/**
 * An Excel export handler for transactions.
 */
class TransactionsExportExcel implements Responsable
{
    /**
     * Name of the file.
     *
     * @var string
     */
    private $fileName;

    /**
     * Headers.
     *
     * @var array
     */
    private $headers = [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    /**
     * TransactionsExportExcel constructor.
     * @param array $request
     */
    public function __construct(array $request)
    {
        $this->setFileName();
    }

    /**
     * Return name of an export file.
     */
    protected function setFileName(): void
    {
        $this->fileName = sprintf('transactions_%s.xlsx', now()->timestamp);
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $this->createExportFile();

        return response()->download(
            $this->getFilePath(),
            $this->fileName,
            $this->headers
        )->deleteFileAfterSend(true);
    }

    /**
     * Create export file.
     *
     * @return void
     */
    protected function createExportFile(): void
    {
        $writer = $this->getWriter($this->getSpreadsheet());
        $writer->save($this->getFilePath());
    }

    /**
     * Return spreadsheet object.
     *
     * @return Spreadsheet
     */
    protected function getSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $activeSheet = $spreadsheet->getActiveSheet();
        $transactions = $this->getTransactionsData();

        $this->populateSheet($activeSheet, $transactions);

        return $spreadsheet;
    }

    /**
     * Populate sheet with data.
     *
     * @param  Worksheet $activeSheet
     * @param  array     $transactions
     *
     * @return void
     */
    protected function populateSheet(Worksheet $activeSheet, array $transactions): void
    {
        $presenter = $this->getPresenter();
        $sheet_data = array_merge([array_values($presenter->getColumnNames())], $transactions);

        foreach ($sheet_data as $rowKey => $transactionRow) {
            $row = ($rowKey + 1);

            foreach ($transactionRow as $cellKey => $cellValue) {
                $column = $presenter->getLetterIndexByIntIndex($cellKey);
                $cellNum = $column . $row;

                if (in_array($column, $presenter->getColumnsOfExplicitStringType())) {
                    $activeSheet->setCellValueExplicit($cellNum, $cellValue, DataType::TYPE_STRING);
                } else {
                    $activeSheet->setCellValue($cellNum, $cellValue);
                }
            }
        }
    }

    /**
     * Return transactions data
     *
     * @return array
     */
    protected function getTransactionsData(): array
    {
        $presenter = $this->getPresenter();
        $presenterScope = $this->getPresenterScope();

        $transactions = Transaction::query()
            ->withGlobalScope($presenterScope, new $presenterScope(request()))
            ->with('processing');

        return $transactions->get()
            ->map(function (Transaction $transaction) use ($presenter) {
                $image = TransactionImageFactory::createFromModel($transaction);
                $presenter->setModel($image);

                return array_values($presenter->getValues());
            })->toArray();
    }

    /**
     * Return path to export file.
     *
     * @return string
     */
    protected function getFilePath(): string
    {
        Storage::makeDirectory('export-files');
        return storage_path('app/export-files/' . $this->fileName);
    }

    /**
     * Return writer for a file generation.
     *
     * @param  Spreadsheet $spreadsheet
     *
     * @return Xlsx
     */
    protected function getWriter(Spreadsheet $spreadsheet): Xlsx
    {
        return new Xlsx($spreadsheet);
    }

    /**
     * Return presenter of registry.
     *
     * @return Presenter
     */
    protected function getPresenter(): Presenter
    {
        return auth()->user()->isAdmin()
            ? new AdminExcelPresenter()
            : new MerchantExcelPresenter();
    }

    /**
     * Return presenter scope of registry.
     *
     * @return string
     */
    protected function getPresenterScope(): string
    {
        return auth()->user()->isAdmin()
            ? TransactionsRegistryAdminScope::class
            : TransactionsRegistryMerchantScope::class;
    }
}
