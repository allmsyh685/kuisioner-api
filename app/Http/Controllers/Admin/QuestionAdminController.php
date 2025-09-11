<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionAdminController extends Controller
{
    public function index()
    {
        $questions = Question::with('options')->orderBy('order')->get()->map(function ($q) {
            return [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'options' => $q->options->pluck('option_text')->values(),
                'order' => $q->order,
                'is_active' => (bool) $q->is_active,
                'created_at' => optional($q->created_at)->toISOString(),
                'updated_at' => optional($q->updated_at)->toISOString(),
            ];
        });
        return response()->json(['success' => true, 'data' => $questions]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_text' => 'required|string',
            'options' => 'required|array|min:1',
            'order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'data' => null, 'message' => $validator->errors()->first()], 422);
        }
        $data = $validator->validated();
        $question = Question::create([
            'question_text' => $data['question_text'],
            'order' => $data['order'],
            'is_active' => $data['is_active'] ?? true,
            // Persist options to satisfy MySQL NOT NULL json column
            'options' => array_values($data['options']),
        ]);

        foreach ($data['options'] as $idx => $text) {
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => $text,
                'option_order' => $idx + 1,
            ]);
        }

        $question->load('options');
        return response()->json(['success' => true, 'data' => [
            'id' => $question->id,
            'question_text' => $question->question_text,
            'options' => $question->options->pluck('option_text')->values(),
            'order' => $question->order,
            'is_active' => (bool) $question->is_active,
            'created_at' => optional($question->created_at)->toISOString(),
            'updated_at' => optional($question->updated_at)->toISOString(),
        ]], 201);
    }

    public function update(int $id, Request $request)
    {
        $question = Question::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'question_text' => 'sometimes|string',
            'options' => 'sometimes|array|min:1',
            'order' => 'sometimes|integer',
            'is_active' => 'sometimes|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'data' => null, 'message' => $validator->errors()->first()], 422);
        }
        $data = $validator->validated();
        $question->update([
            'question_text' => $data['question_text'] ?? $question->question_text,
            'order' => $data['order'] ?? $question->order,
            'is_active' => $data['is_active'] ?? $question->is_active,
            // Keep options json column in sync when provided
            'options' => array_key_exists('options', $data) ? array_values($data['options']) : $question->options,
        ]);

        if (array_key_exists('options', $data)) {
            QuestionOption::where('question_id', $question->id)->delete();
            foreach ($data['options'] as $idx => $text) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $text,
                    'option_order' => $idx + 1,
                ]);
            }
        }

        $question->load('options');
        return response()->json(['success' => true, 'data' => [
            'id' => $question->id,
            'question_text' => $question->question_text,
            'options' => $question->options->pluck('option_text')->values(),
            'order' => $question->order,
            'is_active' => (bool) $question->is_active,
            'created_at' => optional($question->created_at)->toISOString(),
            'updated_at' => optional($question->updated_at)->toISOString(),
        ]]);
    }

    public function destroy(int $id)
    {
        $question = Question::findOrFail($id);
        $question->delete();
        return response()->json(['success' => true, 'data' => null]);
    }
}


