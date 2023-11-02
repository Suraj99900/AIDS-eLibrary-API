<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Models\AIDSUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Laravel\Lumen\Routing\Controller as BaseController;

class AIDSUploadController extends BaseController
{
    public function addUploadData(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'semester' => 'required',
            'file' => 'required|file|max:100000', // Adjust the max file size if needed
            'user_name' => 'required',
            'file_type' => 'required',
        ]);

        $iIsbn = $request->input('isbn', '');
        $sName = $request->input('name', '');
        $iSemester = $request->input('semester', '');
        $sDescription = $request->input('description', '');
        $iFileType = $request->input('file_type', '');
        $sUserName = $request->input('user_name', '');

        try {
            // Get the uploaded file
            $uploadedFile = $request->file('file');
            $originalFileName = $uploadedFile->getClientOriginalName();

            $originalFileName = $sName . "_" . date('Y-m-d H:i:s'). "_" . $originalFileName;
            // Move the file to the 'resources/uploads' directory
            $destinationPath = resource_path('uploads');
            $file = $uploadedFile->move($destinationPath, $originalFileName);

            $fileRecord = new AIDSUpload();
            $fileRecord->name = $sName;
            $fileRecord->isbn = $iIsbn;
            $fileRecord->semester = $iSemester;
            $fileRecord->user_name = $sUserName;
            $fileRecord->file_type = $iFileType;
            $fileRecord->description = $sDescription;
            $fileRecord->file_name = $file->getFilename();
            $fileRecord->added_on = date('Y-m-d H:i:s'); // Use Laravel's now() function to get the current date and time
            $fileRecord->file_path = url('uploads/' . $originalFileName);

            if ($fileRecord->save()) {
                return response()->json([
                    'message' => 'File uploaded successfully',
                    'status_code' => 200
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error occurred.',
                'status_code' => 500
            ], 500);
        }
    }

    public function getUploadedBook(Request $request)
    {
        $name = $request->input('name', '');
        $iSemester = $request->input('semester', '');
        $iSBN = $request->input('isbn', '');
        $iLimit = $request->input('limit', 10);
        $iPage = $request->input('page', 1);

        try {
            $query = AIDSUpload::select('aids_student_semester.semester', 'aids_staff_upload.*')
                ->leftJoin('aids_student_semester', 'aids_staff_upload.semester', '=', 'aids_student_semester.id')
                ->where('aids_staff_upload.deleted', 0)
                ->where('aids_staff_upload.status', 1)
                ->where('aids_student_semester.deleted', 0)
                ->where('aids_student_semester.status', 1)
                ->orderBy('added_on','DESC');

            if ($name !== '') {
                $query->where('aids_staff_upload.name', 'like', '%' . $name . '%');
            }

            if ($iSemester !== '') {
                $query->where('aids_staff_upload.semester', $iSemester);
            }

            if ($iSBN !== '') {
                $query->where('aids_staff_upload.isbn', $iSBN);
            }

            $result = $query->paginate($iLimit);
            return response()->json([
                'message' => 'Ok',
                'body' => $result,
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error occurred.',
                'status_code' => 500
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
            $directory = resource_path('uploads/' . $fileName); // Adjust this to match your file storage directory
    
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
