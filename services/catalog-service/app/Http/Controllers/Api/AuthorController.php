<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $authors = Author::with('books')->latest()->paginate(10);
        return response()->json($authors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:authors,email',
            'bio' => 'nullable|string',
            'nationality' => 'nullable|string|max:255',
        ]);

        $author = Author::create($validatedData);
        return response()->json($author, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author): \Illuminate\Http\JsonResponse
    {
        return response()->json($author->load('books'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Author $author): \Illuminate\Http\JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:authors,email,' . $author->id,
            'bio' => 'nullable|string',
            'nationality' => 'nullable|string|max:255',
        ]);

        $author->update($validatedData);
        return response()->json($author);
    }

    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author): \Illuminate\Http\JsonResponse
    {
        $author->delete();
        return response()->json(['success' => true, 'message' => 'Author deleted successfully']);
    }
}
