<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\MasterFolderModel;


class MasterFolderController extends Controller
{
    public function createFolder(Request $request)
    {
        try {

            $data =  $this->validate($request, [
                'folder_name' => 'required',
                'user_name' => 'required',
            ]);

            $data['creation_on'] = date('Y-m-d H:i:s');

            $folder = MasterFolderModel::createFolder($data);

            return response()->json(['message' => 'Folder created successfully', 'data' => $folder, "status_code" => 200], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), "status_code" => 500], 500);
        }
    }

    public function updateFolder(Request $request, $folderId)
    {
        try {
            $data = $request->validate([
                'folder_name' => 'string',
                'user_name' => 'string',
            ]);

            MasterFolderModel::updateFolder($folderId, $data);

            return response()->json(['message' => 'Folder updated successfully', "status_code" => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), "status_code" => 500], 500);
        }
    }

    public function getFolderById($folderId)
    {
        try {
            $folder = MasterFolderModel::getFolderById($folderId);

            return response()->json(['data' => $folder, "status_code" => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), "status_code" => 404], 500);
        }
    }

    public function getAllFolders(Request $request)
    {
        // DataTables parameters
        $draw = $request->input('draw');
        $start = $request->input('start') != null? $request->input('start') : '';
        $length = $request->input('length') != null ? $request->input('length') : '';
        $orderColumn = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir') ? $request->input('order.0.dir') :'asc';

        $searchValue = $request->input('search.value')? $request->input('search.value'): '';
        if($searchValue  == ''){
            $searchValue = !is_array($request->input('search')) ?$request->input('search') :'';
        }
        // Your custom parameters
        $limit = $request->input('limit') ? $request->input('limit') : 7;
        $page = $request->input('page') ? $request->input('page') : 0;

        try {
            // Modify the query based on DataTables parameters
            $folders = MasterFolderModel::getDataTableData($start, $length, $orderColumn, $orderDirection, $searchValue, $limit);

            // Total records without filtering
            $totalRecords = MasterFolderModel::getTotalRecords();

            // Total records after filtering (if search is applied)
            $filteredRecords = MasterFolderModel::getFilteredRecords($searchValue);

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $folders,
                "status_code" => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), "status_code" => 500], 500);
        }
    }

    public function freezeFolder(Request $request, $id)
    {
        $status = $request->input('status') ?: '';
        $msg = $status == 0 ? "Folder frozen successfully" : "Folder unfrozen successfully";
        try {
            MasterFolderModel::freezeFolder((int)$id, (int)$status);

            return response()->json(['message' => $msg, "status_code" => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), "status_code" => 500], 500);
        }
    }

    public function deleteFolder(Request $request, $id)
    {
        $delete = $request->input('delete') ?: '';

        try {
            MasterFolderModel::deleteFolder($id, $delete);

            return response()->json(['message' => 'Folder deleted successfully', "status_code" => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), "status_code" => 500], 500);
        }
    }
}
