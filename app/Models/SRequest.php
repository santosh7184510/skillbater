<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SRequest extends Model
{
    use HasFactory;
        protected $fillable = [
        'from_user',
        'to_user',
        'skill_id',
        'status',
    ];

}
