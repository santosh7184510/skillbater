<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Skill;

class SkillController extends Controller
{
    // Store a new skill
    public function store(Request $request)
{
    $request->validate([
        'skills.*.name' => 'required|string|max:255',
        'skills.*.has_certificate' => 'required|boolean',
        'skills.*.certificate' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
    ]);

    foreach ($request->skills as $skillData) {
        $certificatePath = null;
        if(isset($skillData['certificate'])){
            $certificatePath = $skillData['certificate']->store('certificates', 'public');
        }

        \App\Models\Skill::create([
            'user_id' => auth()->id(),
            'name' => $skillData['name'],
            'has_certificate' => $skillData['has_certificate'],
            'certificate_path' => $certificatePath
        ]);
    }

    return redirect()->back()->with('success', 'Skills added successfully!');
}
}
