<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_text',
        'order',
        'is_active',
        // no direct options JSON column in MySQL schema
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function optionItems()
    {
        return $this->hasMany(QuestionOption::class, 'question_id')->orderBy('option_order');
    }
}


