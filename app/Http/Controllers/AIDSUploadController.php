<?php

namespace App\Http\Controllers;

use App\Models\AIDSUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Laravel\Lumen\Routing\Controller as BaseController;

class AIDSUploadController extends BaseController
{
    public function addUploadData(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'semester' => 'required',
            'file' => 'required|file|max:100000',
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
            // Get the uploaded file and store it in the 'uploads' directory
            $file = $request->file('file')->store('uploads');

            $fileRecord = new AIDSUpload();
            $fileRecord->name = $sName;
            $fileRecord->isbn = $iIsbn;
            $fileRecord->semester = $iSemester;
            $fileRecord->user_name = $sUserName;
            $fileRecord->file_type = $iFileType;
            $fileRecord->description = $sDescription;
            $fileRecord->file_name = $file;
            $fileRecord->added_on = date("Y-m-d h:i:s");
            $fileRecord->file_path = url('uploads/' . $file);

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
                ->where('aids_student_semester.status', 1);

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

            return $result;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error occurred.',
                'status_code' => 500
            ], 500);
        }
    }

    public function downloadFile($fileName)
    {
        // Define the path to the directory where files are stored
        $directory = resource_path('uploads'); // Adjust this to match your file storage directory

        // Build the full path to the file
        $filePath = $directory . '/' . $fileName;

        if (file_exists($filePath)) {
            return Response::download($filePath, $fileName);
        } else {
            return response()->json([
                'message' => 'File not found.',
                'status_code' => 404
            ], 404);
        }
    }
}
