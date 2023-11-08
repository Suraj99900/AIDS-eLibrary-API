<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AIDSBookManage;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AidsBookManageController extends Controller
{
    public function add(Request $request)
    {
        try {
            $validatedData = $this->validate($request, [
                'book_name' => 'required|string',
                'isbn_no' => 'required|string',
                'user_name' => 'nullable|string',
            ]);
            $validatedData['added_on'] = date('Y-m-d H:i:s');
            $book = AIDSBookManage::create($validatedData);

            return response()->json(['message' => 'Book added successfully', 'status_code' => 200, 'book' => $book], 201);
        } catch (QueryException $e) {
            // Handle database query exceptions
            return response()->json(['message' => 'Failed to add the book', 'status_code' => 500], 500);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['message' => 'An error occurred', 'status_code' => 500], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $this->validate($request, [
                'book_name' => 'required|string',
                'isbn_no' => 'required|string',
                'user_name' => 'nullable|string',
            ]);

            $book = AIDSBookManage::findOrFail($id);
            $book->update($validatedData);

            return response()->json(['message' => 'Book updated successfully', 'book' => $book, 'status_code' => 200], 200);
        } catch (ModelNotFoundException $e) {
            // Handle "Model not found" exception (record not found)
            return response()->json(['message' => 'Book not found', 'status_code' => 500], 404);
        } catch (QueryException $e) {
            // Handle database query exceptions
            return response()->json(['message' => 'Failed to update the book', 'status_code' => 500], 500);
        } catch (\Exception $e) {
            echo $e;
            die;
            // Handle other exceptions
            return response()->json(['message' => 'An error occurred', 'status_code' => 500], 500);
        }
    }

    public function fetch(Request $request)
    {
        $sSearch = $request->input('search') ? $request->input('search'): '';
        $iLimit = $request->input('limit') ? $request->input('limit'): 10;

        try {
            $query = AIDSBookManage::query()->where('status',1)->where('deleted',0);
            // Apply filters if provided in the request
            if ($sSearch !== '') {
                $query->orWhere('book_name', 'like', '%' . $sSearch . '%');
            }

            if ($sSearch !== '') {
                $query->orWhere('isbn_no', 'like', '%' . $sSearch . '%');
            }

            // Apply pagination
            $perPage = $request->input('page', 10); // Default 10 items per page

            $books = $query->paginate($iLimit);

            return response()->json(['message' => 'Books fetched successfully', 'books' => $books], 200);
        } catch (QueryException $e) {
            // Handle database query exceptions
            return response()->json(['message' => 'Failed to fetch books', 'status_code' => 500], 500);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }


    public function fetchById($id)
    {
        try {
            $book = AIDSBookManage::findOrFail($id);
            return response()->json(['message' => 'ok', 'books' => $book, 'status_code' => 200]);
        } catch (ModelNotFoundException $e) {
            // Handle "Model not found" exception (record not found)
            return response()->json(['message' => 'Book not found', 'status_code' => 500], 404);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['message' => 'An error occurred', 'status_code' => 500], 500);
        }
    }

    public function delete($id)
    {
        try {
            $book = AIDSBookManage::findOrFail($id);
            $book->update(['deleted' => 1]); // Set the 'deleted' flag to 1 (or any appropriate value) for soft deletion.
            return response()->json(['message' => 'Book deleted successfully', 'status_code' => 200]);
        } catch (ModelNotFoundException $e) {
            // Handle "Model not found" exception (record not found)
            return response()->json(['message' => 'Book not found', 'status_code' => 404], 404);
        } catch (QueryException $e) {
            // Handle database query exceptions
            return response()->json(['message' => 'Failed to delete the book', 'status_code' => 500], 500);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['message' => 'An error occurred', 'status_code' => 500], 500);
        }
    }
    
}
