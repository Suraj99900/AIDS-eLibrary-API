<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AIDSBookManage;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AidsBookManageController extends Controller
{
    public function add(Request $request)
    {
        try {
            $data = $request->input('bookData');
            // Ensure that "books" key exists in the JSON data and it's an array
            if (!isset($data['book']) || !is_array($data['book'])) {
                return response()->json(['message' => 'Invalid data format', 'status_code' => 400], 400);
            }

            // Initialize an array to store the inserted books
            $insertedBooks = [];

            // Loop through the array of books
            foreach ($data['book'] as $book) {
                // Validate each book's data
                $validator = Validator::make($book, [
                    'book_name' => 'required|string',
                    'isbn_no' => 'required|string',
                    'user_name' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    return response()->json(['message' => 'Validation failed', 'status_code' => 400], 400);
                }

                // Create an array with the validated book data
                $bookData = [
                    'book_name' => $book['book_name'],
                    'isbn_no' => $book['isbn_no'],
                    'user_name' => $book['user_name'] ?? null,
                    'added_on' => date('Y-m-d H:i:s')
                ];

                // Insert the book into the database
                $insertedBook = AIDSBookManage::create($bookData);
                $insertedBooks[] = $insertedBook;
            }

            return response()->json(['message' => 'Books added successfully', 'status_code' => 201, 'books' => $insertedBooks], 201);
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
            // Handle other exceptions
            return response()->json(['message' => 'An error occurred', 'status_code' => 500], 500);
        }
    }

    public function fetch(Request $request)
    {
        $sSearch = $request->input('search') ? $request->input('search') : '';
        $iLimit = $request->input('limit') ? $request->input('limit') : 10;

        try {
            $query = AIDSBookManage::query()->where('status', 1)->where('deleted', 0);
            // Apply filters if provided in the request
            if ($sSearch !== '') {
                $query->Where('book_name', 'like', '%' . $sSearch . '%');
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
