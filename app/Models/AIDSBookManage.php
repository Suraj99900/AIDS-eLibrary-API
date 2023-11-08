<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIDSBookManage extends Model
{
    protected $table = 'aids_book_manage';

    protected $fillable = [
        'book_name',
        'isbn_no',
        'added_on',
        'user_name',
        'status',
        'deleted',
    ];

    // Define any relationships, accessors, or mutators as needed.

}
