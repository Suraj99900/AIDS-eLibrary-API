<?php

// app/Http/Controllers/BookIssueController.php

namespace App\Http\Controllers;

use App\Models\AidsBookIssueBook;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class AIDSBookIssueController extends Controller
{
    // ... (Other methods remain unchanged)

    /**
     * Get all book issues with related student information.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllBookIssues(Request $request)
    {
        $sSearch = $request->input('search') ? $request->input('search') : '';
        $iLimit = $request->input('limit') ? $request->input('limit') : 7;
        $sShow = $request->input('show') !== '' ? $request->input('show') : 2;

        try {
            $bookIssues = AidsBookIssueBook::getAllBookIssues($sSearch, $iLimit, $sShow);
            return response()->json(['message' => 'ok', 'data' => $bookIssues, 'status_code' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal Server Error', 'status_code' => 500], 500);
        }
    }

    /**
     * Get a specific book issue by ID with related student information.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBookIssueById($id)
    {
        try {
            $bookIssue = AidsBookIssueBook::getBookIssueById($id);

            if ($bookIssue) {
                return response()->json(['message' => 'ok', 'data' => $bookIssue, 'status_code' => 200], 200);
            } else {
                return response()->json(['message' => 'Book issue not found.', 'status_code' => 404], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Book issue not found.', 'status_code' => 404], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal Server Error', 'status_code' => 500], 500);
        }
    }

    /**
     * Add a new book issue record.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addBookIssue(Request $request)
    {
        try {
            $data = $this->validate($request, [
                'zprn' => 'required',
                'book_id' => 'required',
                'user_name' => 'required',
            ]);
            $data['issue_date'] = date('Y-m-d');
            $bookIssue = AidsBookIssueBook::addBookIssue($data);
            return response()->json(['message' => 'Data added successfully', 'data' => $bookIssue, 'status_code' => 200], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'status_code' => 422], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal Server Error', 'status_code' => 500], 500);
        }
    }

    /**
     * Update a book issue record by ID.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBookIssueById(Request $request, $id)
    {
        try {
            $data = $this->validate($request, [
                'zprn' => 'required',
                'book_id' => 'required',
                'issue_date' => 'required',
            ]);

            $result = AidsBookIssueBook::updateBookIssueById($id, $data);

            if ($result) {
                return response()->json(['message' => 'Book issue updated successfully.', 'status_code' => 200], 200);
            } else {
                return response()->json(['message' => 'Book issue not found.', 'status_code' => 404], 404);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'status_code' => 422], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Book issue not found.', 'status_code' => 404], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal Server Error', 'status_code' => 500], 500);
        }
    }

    public function returnBookById(Request $request, $id)
    {
        try {
            $data = $this->validate($request, [
                'is_return' => 'required',
            ]);
            // Added return date in column...
            $data['return_date'] = date('Y-m-d');

            $result = AidsBookIssueBook::updateBookIssueById($id, $data);

            if ($result) {
                return response()->json(['message' => 'Book return successfully.', 'status_code' => 200], 200);
            } else {
                return response()->json(['message' => 'Book issue not found.', 'status_code' => 404], 404);
            }
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'status_code' => 422], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Book issue not found.', 'status_code' => 404], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal Server Error', 'status_code' => 500], 500);
        }
    }

    /**
     * Delete a book issue record by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteBookIssueById($id)
    {
        try {
            $result = AidsBookIssueBook::deleteBookIssueById($id);

            if ($result) {
                return response()->json(['message' => 'Book issue deleted successfully.', 'status_code' => 200], 200);
            } else {
                return response()->json(['message' => 'Book issue not found.', 'status_code' => 404], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Book issue not found.', 'status_code' => 404], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal Server Error', 'status_code' => 500], 500);
        }
    }
}
