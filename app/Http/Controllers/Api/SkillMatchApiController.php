<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Models\User;
use App\Models\SkillRequest;
use Illuminate\Support\Facades\Auth;

class SkillMatchApiController extends Controller
{
    // Return all skills from other users
    public function index() {
        $skills = Skill::with('user')->where('user_id','<>', Auth::id())->get();

        $data = $skills->map(function($skill) {
            return [
                'id' => $skill->user->username ?? 'USR-'.$skill->user_id,
                'user_id' => $skill->user_id,
                'reqSkill' => $skill->name,
                'skill_id' => $skill->id,
                'known' => $skill->user->skills()->pluck('name')->toArray()
            ];
        });

        return response()->json($data);
    }

    // Return full profile of a user
    public function profile($id) {
        $user = User::with('skills')->findOrFail($id);

        return response()->json([
            'id' => $user->username ?? 'USR-'.$user->id,
            'skills' => $user->skills()->pluck('name'),
        ]);
    }

    // Accept/Reject skill request
    public function requestSkill(Request $request) {
        $request->validate([
            'to_user' => 'required|exists:users,id',
            'skill_id' => 'required|exists:skills,id',
            'action' => 'required|in:accept,reject'
        ]);

        SkillRequest::updateOrCreate(
            [
                'from_user' => Auth::id(),
                'to_user' => $request->to_user,
                'skill_id' => $request->skill_id
            ],
            [
                'status' => $request->action === 'accept' ? 'accepted' : 'rejected'
            ]
        );

        return response()->json(['success'=>true]);
    }
}
