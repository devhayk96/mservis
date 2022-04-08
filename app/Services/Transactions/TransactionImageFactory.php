<?php

namespace App\Services\Transactions;

use App\Models\Transaction;
use App\Services\Presenters\AdminExcelPresenter as ColumnsEnum;

/**
 * TransactionImage factory.
 */
class TransactionImageFactory
{
    /**
     * Create a TransactionImage by a given Transaction model.
     *
     * @param  Transaction $model
     *
     * @return TransactionImage
     */
    public static function createFromModel(Transaction $model): TransactionImage
    {
        $image = new TransactionImage();

        $image->setId($model->id);
        $image->setDate($model->date ?: $model->created_at->addHours(3));
        $image->setMerchant($model->merchant);
        $image->setCardNumber($model->card_number);
        $image->setAmount($model->amount);
        $image->setCurrency($model->currency_code);
        $image->setManagerName($model->manager_name);
        $image->setExternalId($model->external_id);
        $image->setExecutionDate($model->execution_date);
        $image->setStatus($model->getStatus());
        $image->setComment($model->comment);
        $image->setCreatedAt($model->created_at);
        $image->setBank($model->bank);
        $image->setIsProcessing($model->isProcessing());
        $image->setProcessingOperatorName($model->getProcessingOperator());
        $image->setStatusDescription($model->getProcessingComment());

        return $image;
    }

    /**
     * Create a TransactionImage by values from admin export file.
     *
     * @param  array  $values
     *
     * @return TransactionImage
     */
    public static function createFromAdminExportFileRecord(array $values): TransactionImage
    {
        $helper = new TransactionImageFactoryHelper();
        $image = new TransactionImage();

        $image->setId((int) data_get($values, ColumnsEnum::COLUMN_ID));
        $image->setDate($helper->parseDate((string) data_get($values, ColumnsEnum::COLUMN_DATE)));
        $image->setMerchant($helper->getMerchantByName((string) data_get($values, ColumnsEnum::COLUMN_MERCHANT)));
        $image->setCardNumber($helper->normalizeCardNumber((string) data_get($values, ColumnsEnum::COLUMN_CARD_NUMBER)));
        $image->setAmount($helper->normalizeAmount(data_get($values, ColumnsEnum::COLUMN_AMOUNT)));
        $image->setCurrency((string) data_get($values, ColumnsEnum::COLUMN_CURRENCY));
        $image->setManagerName((string) data_get($values, ColumnsEnum::COLUMN_MANAGER_NAME));
        $image->setExternalId((string) data_get($values, ColumnsEnum::COLUMN_TRANSACTION_ID));
        $image->setExecutionDate($helper->parseDate((string) data_get($values, ColumnsEnum::COLUMN_EXECUTION_DATE)));
        $image->setComment((string) data_get($values, ColumnsEnum::COLUMN_COMMENT));
        $image->setCreatedAt($helper->parseDate((string) data_get($values, ColumnsEnum::COLUMN_CREATED_AT)));
        $image->setBank($helper->getBankByCardNumber((string) data_get($values, ColumnsEnum::COLUMN_CARD_NUMBER)));

        $status = (string) data_get($values, ColumnsEnum::COLUMN_STATUS);
        $image->setIsProcessing($status === 'Send supplier');
        $image->setStatus($status === 'Send supplier' ? 'send_supplier' : $status);

        return $image;
    }
}
