<?php

namespace App\Http\Controllers;

use App\Models\SurveyResponse;
use App\Models\ResponseAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResponseController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'location' => 'required|string|max:255',
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.option_id' => 'nullable|integer|exists:question_options,id',
            'answers.*.answer_text' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $payload = $validator->validated();

        $response = SurveyResponse::create([
            'name' => $payload['name'],
            'age' => $payload['age'],
            'location' => $payload['location'],
        ]);

        foreach ($payload['answers'] as $answer) {
            ResponseAnswer::create([
                'response_id' => $response->id,
                'question_id' => $answer['question_id'],
                'option_id' => $answer['option_id'] ?? null,
                'answer_text' => $answer['answer_text'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $response,
        ], 201);
    }
}


