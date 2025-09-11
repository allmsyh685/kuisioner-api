<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $table = 'responses';

    protected $fillable = [
        'name',
        'age',
        'location',
        'education_level',
        'ai_usage_frequency',
        'ai_purpose',
        'ai_tool_used',
        'difficulty_without_ai',
        'anxiety_without_ai',
        'ai_important_routine',
        'more_productive_with_ai',
        'rely_on_ai_decisions',
        'ai_better_than_humans',
    ];
}



