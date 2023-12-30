<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Models\AIDSUpload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $dSubmissionDate = $request->input('submission_date', '');
        $dSubmissionDate = $dSubmissionDate != '' ? date($dSubmissionDate): '';

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
            $dSubmissionDate != '' ? $fileRecord->submission_date = $dSubmissionDate: '';
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
        $search = $request->input('search', '');
        $iSBN = $request->input('isbn', '');
        $iTypeId = $request->input('typeId', '');
        $iSemester = $request->input('semester', '');
        $dFromDate = $request->input('fromDate', '');
        $dToDate = $request->input('dToDate', '');
        $iLimit = $request->input('limit', 10);
        $iPage = $request->input('page', 1);
        $dToDate = date("Y-m-d", strtotime($dToDate . "+1 day"));
        try {
            DB::enableQueryLog();
            $query = AIDSUpload::select('aids_student_semester.semester as sem', 'aids_staff_upload.*')
                ->leftJoin('aids_student_semester', 'aids_staff_upload.semester', '=', 'aids_student_semester.id')
                ->where('aids_staff_upload.deleted', 0)
                ->where('aids_staff_upload.status', 1)
                ->where('aids_student_semester.deleted', 0)
                ->where('aids_student_semester.status', 1)
                ->orderBy('added_on','DESC');

            if ($name !== '') {
                $query->where('aids_staff_upload.name', 'like', '%' . $name . '%');
            }
            if ($search !== '') {
                $query->where('aids_staff_upload.name', 'like', '%' . $search . '%');
            }

            if ($iSemester !== '') {
                $query->where('aids_staff_upload.semester', $iSemester);
            }

            if ($iSBN !== '') {
                $query->where('aids_staff_upload.isbn', $iSBN);
            }
            if ($search !== '') {
                $query->orWhere('aids_staff_upload.isbn', $search);
            }
            if ($iTypeId !== '' && $iTypeId > 0) {
                $query->where('aids_staff_upload.file_type', $iTypeId);
            }
            if ($dFromDate !== '' && $dToDate !== '') {
                $query->whereRaw('aids_staff_upload.added_on between ? AND ?', [$dFromDate, $dToDate]);
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

    public function delete($id)
    {
        try {
            $book = AIDSUpload::findOrFail($id);
            $book->update(['deleted' => 1]); // Set the 'deleted' flag to 1 (or any appropriate value) for soft deletion.
            return response()->json(['message' => 'Book deleted successfully', 'status_code' => 200]);
        } catch (ModelNotFoundException $e) {
            // Handle "Model not found" exception (record not found)
            return response()->json(['message' => 'Book not found', 'status_code' => 404], 404);
        } catch (QueryException $e) {
            // Handle database query exceptions
            return response()->json(['message' => 'Failed to delete the book', 'status_code' => 500], 500);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['message' => 'An error occurred', 'status_code' => 500], 500);
        }
    }
}
