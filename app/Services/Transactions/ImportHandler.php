<?php

namespace App\Services\Transactions;

use App\Models\Transaction;

class ImportHandler
{
    /**
     * Single record import handlers.
     *
     * @var \Illuminate\Support\Collection[SingleRecordImportHandler]
     */
    private $recordHandlers;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->webhookDispatcher = new TransactionWebhookJobDispatcher();
        $this->recordHandlers = collect();
    }

    /**
     * Create collection of the transaction images by data from CSV file.
     *
     * @param array $rows
     */
    public function fillByFileData(array $rows): void
    {
        foreach ($rows as $row) {
            $transactionRow = TransactionImageFactory::createFromAdminExportFileRecord($row);

            $recordHandler = new SingleRecordImportHandler();
            $recordHandler->setImage($transactionRow);

            $this->recordHandlers->put($transactionRow->getId(), $recordHandler);
        }
    }

    /**
     * Count transactions that can be updated.
     *
     * @return int
     */
    public function countTransactionsToBeUpdated(): int
    {
        $this->populateRecordHandlersByModels();

        return $this->recordHandlers->filter(function (SingleRecordImportHandler $item) {
            return $item->checkIfModelCanBeUpdated();
        })->count();
    }

    /**
     * Update models of the transaction images.
     *
     * @return void
     */
    public function updateModels(): void
    {
        $this->populateRecordHandlersByModels();

        $this->recordHandlers->each(function (SingleRecordImportHandler $item) {
            if ($item->checkIfModelCanBeUpdated()) {
                $item->updateModel();

                if ($item->isStatusChanged()) {
                    $this->webhookDispatcher->addNotificationFor($item->getModel());
                }
            }
        });

        $this->webhookDispatcher->dispatch();
    }

    /**
     * Pupulate transaction images by their models.
     *
     * @return void
     */
    protected function populateRecordHandlersByModels(): void
    {
        Transaction::whereIn('id', $this->recordHandlers->keys()->toArray())->get()->each(function (Transaction $item) {
            if ($this->recordHandlers->has($item->id)) {
                $this->recordHandlers->get($item->id)->setModel($item);
            }
        });
    }
}
