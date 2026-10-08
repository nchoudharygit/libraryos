<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::all();

        return response()->json($transactions, 200);
    }

    public function show(string $id)
    {
        $transaction = Transaction::findOrFail($id);

        return response()->json($transaction, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|integer',
            'consumer_id' => 'required|integer',
            'due_date' => 'required|date|after:today',
        ]);

        try {
            $bookResponse = Http::timeout(3)->get(
                config('services.catalog.url') . '/api/v1/books/' . $validated['book_id']
            );
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Catalog service unavailable'], 503);
        }

        if ($bookResponse->status() === 404) {
            return response()->json(['message' => 'Book not found'], 422);
        }

        if ($bookResponse->failed()) {
            return response()->json(['message' => 'Catalog service unavailable'], 503);
        }
        $book = $bookResponse->json();

        $issuedCount = Transaction::where('book_id', $validated['book_id'])
            ->where('status', 'issued')
            ->count();

        if ($issuedCount >= $book['total_copies']) {
            return response()->json(['message' => 'Book is not available'], 422);
        }
        try {
            $memberResponse = Http::timeout(3)->get(
                config('services.consumer.url') . '/api/v1/members/' . $validated['consumer_id']
            );
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Consumer service unavailable'], 503);
        }

        if ($memberResponse->status() === 404) {
            return response()->json(['message' => 'Member not found'], 422);
        }


        if ($memberResponse->failed()) {
            return response()->json(['message' => 'Consumer service unavailable'], 503);
        }

        $member = $memberResponse->json();

        if (!($member['is_active'] ?? false)) {
            return response()->json(['message' => 'Member is not active'], 422);
        }

        $transaction = Transaction::create([
            'book_id' => $validated['book_id'],
            'consumer_id' => $validated['consumer_id'],
            'issue_date' => now()->toDateString(),
            'due_date' => $validated['due_date'],
            'status' => 'issued',
        ]);

        return response()->json($transaction, 201);
    }

    public function update(Request $request, string $id)
    {
        $transaction = Transaction::findOrFail($id);
        $validated = $request->validate([
            'return_date' => 'sometimes|date',
            'status' => 'sometimes|string|in:issued,returned,overdue'
        ]);

        $transaction->update($validated);
        return response()->json($transaction, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully'], 200);
    }
}
