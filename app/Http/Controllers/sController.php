<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Skill;
use App\Models\SkillRequest;
use Illuminate\Support\Facades\Auth;

class SkillController extends Controller
{
    // Search skills and return users who have it
    public function searchSkills(Request $request)
    {
        $query = $request->get('q', '');

        if (!$query) {
            return response()->json([]);
        }

        $skills = Skill::with('user')
            ->where('name', 'like', "%{$query}%")
            ->get();

        $data = $skills->map(function ($skill) {
            return [
                'id' => $skill->id,
                'skill_name' => $skill->name,
                'user' => [
                    'id' => $skill->user->id,
                    'username' => $skill->user->username,
                ]
            ];
        });

        return response()->json($data);
    }

    // Send a skill request to a user
    public function sendRequest(Request $request)
    {
        $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $fromUserId = Auth::id(); // logged-in user
        $toUserId = $request->user_id;
        $skillId = $request->skill_id;

        // Prevent sending duplicate request
        $exists = SkillRequest::where([
            'from_user' => $fromUserId,
            'to_user' => $toUserId,
            'skill_id' => $skillId,
        ])->first();

        if ($exists) {
            return response()->json(['message' => 'Request already sent'], 400);
        }

        SkillRequest::create([
            'from_user' => $fromUserId,
            'to_user' => $toUserId,
            'skill_id' => $skillId,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Request sent successfully']);
    }
}
