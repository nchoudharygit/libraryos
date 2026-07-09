<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::with('author')->latest()->paginate(10);
        return response()->json($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'published_date' => 'nullable|date',
            'isbn' => 'nullable|string|max:20|unique:books,isbn',
            'summary' => 'nullable|string',
        ]);

        $book = Book::create($validatedData);
        return response()->json($book, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        return response()->json($book->load('author'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validatedData = $request->validate([
            'title' => 'string|max:255',
            'author_id' => 'exists:authors,id',
            'published_date' => 'nullable|date',
            'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $book->id,
            'summary' => 'nullable|string',
        ]);

        $book->update($validatedData);
        return response()->json($book);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(['success' => true, 'message' => 'Book deleted successfully']);
    }
}
