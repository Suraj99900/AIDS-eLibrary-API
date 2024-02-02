<?php

namespace App\Http\Controllers;

use App\Models\UploadFile;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class UploadFileController extends Controller
{
    public function addUploadData(Request $request)
    {
        try {
            $this->validate($request, [
                'sub_folder_id' => 'required',
                'name' => 'required',
                'file' => 'required|file|max:100000', // Adjust the max file size if needed
                'user_name' => 'required',
                'file_type' => 'required',
            ]);

            $sName = $request->input('name', '');
            $sDescription = $request->input('description', '');
            $iFileType = $request->input('file_type', '');
            $sUserName = $request->input('user_name', '');
            $iSubFolderId = $request->input('sub_folder_id', '');

            // Get the uploaded file
            $uploadedFile = $request->file('file');
            $originalFileName = $uploadedFile->getClientOriginalName();

            $originalFileName = $sName . "_class_room_" . date('Y-m-d_H-i-s') . "_" . $originalFileName;

            // Move the file to the 'resources/uploads' directory
            $destinationPath = resource_path('classroom');
            if (move_uploaded_file($uploadedFile->getRealPath(), $destinationPath . '/' . $originalFileName)) {
                // File moved successfully
                $aData = UploadFile::UploadFileData(
                    $sName,
                    $sDescription,
                    $iFileType,
                    $sUserName,
                    $iSubFolderId,
                    url('classroom/' . $originalFileName),
                    $originalFileName
                );

                return response()->json([
                    'message' => 'File uploaded successfully.',
                    'status_code' => 200,
                    'data' => $aData,
                ], 200);
            } else {

                return response()->json([
                    'message' => 'Internal server error occurred.',
                    'status_code' => 500,
                    'error' => 'Error moving file. See logs for details.',
                ], 500);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors(),
                'status_code' => 422,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error occurred.',
                'status_code' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllData(Request $request)
    {
        $sName = $request->input('search', '');
        $iSubFolderId = $request->input('sub_folder_id', '');
        $dFromDate = $request->input('fromDate', '');
        $dToDate = $request->input('dToDate', '');
        $iLimit = $request->input('limit', 1000);
        $iPage = $request->input('page', 1);
        $dToDate = date("Y-m-d", strtotime($dToDate . "+1 day"));

        try {
            $result = UploadFile::fetchAll($sName, $iSubFolderId, $dFromDate, $dToDate, $iLimit, $iPage);

            return response()->json([
                'message' => 'Data retrieved successfully.',
                'status_code' => 200,
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error occurred.',
                'status_code' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function downloadFile(Request $request)
    {
        try {
            $this->validate($request, [
                'url' => 'required',
            ]);
    
            $fileName = $request->input('url', '');
    
            // Define the path to the directory where files are stored
            $directory = resource_path('classroom/' . $fileName); // Adjust this to match your file storage directory
   
            if (File::exists($directory)) {
                $response = new BinaryFileResponse($directory);
    
                // Set headers using the headers property
                $response->headers->set('Content-Type', 'application/pdf');
                $response->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'"');
    
                return $response;
            } else {
                return response()->json([
                    'message' => 'File not found.',
                    'status_code' => 404
                ], 404);
            }
        } catch (\Exception $e) {
            // Handle exceptions here, e.g., log the error or return an error response
            return response()->json([
                'message' => 'An error occurred while processing the request.',
                'status_code' => 500
            ], 500);
        }
    }
}
