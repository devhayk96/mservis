<?php

namespace App\Services\Transactions;

use Str;
use Illuminate\Http\UploadedFile;
use App\Imports\TransactionsImport;

/**
 * Help to work with the import files.
 */
class TransactionImportFileManager
{
    /**
     * Folder that contains uploaded import files.
     */
    protected const STORAGE_APP_FOLDER = 'import-files';

    /**
     * Return path of folder with import files.
     *
     * @return string
     */
    public static function getFilesFolderPat(): string
    {
        return storage_path('app') . '/' . self::STORAGE_APP_FOLDER;
    }

    /**
     * Store an uploaded import file.
     *
     * @param  UploadedFile $uploadedFile
     *
     * @return string                     Name of a stored file.
     */
    public function storeFromRequest(UploadedFile $uploadedFile): string
    {
        $fileName = $this->getFileName();
        $uploadedFile->storeAs(self::STORAGE_APP_FOLDER, $fileName);
        return $fileName;
    }

    /**
     * Return contents of a file.
     *
     * @param  string $fileName
     *
     * @return array
     */
    public function getTransactionRowsOfFile(string $fileName): array
    {
        $fileRows = (new TransactionsImport())->toArray($this->getImportFilePath($fileName))[0];
        return array_slice($fileRows, 1);
    }

    /**
     * Return path to the import file.
     *
     * @param  string $fileName
     *
     * @return string
     */
    public function getImportFilePath(string $fileName): string
    {
        return sprintf('%s/%s', self::getFilesFolderPat(), $fileName);
    }

    /**
     * Check a file existence.
     *
     * @param  string $fileName
     *
     * @return bool
     */
    public function fileExists(string $fileName): bool
    {
        if (!$fileName) {
            return false;
        }

        return file_exists($this->getImportFilePath($fileName));
    }

    /**
     * Return name for the stored file.
     *
     * @return string
     */
    protected function getFileName(): string
    {
        return Str::random(20) . '.xlsx';
    }
}
