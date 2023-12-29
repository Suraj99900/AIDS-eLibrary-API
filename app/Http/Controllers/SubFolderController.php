<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubfolderModel;

class SubFolderController extends Controller
{
    public function createSubFolder(Request $request)
    {
        try {
            $data =  $this->validate($request, [
                'master_folder_id' => 'required',
                'sub_folder' => 'required',
                'user_name' => 'required',
            ]);

            $data['creation_on'] = date('Y-m-d H:i:s');

            // Validate the request data here if needed

            $subfolder = SubfolderModel::createSubFolder($data);

            return response()->json(['message' => 'Subfolder created successfully', 'data' => $subfolder, 'status_code' => 201], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status_code' => 500], 500);
        }
    }

    public function getSubFolderById($id)
    {
        try {
            $subfolder = SubfolderModel::getSubFolderById($id);

            if (!$subfolder) {
                return response()->json(['error' => 'Subfolder not found', 'status_code' => 404], 404);
            }

            return response()->json(['data' => $subfolder, 'status_code' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status_code' => 500], 500);
        }
    }

    public function getSubFoldersByMasterFolderId(Request $request,$masterFolderId)
    {
        $sSearch = $request->input('search') ? $request->input('search') : '';
        try {
            $subfolders = SubfolderModel::getSubFoldersByMasterFolderId($masterFolderId,$sSearch);

            return response()->json(['data' => $subfolders, 'status_code' => 200], 200);
        } catch (\Exception $e) {
            return $e;die;
            return response()->json(['error' => $e->getMessage(), 'status_code' => 500], 500);
        }
    }

    public function getAllSubFolders()
    {
        try {
            $subfolders = SubfolderModel::getAllSubFolders();

            return response()->json(['data' => $subfolders, 'status_code' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status_code' => 500], 500);
        }
    }

    public function deleteSubFolder($id)
    {
        try {
            SubfolderModel::deleteSubFolder($id);

            return response()->json(['message' => 'Subfolder deleted successfully', 'status_code' => 200], 200);
        } catch (\Exception $e) {
            return $e;die;
            return response()->json(['error' => $e->getMessage(), 'status_code' => 500], 500);
        }
    }

    public function freezeSubFolder($id)
    {
        try {
            $subfolder = SubfolderModel::freezeSubFolder($id);

            return response()->json(['message' => 'Subfolder frozen successfully', 'data' => $subfolder, 'status_code' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status_code' => 500], 500);
        }
    }

    public function unfreezeSubFolder($id)
    {
        try {
            $subfolder = SubfolderModel::unfreezeSubFolder($id);

            return response()->json(['message' => 'Subfolder unfrozen successfully', 'data' => $subfolder, 'status_code' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status_code' => 500], 500);
        }
    }
}
