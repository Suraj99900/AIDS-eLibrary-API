<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIDSStudentInfo extends Model
{
    
    public $table = 'aids_student_info';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id', 'name', 'zprn', 'semester','phone_no','address','added_on','status','deleted'
    ];
    


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
