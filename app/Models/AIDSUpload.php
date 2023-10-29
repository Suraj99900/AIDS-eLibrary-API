<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIDSUpload extends Model
{

    public $table = 'aids_staff_upload';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id', 'name', 'isbn', 'semester','description','file_name','file_type','user_name','added_on','status','deleted'
    ];
    


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
