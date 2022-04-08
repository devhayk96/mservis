<?php

namespace App\Http\Controllers\API;

use Illuminate\Routing\Controller;
use App\Models\GoogleDrivePublicFolder;
use Illuminate\Http\Request;


class GoogleDrivePublicFoldersController extends Controller
{
    public function showFolder()
    {
        $folderModel = GoogleDrivePublicFolder::first();

        if ($folderModel) {
            return [
                'status' => 'ok',
                'folder_url' => $this->getFolderUrl($folderModel)
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Folder doesn\'t exist: no transactions has been provided.'
            ];
        }
    }

    protected function getFolderUrl(GoogleDrivePublicFolder $model)
    {
        return sprintf('https://drive.google.com/drive/folders/%s', $model->folder_id);
    }
}
