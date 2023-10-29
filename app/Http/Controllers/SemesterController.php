<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class SemesterController extends BaseController
{
    public function getAllSemester(Request $request)
    {
        try {
            $oSemester = Semester::where("status",1)->where("deleted",0)->get();
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal server error occurred.', "status_code" => 500], 500);
        }
        return response()->json([
            "message" => "OK",
            "data" => $oSemester,
            "status_code" => 200
        ], 200);
    }
}
