<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class SubfolderModel extends Model
{
    public $table = 'subfolder';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'id', 'master_folder_id', 'sub_folder', 'user_name', 'creation_on', 'status', 'deleted'
    ];

    public static function createSubFolder($aData)
    {
        try {
            return self::create($aData);
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to create folder: " . $e->getMessage());
        }
    }

    public static function getSubFolderById($id)
    {
        try {
            return self::find($id)->where('deleted',0)->where('status',1);
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to fetch folder by ID: " . $e->getMessage());
        }
    }

    public static function getSubFoldersByMasterFolderId($masterFolderId,$sSearch)
    {
        try {
            return self::where('master_folder_id', $masterFolderId)
                ->where(function ($subquery) use ($sSearch) {
                    $subquery->where('sub_folder', 'like', '%' . $sSearch . '%')
                    ->orWhere('user_name', 'like', '%' . $sSearch . '%')
                    ->orWhere('creation_on', 'like', '%' . $sSearch . '%');
            })
            ->where('deleted', 0)->where('status', 1)->get();
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to fetch folders by master_folder_id: " . $e->getMessage());
        }
    }

    public static function getAllSubFolders()
    {
        try {
            return self::all()->where('deleted', 0)->where('status', 1);
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to fetch all folders: " . $e->getMessage());
        }
    }

    public static function deleteSubFolder($id)
    {
        try {
            return self::where('id', $id)->update(['deleted' => 1]);
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to delete folder: " . $e->getMessage());
        }
    }

    public static function freezeSubFolder($id)
    {
        try {
            $subfolder = self::find($id);
            if ($subfolder) {
                $subfolder->status = 0;
                $subfolder->save();
            }
            return $subfolder;
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to freeze folder: " . $e->getMessage());
        }
    }

    public static function unfreezeSubFolder($id)
    {
        try {
            $subfolder = self::find($id);
            if ($subfolder) {
                $subfolder->status = 1;
                $subfolder->save();
            }
            return $subfolder;
        } catch (QueryException $e) {
            // Handle database query exception
            throw new \Exception("Failed to unfreeze folder: " . $e->getMessage());
        }
    }
}
