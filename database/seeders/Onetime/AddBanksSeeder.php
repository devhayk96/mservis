<?php

namespace Database\Seeders\Onetime;

use App\Imports\BanksImport;
use App\Models\Bank;
use Illuminate\Database\Seeder;
use App\Enums\ImportBanksColumnsEnum as ColumnsEnum;

class AddBanksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banksFile = public_path('banks/banks.xlsx');

        if (file_exists($banksFile)) {
            $fileRows = $this->getBanksRowsOfFile($banksFile);
            $this->fillByFileData($fileRows);
        }
    }

    /**
     * @param string $banksFile
     * @return array
     */
    private function getBanksRowsOfFile(string $banksFile): array
    {
        $fileRows = (new BanksImport())->toArray($banksFile)[0];
        return array_slice($fileRows, 1);
    }

    private function fillByFileData($rows): void
    {
        $banksData = [];

        foreach ($rows as $row) {
            $data = $this->fillData($row);
            $banksData[] = $data;
        }

        $banksData = collect($banksData);
        $chunks = $banksData->chunk(20);

        foreach ($chunks as $chunk)
        {
            Bank::query()->insert($chunk->toArray());
        }
    }

    /**
     * @param array $row
     * @return string[]
     */
    public function fillData(array $row): array
    {
        return [
            'bin' => (string) $row[ColumnsEnum::COLUMN_BIN],
            'bank' => (string) $row[ColumnsEnum::COLUMN_BANK],
            'system' => (string) $row[ColumnsEnum::COLUMN_SYSTEM],
            'type' => (string) $row[ColumnsEnum::COLUMN_TYPE],
            'level' => (string) $row[ColumnsEnum::COLUMN_LEVEL],
            'geo' => (string) $row[ColumnsEnum::COLUMN_GEO],
        ];
    }
}
