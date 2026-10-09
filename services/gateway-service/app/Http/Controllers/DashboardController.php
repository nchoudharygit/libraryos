<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $members = Http::timeout(3)->get(config('services.consumer.url') . '/api/v1/members');
        $transactions = Http::timeout(3)->get(config('services.transaction.url') . '/api/v1/transactions');

        $activeMembers = 0;
        $issuedThisMonth = 0;
        $overdueBooks = 0;
        $mostBorrowedBook = null;

        if ($members->successful()) {
            $activeMembers = collect($members->json())->where('is_active', true)->count();
        }

        if ($transactions->successful()) {
            $transactionData = collect($transactions->json());

            $issuedThisMonth = $transactionData->filter(function ($t) {
                return Carbon::parse($t['issue_date'])->isSameMonth(now())
                    && Carbon::parse($t['issue_date'])->isSameYear(now());
            })->count();

            $overdueBooks = $transactionData->filter(function ($t) {
                return is_null($t['return_date'])
                    && Carbon::parse($t['due_date'])->isPast();
            })->count();

            $mostBorrowedBook = $transactionData
                ->groupBy('book_id')
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first();
        }

        return view('dashboard', [
            'activeMembers' => $activeMembers,
            'issuedThisMonth' => $issuedThisMonth,
            'overdueBooks' => $overdueBooks,
            'mostBorrowedBook' => $mostBorrowedBook,
        ]);
    }
}