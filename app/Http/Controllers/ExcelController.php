<?php

namespace App\Http\Controllers;

use App\Exports\StudentExport;
use App\Models\AIDSStudentInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function exportStudents()
    {
        try {
            // Fetch student information from the model
            $students = AIDSStudentInfo::all();

            // Format the data as needed for the export
            $formattedData = [];
            $index = 1;
            foreach ($students as $student) {
                $formattedData[] = [
                    'Sr No' => $index,
                    'name' => $student->name,
                    'ZPRN' => $student->zprn,
                    'Semester' => $student->semester,
                    'Phone No' => $student->phone_no,
                    'Address' => $student->address,
                    'Email' => $student->email,
                    'Need To Update' => 0,
                ];
                $index++;
            }
            $aHead[] = [
                'Sr No',
                'name',
                'ZPRN',
                'Semester',
                'Phone No',
                'Address',
                'Email',
                'Need To Update',
            ];

            $export = new StudentExport($formattedData, $aHead);

            // Call the download method
            return $export->download();
        } catch (\Exception $e) {
            // Log the exception
            return response()->json([
                'message' => 'An error occurred while exporting the Excel file.',
                'status_code' => 500,
            ], 500);
        }
    }


    public function importStudents(Request $request)
    {
        try {
            // Validate the request and check if the file is present
            $this->validate($request, [
                'file' => 'required|mimes:xlsx,xls',
            ]);

            // Get the uploaded file
            $file = $request->file('file');

            // Load the Excel file using Laravel Excel
            $importedData = Excel::toArray([], $file);

            // Assuming the first sheet is used for the import
            $data = $importedData[0];
            for ($iii = 3; $iii < count($data); $iii++) {
                // Process the imported data and store it in the database
                if ($data[$iii][7] == 1) {
                    $studentData = [
                        'name' => $data[$iii][1],
                        'zprn' => $data[$iii][2],
                        'semester' => $data[$iii][3],
                        'phone_no' => $data[$iii][4],
                        'address' => $data[$iii][5],
                        'email' => $data[$iii][6],
                        'added_on' => date('Y-m-d H:i:s'),
                    ];
                    $existingStudent = AIDSStudentInfo::where('zprn', $studentData['zprn'])->first();
                    if($existingStudent){
                        continue;
                    }
                    try {
                        // Create a new student record
                        $student = new AIDSStudentInfo();
                        $student->name = $studentData['name'];
                        $student->zprn = $studentData['zprn'];
                        $student->email = $studentData['email'];
                        $student->semester = $studentData['semester'];
                        $student->phone_no = $studentData['phone_no'];
                        $student->address = $studentData['address'];
                        $student->added_on = date('Y-m-d H:i:s');
                        $student->status = 1;
                        $student->deleted = 0;
                        $student->save();
                    } catch (\Exception $e) {

                        return response()->json([
                            'message' => 'An error occurred while importing data.',
                            'status_code' => 500,
                        ], 500);
                    }
                }
            }


            return response()->json([
                'message' => 'Data imported successfully.',
                'status_code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return $e;
            // Log the exception
            return response()->json([
                'message' => 'An error occurred during the import process.',
                'status_code' => 500,
            ], 500);
        }
    }
}
