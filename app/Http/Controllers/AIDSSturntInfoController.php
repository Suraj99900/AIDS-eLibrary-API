<?php

namespace App\Http\Controllers;

use App\Models\AIDSStudentInfo;
use Illuminate\Http\Request;

class AIDSSturntInfoController extends Controller
{
    public function addStudentInfo(Request $request)
    {
        try {
            // Validate the incoming request data
            $this->validate($request, [
                'name' => 'required|string',
                'zprn' => 'required|string',
                'semester' => 'required|integer',
                'phone_no' => 'string|nullable',
                'address' => 'string|nullable',
            ]);

            // Check if a student with the same ZPRN number already exists
            $existingStudent = AIDSStudentInfo::where('zprn', $request->input('zprn'))->first();

            if ($existingStudent) {
                return response()->json(['message' => 'A student with the same ZPRN number already exists', 'status_code' => 400], 400);
            }

            // Create a new student record
            $student = new AIDSStudentInfo();
            $student->name = $request->input('name');
            $student->zprn = $request->input('zprn');
            $student->email = $request->input('email');
            $student->semester = $request->input('semester');
            $student->phone_no = $request->input('phone_no');
            $student->address = $request->input('address');
            $student->added_on = date('Y-m-d H:i:s');
            $student->status = 1;
            $student->deleted = 0;
            $student->save();

            return response()->json(['message' => 'Student information added successfully', 'status_code' => 201], 201);
        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            return response()->json(['message' => 'Error: ' . $e->getMessage(), 'status_code' => 500], 500);
        }
    }


    public function getStudentsInfo(Request $request)
    {
        $sSearch = $request->input('search') ? $request->input('search') : '';
        try {
            // Fetch all student records with a left join on the Semester model
            $students = AIDSStudentInfo::leftJoin('aids_student_semester', 'aids_student_info.semester', '=', 'aids_student_semester.id')
                ->select('aids_student_info.*', 'aids_student_semester.semester as semester_name')
                ->orderBy('updated_at', 'DESC');

            if ($sSearch !== '' && $sSearch !== null) {
                $students->where('name', 'like', '%' . $sSearch . '%');
                $students->orWhere('zprn', 'like', '%' . $sSearch . '%');
            }
            $students = $students->get();
            return response()->json(['students' => $students, 'status_code' => 200], 200);
        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            return response()->json(['message' => 'Error: ' . $e->getMessage(), 'status_code' => 500], 500);
        }
    }

    public function getStudentByNameZPRN(Request $request)
    {
        try {
            $name = $request->input('name');
            $zprn = $request->input('zprn');

            $student = AIDSStudentInfo::where('zprn', $zprn)
                ->first();

            if ($student) {
                return response()->json(['student' => $student, 'status_code' => 200], 200);
            } else {
                return response()->json(['message' => 'Student not found', 'status_code' => 500], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage(), 'status_code' => 500], 500);
        }
    }

    public function updateStudentByNameZPRN(Request $request)
    {
        try {
            $name = $request->input('name');
            $zprn = $request->input('zprn');

            $student = AIDSStudentInfo::where('zprn', $zprn)
                ->first();
            if ($student) {
                // Update the student record
                $student->fill($request->all());
                $student->update();

                return response()->json(['message' => 'Student information updated successfully', 'status_code' => 200], 200);
            } else {
                return response()->json(['message' => 'Student not found', 'status_code' => 500], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage(), 'status_code' => 500], 500);
        }
    }

    public function freezeStudent($zprn)
    {
        try {
            // Find the student record by ZPRN
            $student = AIDSStudentInfo::where('zprn', $zprn)->first();

            if ($student) {
                // Set the status or a specific field to indicate the user is "frozen"
                $student->status = 0; // Set the status to 0 (or another appropriate value)
                $student->save();

                return response()->json(['message' => 'Student has been frozen', 'status_code' => 200], 200);
            } else {
                return response()->json(['message' => 'Student not found', 'status_code' => 500], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage(), 'status_code' => 500], 500);
        }
    }
}
