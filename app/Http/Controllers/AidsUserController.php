<?php

namespace App\Http\Controllers;

use App\Models\AIDSUser;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AidsUserController extends Controller
{
    public function addUser(Request $request)
    {
        $this->validate($request, [
            'userName' => 'required',
            'secretkey' => 'required',
            'password' => 'required',
        ]);

        $iClientId = $request->secretkey;
        $sUserName = $request->userName;
        $sPassword = $request->password;

        try {
            // Verify the secret key for the user
            $oCheckKey = Client::where('client_id', $iClientId)->pluck('id')->toArray();

            // Check if the user is already present
            $oIsUserPresent = AIDSUser::where('user_name', $sUserName)->pluck('id')->toArray();

            if (isset($oCheckKey[0])) {
                if (!isset($oIsUserPresent[0])) {
                    // Hash the password before saving it
                    $hashedPassword = Hash::make($sPassword);

                    // Create and save the user
                    $oUser = new AIDSUser();
                    $oUser->user_name = $sUserName;
                    $oUser->password = $hashedPassword;
                    $oUser->client_id = $oCheckKey[0];
                    $oUser->added_on = date("Y-m-d h:i:s");

                    if ($oUser->save()) {
                        return response()->json([
                            "message" => "User registered successfully.",
                            "status_code" => 200
                        ], 200);
                    } else {
                        return response()->json([
                            "message" => "Something went wrong while registering the user.",
                            "status_code" => 500
                        ], 500);
                    }
                } else {
                    return response()->json([
                        "message" => "User already exists with the provided username.",
                        "status_code" => 409
                    ], 409); // 409 Conflict status code for user already exists
                }
            } else {
                return response()->json([
                    "message" => "Invalid secret key.",
                    "status_code" => 403
                ], 403); // 403 Forbidden status code for invalid secret key
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error occurred.',
                "status_code" => 500
            ], 500);
        }
    }


    public function updateUser(Request $request)
    {
        $this->validate($request, [
            'userName' => 'required',
            'old_password' => 'required',
            'new_password' => 'required',
        ]);
    
        $sUserName = $request->userName;
        $sOldPassword = $request->old_password;
        $sNewPassword = $request->new_password;
    
        try {
            // Verify the user by username
            $oUser = AIDSUser::where('user_name', $sUserName)->first();
    
            if ($oUser) {
                // Check if the old password matches the hashed password in the database
                if (Hash::check($sOldPassword, $oUser->password)) {
                    // Hash the new password before saving it
                    $hashedPassword = Hash::make($sNewPassword);
                    print_r(Hash::check($sOldPassword, $oUser->password));
    
                    // Update the user's password
                    $oUser->password = $hashedPassword;
                    $oUser->save();
    
                    return response()->json([
                        "message" => "Password updated successfully.",
                        "status_code" => 200
                    ], 200);
                } else {
                    return response()->json([
                        "message" => "Old password is incorrect.",
                        "status_code" => 403
                    ], 403);
                }
            } else {
                return response()->json([
                    "message" => "User not found.",
                    "status_code" => 404
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error occurred.',
                "status_code" => 500
            ], 500);
        }
    }
    

    public function loginUser(Request $request){
        $sUserName = $request->input('userName') ? $request->input('userName') : '';
        $sPassword = $request->input('password') ? $request->input('password') : '';


        try {
            // Verify the user by username
            $oUser = AIDSUser::where('user_name', $sUserName)->first();
    
            if ($oUser) {
                // Check if the old password matches the hashed password in the database
                if (Hash::check($sPassword, $oUser->password)) {
    
                    return response()->json([
                        "message" => "Login successfully",
                        "status_code" => 200
                    ], 200);
                } else {
                    return response()->json([
                        "message" => "Incorect Password",
                        "status_code" => 403
                    ], 403);
                }
            } else {
                return response()->json([
                    "message" => "User not found.",
                    "status_code" => 404
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal server error occurred.',
                "status_code" => 500
            ], 500);
        }

    }
}
