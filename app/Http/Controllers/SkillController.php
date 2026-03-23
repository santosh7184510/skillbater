<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Skill;
use App\Models\SkillRequest;   // ✅ ADD THIS
use Illuminate\Support\Facades\Auth;

class SkillController extends Controller
{
    // 🔍 SEARCH SKILLS
    public function search(Request $request)
    {
        $query = $request->q;

        if (!$query) {
            return response()->json([]);
        }

        $skills = Skill::where('name', 'LIKE', "%{$query}%")
            ->with('user:id,username')
            ->get();

        return response()->json($skills);
    }

    // 📩 SEND REQUEST
    public function sendRequest(Request $request)
{
    $request->validate([
        'to_user'  => 'required|exists:users,id',
        'skill_id' => 'required|exists:skills,id',
    ]);

    // Prevent sending request to yourself
    if (Auth::id() == $request->to_user) {
        return response()->json([
            'message' => 'You cannot request your own skill'
        ], 400);
    }

    // Prevent duplicate request
    $exists = SkillRequest::where('from_user', Auth::id())
        ->where('to_user', $request->to_user)
        ->where('skill_id', $request->skill_id)
        ->where('status', 'pending')
        ->exists();

    if ($exists) {
        return response()->json([
            'message' => 'Request already sent'
        ], 400);
    }

    $skillRequest = SkillRequest::create([
        'from_user' => Auth::id(),
        'to_user'   => $request->to_user,
        'skill_id'  => $request->skill_id,
        'status'    => 'pending',
    ]);

    return response()->json([
        'message' => 'Request sent successfully',
        'data'    => $skillRequest
    ]);
}

public function users()
{
    try {
        $skills = \App\Models\Skill::with('user:id,username')
                    ->select('id', 'name', 'user_id')
                    ->get();

        return response()->json($skills);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}


public function acceptRequest(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id'
    ]);

    // Example logic (change based on your DB structure)
    \App\Models\SkillRequest::where('to_user', auth()->id())
        ->where('from_user', $request->user_id)
        ->update(['status' => 'accepted']);

    return response()->json([
        'message' => 'Request accepted successfully'
    ]);
}


public function index()
{
    $user = Auth::user();

    if (!$user) {
        $skills = []; // guest sees empty
    } else {
        $skills = $user->skills;
    }

    return view('myskill', compact('skills'));
}

    public function store(Request $request)
    {
        // Validate
        $request->validate([
            'name' => 'required|string|max:255',
            'has_certificate' => 'nullable|boolean'
        ]);

        // Create skill
        $skill = new Skill();
        $skill->user_id = auth()->id();
        $skill->name = $request->name;
        $skill->has_certificate = $request->has_certificate ? true : false;

        // Optional certificate file
        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');
            $path = $file->store('certificates', 'public');
            $skill->certificate_path = $path;
        }

        $skill->save();

        return redirect()->back()->with('success', 'Skill added successfully!');
    }



}
