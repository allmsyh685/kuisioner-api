<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScoreController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'points' => 'required|integer|min:0',
            'survey_id' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'data' => null, 'message' => $validator->errors()->first()], 422);
        }
        $score = Score::create($validator->validated());
        return response()->json(['success' => true, 'data' => $score], 201);
    }

    public function leaderboard()
    {
        $scores = Score::orderByDesc('points')->orderBy('created_at')->limit(100)->get(['name','points','created_at']);
        return response()->json(['success' => true, 'data' => $scores]);
    }

    public function rank(Request $request)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:0',
        ]);

        $points = (int) $validated['points'];

        // Rank is number of scores with strictly higher points + 1
        $higherCount = Score::where('points', '>', $points)->count();
        $rank = $higherCount + 1;

        // Also return top 3 for convenience
        $top = Score::orderByDesc('points')->orderBy('created_at')->limit(3)->get(['name','points','created_at']);

        return response()->json([
            'success' => true,
            'data' => [
                'rank' => $rank,
                'top' => $top,
            ],
        ]);
    }
}



