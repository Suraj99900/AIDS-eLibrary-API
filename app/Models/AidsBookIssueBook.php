<?php

// app/Models/AidsBookIssueBook.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AidsBookIssueBook extends Model
{
    public $table = 'aids_book_issue_book';

    protected $fillable = [
        'zprn',
        'book_id',
        'issue_date',
        'return_date',
        'is_return',
        'user_name',
        'status',
        'deleted',
    ];

    public function studentInfo()
    {
        return $this->belongsTo(AIDSStudentInfo::class, 'zprn', 'zprn');
    }
    public function bookManage()
    {
        return $this->belongsTo(AIDSBookManage::class, 'book_id', 'id');
    }

    /**
     * Add a new book issue record.
     *
     * @param array $data
     * @return AidsBookIssueBook
     */
    public static function addBookIssue(array $data)
    {
        return self::create($data);
    }

    /**
     * Update a book issue record by ID.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public static function updateBookIssueById($id, array $data)
    {
        $bookIssue = self::find($id);

        if ($bookIssue) {
            return $bookIssue->update($data);
        }

        return false;
    }

    /**
     * Delete a book issue record by ID.
     *
     * @param int $id
     * @return bool|null
     */
    public static function deleteBookIssueById($id)
    {
        $bookIssue = AidsBookIssueBook::findOrFail($id);

        if ($bookIssue) {
            return $bookIssue->update(['deleted' => 1]);
        }

        return false;
    }

    /**
     * Fetch all book issue records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllBookIssues($sSearch, $iLimit, $sShow)
    {
        return self::with('studentInfo')
            ->with('bookManage')
            ->where(function ($query) use ($sSearch) {
                $query->where('zprn', 'like', "%$sSearch%")
                    ->orWhereHas('studentInfo', function ($subQuery) use ($sSearch) {
                        $subQuery->where('name', 'like', "%$sSearch%");
                    })
                    ->orWhereHas('bookManage', function ($subQuery) use ($sSearch) {
                        $subQuery->where('book_name', 'like', "%$sSearch%")
                            ->orWhere('isbn_no', 'like', "%$sSearch%");
                    });
            })->where('deleted', 0)->where('status', 1)
            ->when($sShow == 1, function ($query) {
                // Add condition to filter by is_return column when $sShow is 'return'
                $query->where('is_return', 1);
            })
            ->when($sShow == 0, function ($query) {
                // Add condition to filter by not returned when $sShow is 'issue'
                $query->where('is_return', 0);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($iLimit);
    }

    /**
     * Fetch a book issue record by ID with related student information.
     *
     * @param int $id
     * @return AidsBookIssueBook|null
     */
    public static function getBookIssueById($id)
    {
        return self::with('studentInfo')->with('bookManage')->find($id);
    }
}
