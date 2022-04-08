<?php

namespace App\Services\Transactions;

use App\Models\Bank;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Models\Merchant;

/**
 * Image (virtual representation) of a transaction.
 */
class TransactionImage
{
    /**
     * Transaction ID (primary key from our database).
     *
     * @var int
     */
    private $id;

    /**
     * Date of a transaction.
     *
     * @var Carbon|null
     */
    private $date;

    /**
     * Transaction merchant.
     *
     * @var Merchant|null
     */
    private $merchant;

    /**
     * Transaction card number.
     *
     * @var string
     */
    private $cardNumber;

    /**
     * Transaction amount.
     *
     * Amount of money.
     *
     * @var float
     */
    private $amount;

    /**
     * Currency code of amount.
     *
     * According to ISO 4217.
     *
     * @var float
     */
    private $currency;

    /**
     * Name of a manager who handled a transaction.
     *
     * @var float
     */
    private $managerName;

    /**
     * Transaction ID (ID from a merchant's system).
     *
     * @var string
     */
    private $externalId;

    /**
     * Date when transaction was executed by a manager.
     *
     * @var Carbon|null
     */
    private $executionDate;

    /**
     * Status of a transaction.
     *
     * @var string
     */
    private $status;

    /**
     * Comment to a transaction.
     *
     * @var string
     */
    private $comment;

    /**
     * Time when transaction record was created in our database.
     *
     * @var Carbon
     */
    private $createdAt;

    /**
     * Bank of a card.
     *
     * @var Bank
     */
    private $bank;

    /**
     * Show if transaction in processing.
     *
     * @var bool
     */
    private $isProcessing = false;

    /**
     * Name of processing operator.
     *
     * @var string
     */
    private $processingOperator = '';

    /**
     * Status description.
     *
     * @var string
     */
    private $statusDescription = '';

    /**
     * Model.
     *
     * @var Transaction|null
     */
    protected $model;

    /**
     * Headings for a CSV file.
     *
     * @return array
     */
    public static function getHeadingNames(): array
    {
        return [
            'Internal id',
            'Date and time',
            'Merchant',
            'Card number',
            'Summ',
            'Currency',
            'Executor',
            'Transaction id',
            'Due Date Transaction',
            'Status',
            'Comment',
            'Transaction Created at',
            'Bank',
        ];
    }

    /**
     * Return data of a transaction for putting in a table.
     *
     * @return array
     */
    public function getRowArray(): array
    {
        return [
            $this->getId(),
            $this->getDateFormatted('d.m.Y H:i:s'),
            (string) optional($this->getMerchant())->name,
            $this->getCardNumber(),
            $this->getAmount(),
            $this->getCurrency(),
            $this->getManagerName(),
            $this->getExternalId(),
            $this->getExecutionDateFormatted(),
            $this->getStatus(),
            $this->getComment(),
            $this->getCreatedAt(),
            $this->getBankName()
        ];
    }

    /**
     * Set ID of a transaction.
     *
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get ID of a transaction.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set date of a transaction.
     *
     * @param Carbon $date
     */
    public function setDate(?Carbon $date): void
    {
        $this->date = $date;
    }

    /**
     * Get date of a transaction.
     *
     * @return Carbon
     */
    public function getDate(): ?Carbon
    {
        return $this->date;
    }

    /**
     * Get transaction formatted date.
     *
     * @return string
     */
    public function getDateFormatted($format): string
    {
        return (string) optional($this->getDate())->format($format);
    }

    /**
     * Set transaction merchant.
     *
     * @param Merchant $merchant
     */
    public function setMerchant(?Merchant $merchant): void
    {
        $this->merchant = $merchant;
    }

    /**
     * Get transaction merchant.
     *
     * @return Merchant
     */
    public function getMerchant(): ?Merchant
    {
        return $this->merchant;
    }

    /**
     * Set transaction card number.
     *
     * @param string $cardNumber
     */
    public function setCardNumber(string $cardNumber): void
    {
        $this->cardNumber = $cardNumber;
    }

