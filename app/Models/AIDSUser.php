<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIDSUser extends Model
{

    public $table = 'aids_user';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id', 'user_name', 'password', 'client_id','user_type','added_on','status','deleted'
    ];
    


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
