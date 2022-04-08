<?php

namespace App\Services\Transactions;

use App\Models\Transaction;
use OutOfBoundsException;
use Alcohol\ISO4217;
use App\Enums\TransactionsInternalStatusesEnum;

/**
 * Handle import of a single transaction record.
 */
class SingleRecordImportHandler
{
    /**
     * Transaction image.
     *
     * @var TransactionImage
     */
    private $transactionImage;

    /**
     * Transaction model.
     *
     * @var Transaction
     */
    private $transactionModel;

    /**
     * Show if model can be updated by an image.
     *
     * @var bool
     */
    protected $canBeUpdated = false;

    /**
     * Show if status changed.
     *
     * @var bool
     */
    protected $isStatusChanged = false;

    /**
     * Set image for a handler.
     *
     * @param TransactionImage $image
     */
    public function setImage(TransactionImage $image): void
    {
        $this->transactionImage = $image;
    }

    /**
     * Get transaction image of a handler.
     *
     * @return TransactionImage
     */
    public function getImage(): TransactionImage
    {
        return $this->transactionImage;
    }

    /**
     * Set image for a handler.
     *
     * @param TransactionImage $image
     */
    public function setModel(Transaction $model): void
    {
        $this->transactionModel = $model;
    }

    /**
     * Get transaction model of a handler.
     *
     * @return [type] [description]
     */
    public function getModel(): ?Transaction
    {
        return $this->transactionModel;
    }

    /**
     * Check if a model can be updated by an image.
     *
     * @return bool
     */
    public function checkIfModelCanBeUpdated(): bool
    {
        if (!$this->transactionModel) {
            return $this->canBeUpdated;
        }

        $this->canBeUpdated = true;

        if (
            $this->transactionModel->external_id !== $this->transactionImage->getExternalId()
            || $this->floatToPrice($this->transactionModel->amount) !== $this->floatToPrice($this->transactionImage->getAmount())
            || !$this->currencyExists()
            || !$this->isStatusValid()
            || $this->transactionModel->card_number !== $this->transactionImage->getCardNumber()
            || $this->transactionModel->date != $this->transactionImage->getDate()
        ) {
            $this->canBeUpdated = false;
        }

        return $this->canBeUpdated;
    }

    /**
     * Update a model by an image.
     *
     * @return void
     */
    public function updateModel(): void
    {
        if (!$this->canBeUpdated) {
            return;
        }

        $this->isStatusChanged = ($this->transactionImage->getStatus() !== $this->transactionModel->getStatus());

        $this->transactionModel->currency_code = $this->transactionImage->getCurrency();
        $this->transactionModel->manager_name = $this->transactionImage->getManagerName();
        $this->transactionModel->execution_date = $this->transactionImage->getExecutionDate();
        $this->transactionModel->setStatus($this->transactionImage->getStatus());
        $this->transactionModel->comment = $this->transactionImage->getComment();
        $this->transactionModel->bank_id = optional($this->transactionImage->getBank())->id;
        $this->transactionModel->save();
    }

    /**
     * Show if model status has changed.
     *
     * @return bool
     */
    public function isStatusChanged(): bool
    {
        return $this->isStatusChanged;
    }

    /**
     * Convert float to string representation of price.
     *
     * @param  float  $value
     *
     * @return string
     */
    protected function floatToPrice(float $value): string
    {
        return number_format($value, 2, '.', '');
    }

    /**
     * Check if currency exists.
     *
     * Based on the ISO 4217.
     *
     * @return bool
     */
    protected function currencyExists(): bool
    {
        try {
            (new ISO4217())->getByCode($this->transactionImage->getCurrency());
        } catch (OutOfBoundsException $e) {
            return false;
        }

        return true;
    }

    /**
     * Check if status can be applied.
     *
     * @return bool
     */
    protected function isStatusValid(): bool
    {
        return TransactionsInternalStatusesEnum::isKnownStatus($this->transactionImage->getStatus());
    }
}
