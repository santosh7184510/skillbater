<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_user',
        'to_user',
        'skill_id',
        'status'
    ];

    // 🔹 Skill Relationship
    public function skill()
    {
        return $this->belongsTo(\App\Models\Skill::class, 'skill_id');
    }

    // 🔹 Who sent the request
    public function fromUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'from_user');
    }

    // 🔹 Who receives the request
    public function toUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'to_user');
    }
}
