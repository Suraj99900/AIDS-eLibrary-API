<?php

namespace App\Models;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Model;


class MasterFolderModel extends Model
{
    public $table = 'folder_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'folder_name', 'user_name', 'creation_on', 'status', 'deleted'
    ];

    /**
     * Create a new folder.
     *
     * @param array $data
     * @return MasterFolderModel
     * @throws \Exception
     */
    public static function createFolder(array $data)
    {
        try {
            return self::create($data);
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to create folder: " . $e->getMessage());
        }
    }

    /**
     * Update folder details.
     *
     * @param $folderId
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public static function updateFolder($folderId, array $data)
    {
        try {
            return self::where('id', $folderId)->update($data)->where('status', 1)->where('delete', 0);
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to update folder: " . $e->getMessage());
        }
    }

    /**
     * Get folder by ID.
     *
     * @param $folderId
     * @return MasterFolderModel|null
     * @throws \Exception
     */
    public static function getFolderById($folderId)
    {
        try {
            return self::findOrFail($folderId)->where('status', 1)->where('delete', 0);
        } catch (ModelNotFoundException $e) {
            // Handle model not found exception
            throw new \Exception("Folder not found: " . $e->getMessage());
        }
    }

    /**
     * Fetch all folders.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public static function getAllFolders($sShow, $iLimit)
    {
        try {
            return self::where('deleted', 0)
                ->when($sShow == 0, function ($query) use ($sShow) {
                    $query->where('status', $sShow);
                })
                ->when($sShow == 1, function ($query) use ($sShow) {
                    $query->where('status', $sShow);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($iLimit);
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to fetch all folders: " . $e->getMessage());
        }
    }

    /**
     * Fetch frozen folders.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public static function getFrozenFolders()
    {
        try {
            return self::where('status', 0)->paginate(10);
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to fetch frozen folders: " . $e->getMessage());
        }
    }

    /**
     * Fetch deleted folders.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public static function getDeletedFolders()
    {
        try {
            return self::where('deleted', 1)->paginate(10);
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to fetch deleted folders: " . $e->getMessage());
        }
    }

    /**
     * Freeze a folder.
     *
     * @param $folderId
     * @return bool
     * @throws \Exception
     */
    public static function freezeFolder($folderId, $iStatus)
    {

        try {
            return self::where('id', $folderId)->update(['status' => $iStatus]);
        } catch (\Exception $e) {
            throw new \Exception("Failed to freeze folder: " . $e->getMessage());
        }
    }

    /**
     * Delete a folder.
     *
     * @param $folderId
     * @return bool
     * @throws \Exception
     */
    public static function deleteFolder($folderId, $iDeleteFlag)
    {
        try {
            return self::where('id', $folderId)->update(['deleted' => $iDeleteFlag]);
        } catch (\Exception $e) {
            throw new \Exception("Failed to delete folder: " . $e->getMessage());
        }
    }


    public static function getDataTableData($start, $length, $orderColumn, $orderDirection, $searchValue, $limit)
    {
        try {
            $query = self::where('deleted', 0);

            // Apply search filter
            if (!empty($searchValue)) {
                $query->where(function ($subquery) use ($searchValue) {
                    $subquery->where('folder_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('user_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('creation_on', 'like', '%' . $searchValue . '%');
                    // Add more fields as needed
                });
            }

            // Apply ordering
            $orderColumn = ($orderColumn == 0) ? 'created_at' : 'folder_name'; // Adjust based on your columns
            $query->orderBy($orderColumn, $orderDirection);

            // Paginate the result
            if($start != '' && $length != ''){
                return $query->paginate($limit, ['*'], 'page', ($start / $length) + 1);
            }else{
                return $query->get();
            }
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to fetch DataTables data: " . $e->getMessage());
        }
    }

    /**
     * Get the total number of records (without filtering).
     *
     * @return      * @throws \Exception
     */
    public static function getTotalRecords()
    {
        try {
            return self::where('deleted', 0)->where('status',1)->count();
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to get total records: " . $e->getMessage());
        }
    }

    /**
     * Get the total number of filtered records (with search filtering).
     *
     * @param string $searchValue
     * @return      * @throws \Exception
     */
    public static function getFilteredRecords($searchValue)
    {
        try {
            $query = self::where('deleted', 0);

            // Apply search filter
            if (!empty($searchValue)) {
                $query->where(function ($subquery) use ($searchValue) {
                    $subquery->where('folder_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('user_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('creation_on', 'like', '%' . $searchValue . '%')
                        ->where('deleted', 0)->where('status',1);
                });
            }

            return $query->count();
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to get filtered records: " . $e->getMessage());
        }
    }
}
