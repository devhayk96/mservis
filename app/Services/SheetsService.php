<?php

namespace App\Services;

use App\Models\GoogleDrivePublicFolder;
use App\Models\GoogleSheet;
use App\Models\Transaction;
use Carbon\Carbon;
use Google\Client;
use Google_Service_Sheets;
use Google_Service_Drive;
use Google_Service_Drive_Permission;
use Google_Service_Drive_DriveFile;
use Google_Service_Sheets_ValueRange;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;

/**
 * Handle writing to Google Sheets.
 *
 * @deprecated 1.6.1 No need to use this service anymore.
 */
class SheetsService
{
    /**
     * Google client instance.
     *
     * @var Client
     */
    protected $googleClient;

    /**
     * Drive service.
     *
     * @var Google_Service_Drive
     */
    protected $googleDriveService;

    /**
     * Sheets service.
     *
     * @var Google_Service_Sheets
     */
    protected $googleSheetService;

    /**
     * ID of a Google Drive folder.
     *
     * @var string
     */
    protected $folderId;

    /**
     * ID of a Google Sheet spreadsheet.
     *
     * @var string
     */
    protected $spreadsheetId;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->initGoogleClient();

        $this->googleDriveService = new Google_Service_Drive($this->googleClient);
        $this->setFolderId();

        $this->googleSheetService = new Google_Service_Sheets($this->googleClient);
        $this->setSpreadsheetId();
    }


    /**
     * Add record to the spreadsheet.
     *
     * @param Transaction $transaction
     */
    public function addRecord(Transaction $transaction): void
    {
        $this->addRowToSpreadsheet([
            $transaction->date->format('d-m-Y H:i:s'),
            $transaction->amount,
            $transaction->card_number,
            $transaction->external_id,
        ]);
    }

    /**
     * Initiate Google clinet.
     *
     * @return void
     */
    protected function initGoogleClient(): void
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . base_path('mservis.json'));
        $this->googleClient = new Client();
        $this->googleClient->useApplicationDefaultCredentials();
        $this->googleClient->setScopes([\Google_Service_Sheets::SPREADSHEETS, \Google_Service_Sheets::DRIVE]);
    }

    /**
     * Set ID of the Drive folder.
     *
     * Creates new if it not exists.
     *
     * @return void
     */
    protected function setFolderId(): void
    {
        $this->folderId = optional(GoogleDrivePublicFolder::first())->folder_id ?? $this->creatFolder();
    }

    /**
     * Create Drive folder.
     *
     * @return string Folder ID.
     */
    protected function creatFolder(): string
    {
        $file = new Google_Service_Drive_DriveFile();
        $file->setName('Transactions');
        $file->setMimeType('application/vnd.google-apps.folder');
        $folder = $this->googleDriveService->files->create($file);

        GoogleDrivePublicFolder::create([
            'folder_id' => $folder->id
        ]);

        $this->addPermissions($folder->id);

        logger(sprintf('Created folder %s. Get Access at https://drive.google.com/drive/folders/%s', $folder->id, $folder->id));
        return $folder->id;
    }

    /**
     * Set ID of the Sheet spreadsheet.
     *
     * Creates new if it not exists.
     *
     * @return void
     */
    protected function setSpreadsheetId()
    {
        $todaysFile = GoogleSheet::whereDate('created_at', Carbon::today()->format('Y-m-d'))
            ->first();

        if ($todaysFile) {
            $this->spreadsheetId = $todaysFile->sheet_id;
        } else {
            $this->spreadsheetId = $this->creatSpreadsheet();

            $this->addRowToSpreadsheet([
                'Дата заявки',
                'Сумма',
                'Номер карты',
                'ID транзакции',
            ]);
        }
    }

    /**
     * Create Drive folder.
     *
     * @return string Folder ID.
     */
    protected function creatSpreadsheet()
    {
        $file = new Google_Service_Drive_DriveFile();
        $file->setMimeType('application/vnd.google-apps.spreadsheet');
        $file->setName(Carbon::today()->format('Y-m-d'));
        $file->setParents([$this->folderId]);

        $file = $this->googleDriveService->files->create($file);
        $fileId = $file->getId();

        GoogleSheet::create([
            'sheet_id' => $fileId
        ]);

        $this->addPermissions($fileId);

        logger(sprintf('Created sheet %s. Get Access at https://docs.google.com/spreadsheets/d/%s', $fileId, $fileId));
        return $fileId;
    }

    /**
     * Add permission to entity.
     *
     * @param string $id Entity ID.
     */
    protected function addPermissions(string $entityId): void
    {
        if ($emailsStr = config('google.allowed_google_users')) {
            foreach (explode(' ', $emailsStr) as $email) {
                $drivePermisson = new Google_Service_Drive_Permission();
                $drivePermisson->setType('user');
                $drivePermisson->setEmailAddress(trim($email));
                $drivePermisson->setRole('reader');
                $this->googleDriveService->permissions->create($entityId, $drivePermisson);
            }
        } else {
            $drivePermisson = new Google_Service_Drive_Permission();
            $drivePermisson->setType('anyone');
            $drivePermisson->setRole('reader');
            $this->googleDriveService->permissions->create($entityId, $drivePermisson);
        }
    }

    /**
     * Add row to the sheet.
     *
     * @param array $rowValues
     */
    protected function addRowToSpreadsheet(array $rowValues): void
    {
        $values = [$rowValues];
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $params = [
            'valueInputOption' => 'RAW'
        ];
        $range = 'Sheet1!A:D';

        $this->googleSheetService->spreadsheets_values->append($this->spreadsheetId, $range, $body, $params);

        $this->updateColumWidths();
    }

    /**
     * Update with of the sheet columns.
     *
     * @return void
     */
    protected function updateColumWidths(): void
    {
        $requestBody = [
            'requests' => [
                'autoResizeDimensions' => [
                    'dimensions' => [
                        "sheetId" => 0,
                        "dimension" => "COLUMNS",
                        "startIndex" => 0,
                        "endIndex" => 4
                    ]
                ]
            ]
        ];

        $request = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest($requestBody);
        $this->googleSheetService->spreadsheets->batchUpdate($this->spreadsheetId, $request);
    }
}
