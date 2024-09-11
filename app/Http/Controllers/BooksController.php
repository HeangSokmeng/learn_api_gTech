<?php

namespace App\Http\Controllers;

use App\Models\Books;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        return Books::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function BookValidate(Request $request)
    {
        return Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'description' => 'nullable|string',
            'number_of_books' => 'required|integer|max:255'
        ]);
    }

    public function store(Request $request)
    {
        $validator = $this->BookValidate($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $books = Books::create([
            'title' => $request->title,
            'author' => $request->author,
            'description' => $request->description,
            'number_of_books' => $request->number_of_books,
        ]);
        return response()->json(['books' => $books], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Books $books)
    {
        return $books;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = $this->BookValidate($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $books = Books::find($id);
        if(!$books) return response()->json(['message' => 'Book not found'], 404);
        $books->update([
            'title' => $request->title,
            'author' => $request->author,
            'description' => $request->description,
            'number_of_books' => $request->number_of_books,
        ]);
        return response()->json(['books' => $books], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Books $books)
    {
        $books->delete();
        return response()->json(['message' => 'Book deleted successfully'], 204);
    }
}
