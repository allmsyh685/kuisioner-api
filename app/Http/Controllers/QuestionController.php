<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::where('is_active', true)
            ->with('optionItems')
            ->orderBy('order')
            ->get()
            ->map(function ($q) {
                return [
                    'id' => $q->id,
                    'question_text' => $q->question_text,
                    'options' => $q->optionItems->map(function ($opt) {
                        return [
                            'id' => $opt->id,
                            'text' => $opt->option_text,
                            'order' => $opt->option_order,
                        ];
                    })->values(),
                    'order' => $q->order,
                    'is_active' => (bool) $q->is_active,
                    'created_at' => optional($q->created_at)->toISOString(),
                    'updated_at' => optional($q->updated_at)->toISOString(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $questions,
        ]);
    }
}


