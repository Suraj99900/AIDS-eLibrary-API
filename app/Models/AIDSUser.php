<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class AIDSUser extends Model
{

    public $table = 'aids_user';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_name', 'password', 'client_id', 'user_type', 'added_on', 'status', 'deleted'
    ];


    public static function getAllUser($sSearch, $iLimit, $sShow)
    {
        return self::where('deleted', 0)
            ->when($sSearch != "", function ($query) use ($sSearch) {
                $query->where('user_name', 'like', '%' . $sSearch . '%');
            })
            ->when($sShow == 0, function ($query) use ($sShow) {
                $query->where('status', $sShow);
            })
            ->when($sShow == 1, function ($query) use ($sShow) {
                $query->where('status', $sShow);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($iLimit);
    }

    public static function changePassword($iId, $sOldPass, $sNewPass)
    {
        $oUser = AIDSUser::where("id", $iId)->where('status', 1)->where('deleted', 0)->first();
        if (!Hash::check($sOldPass, $oUser->password)) {
            return false;
        }else{
            $sHashPassword = Hash::make($sNewPass);
            return AIDSUser::where("id", $iId)->where('status', 1)->where('deleted', 0)->update(['password' => $sHashPassword]);
        }
        
    }

    public static function freezeUserById($iId)
    {
        $oUser = AIDSUser::where('id', $iId)->where('deleted',0)->first();
        $iStatus = $oUser->status == 0 ? 1 :0;
        return self::where('status', $oUser->status)
            ->where('deleted', 0)
            ->where('id', $iId)
            ->update(['status' => $iStatus]);
    }


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
