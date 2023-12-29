<?php

namespace App\Models;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class UploadFile extends Model
{
    public $table = 'folder_file';

    public $fillable = [
        'sub_folder_id', 'file', 'description', 'file_name', 'file_path', 'file_type', 'user_name', 'creation_on', 'status', 'deleted'
    ];

    public static function UploadFileData($sName, $sDescription, $iFileType, $sUserName, $iSubFolderId, $sUrlPath, $originalFileName)
    {
        $dDate = date('Y-m-d H:i:s');

        $data = [
            'sub_folder_id' => $iSubFolderId,
            'file' => $sName,
            'description' => $sDescription,
            'file_name' => $originalFileName,
            'file_path' => $sUrlPath, // You may want to adjust this based on your file storage configuration
            'file_type' => $iFileType,
            'user_name' => $sUserName,
            'creation_on' => $dDate
        ];

        try {
            return self::create($data);
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to create folder: " . $e->getMessage());
        }
    }

    public static function fetchAll($sName, $iSubFolderId, $dFromDate, $dToDate, $iLimit, $iPage)
    {
        try {
            $query = self::query()->where('status',1)->where('deleted',0);

            if ($sName !== '') {
                $query->where('file', 'like', '%' . $sName . '%');
            }

            if ($iSubFolderId !== '') {
                $query->where('sub_folder_id', $iSubFolderId);
            }

            if ($dFromDate !== '' && $dToDate !== '') {
                $query->whereBetween('creation_on', [$dFromDate, $dToDate]);
            }

            $query->orderBy('creation_on', 'desc');

            $filteredData = $query->paginate($iLimit);

            return $filteredData;

        } catch (QueryException $e) {
            throw new \Exception("Failed to fetch data: " . $e->getMessage());
        }
    }
    
}
