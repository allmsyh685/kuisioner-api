<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponseAnswer extends Model
{
    use HasFactory;

    protected $table = 'response_answers';

    protected $fillable = [
        'response_id',
        'question_id',
        'option_id',
        'answer_text',
    ];
}