    /**
     * Get transaction card number.
     *
     * @return string
     */
    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    /**
     * Get transaction formatted card number.
     *
     * @return string
     */
    public function getCardNumberFormatted(): string
    {
        return substr_replace($this->cardNumber, str_repeat('*', 8), 4, 8);
    }

    /**
     * Set transaction amount.
     *
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * Get transaction amount.
     *
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }


    /**
     * Get transaction formatted amount.
     *
     * @return float
     */
    public function getFormattedAmount()
    {
        return number_format($this->getAmount(), 2, ',', ' ');
    }

    /**
     * Set currency of transaction amount.
     *
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * Get currency of transaction amount.
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Set name of a manager.
     *
     * @param string $name
     */
    public function setManagerName(string $name): void
    {
        $this->managerName = $name;
    }

    /**
     * Get name of a manager.
     *
     * @return string
     */
    public function getManagerName(): string
    {
        return $this->managerName;
    }

    /**
     * Set external transaction ID.
     *
     * @param string $id
     */
    public function setExternalId(string $id): void
    {
        $this->externalId = $id;
    }

    /**
     * Get external transaction ID.
     *
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    /**
     * Set execution date.
     *
     * @param Carbon|null $date
     */
    public function setExecutionDate(?Carbon $date): void
    {
        $this->executionDate = $date;
    }

    /**
     * Get execution date.
     *
     * @return Carbon|null
     */
    public function getExecutionDate(): ?Carbon
    {
        return $this->executionDate;
    }

    /**
     * Get transaction formatted date.
     *
     * @return string
     */
    public function getExecutionDateFormatted(): string
    {
        return (string) optional($this->getExecutionDate())->format('d.m.Y H:i:s');
    }

    /**
     * Set status.
     *
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set comment.
     *
     * @param ?string $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * Get comment.
     *
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Set created at.
     *
     * @param ?Carbon $date
     */
    public function setCreatedAt(?Carbon $date): void
    {
        $this->createdAt = $date;
    }

    /**
     * Get created at.
     *
     * @return Carbon|null
     */
    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    /**
     * Get formatted created at.
     *
     * @return string|null
     */
    public function getFormattedCreatedAt()
    {
        return $this->getCreatedAt() ? $this->getCreatedAt()->format('d.m.Y H:i:s') : null;
    }

    /**
     * Set bank of a card.
     *
     * @param Bank $merchant
     */
    public function setBank(?Bank $bank): void
    {
        $this->bank = $bank;
    }

    /**
     * Get bank of a card.
     *
     * @return Bank
     */
    public function getBank(): ?Bank
    {
        return $this->bank;
    }

    /**
     * Get bank name of a card.
     *
     * @return string
     */
    public function getBankName(): string
    {
        return (string) optional($this->getBank())->bank;
    }

    /**
     * Set whether transaction is in processing.
     *
     * @param bool $status
     */
    public function setIsProcessing(bool $status): void
    {
        $this->isProcessing = $status;
    }

    /**
     * Show whether transaction is in processing.
     *
     * @return bool
     */
    public function isProcessing(): bool
    {
        return $this->isProcessing;
    }

    /**
     * Set name of processing operator.
     *
     * @param string $name
     */
    public function setProcessingOperatorName(string $name): void
    {
        $this->processingOperator = $name;
    }

    /**
     * Get name of processing operator.
     *
     * @return string
     */
    public function getProcessingOperatorName(): string
    {
        return $this->processingOperator;
    }

    /**
     * Set status description.
     *
     * @param string $name
     */
    public function setStatusDescription(string $description): void
    {
        $this->statusDescription = $description;
    }

    /**
     * Get status description.
     *
     * @return string
     */
    public function getStatusDescription(): string
    {
        return $this->statusDescription;
    }

    /**
     * Return status for admin.
     *
     * @return string
     */
    public function getStatusForAdmin(): string
    {
        return $this->isProcessing()
            ? 'Send supplier'
            : $this->getStatus();
    }

    /**
     * Set model.
     *
     * @param Transaction $model
     */
    public function setModel(Transaction $model): void
    {
        $this->model = $model;
    }

    /**
     * Get model.
     *
     * @param Transaction $model
     */
    public function getModel(): ?Transaction
    {
        return $this->model;
    }
}
