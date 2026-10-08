<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Member::all();

        return response()->json($members, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'membership_id' => 'required|string|unique:members,membership_id',
            'join_date' => 'required|date',
            'is_active' => 'boolean',
        ]);

        $member = Member::create($validated);

        return response()->json($member, 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        return response()->json($member, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:members,email,' . $id,
            'membership_id' => 'sometimes|string|unique:members,membership_id,' . $id,
            'join_date' => 'sometimes|date',
            'is_active' => 'sometimes|boolean',
        ]);

        $member->update($validated);

        return response()->json($member, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        $member->delete();

        return response()->json(['message' => 'Member deleted successfully'], 200);
    }
}
