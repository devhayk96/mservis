<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Services\Transactions\ImportHandler;
use App\Services\Transactions\TransactionImportFileManager;

/**
 * Transactions import controller.
 */
class TransactionImportController extends Controller
{
    /**
     * Check imported file.
     *
     * @return void
     */
    public function checkFile(
        Request $request,
        TransactionImportFileManager $fileManager,
        ImportHandler $importHandler
    ): View {
        try {
            $this->validateUploadedFile($request->import_file);
        } catch (Exception $e) {
            return view('partials/import-message', [
                'message' => $e->getMessage()
            ]);
        }

        $fileName = $fileManager->storeFromRequest($request->import_file);
        $transactionRows = $fileManager->getTransactionRowsOfFile($fileName);

        $importHandler->fillByFileData($transactionRows);

        return view('partials/import-prepare', [
            'fileName' => $fileName,
            'validRowsAmount' => $importHandler->countTransactionsToBeUpdated(),
            'totalRowsAmount' => count($transactionRows),
        ]);
    }

    /**
     * Handle an uploaded file.
     *
     * @return void
     */
    public function import(
        Request $request,
        TransactionImportFileManager $fileManager,
        ImportHandler $importHandler
    ): View {
        if (!$fileManager->fileExists((string) $request->file_name)) {
            return view('partials/import-message', [
                'message' => 'Файл не найден'
            ]);
        }

        $fileRows = $fileManager->getTransactionRowsOfFile($request->file_name);
        $importHandler->fillByFileData($fileRows);
        $importHandler->updateModels();

        return view('partials/import-complete');
    }

    /**
     * Validate uploaded import file.
     *
     * @param  mixed $file
     *
     * @return void
     */
    protected function validateUploadedFile($file): void
    {
        if (! ($file && $file instanceof UploadedFile)) {
            throw new Exception('Необходимо выбрать файл');
        }

        if (strtolower($file->getClientOriginalExtension()) !== 'xlsx') {
            throw new Exception('Файл должен быть в формате XLSX');
        }
    }
}
