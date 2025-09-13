<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ResponseAdminController extends Controller
{
    public function index()
    {
        $responses = SurveyResponse::orderByDesc('created_at')->get();
        return response()->json(['success' => true, 'data' => $responses]);
    }

    public function show(int $id)
    {
        $response = SurveyResponse::findOrFail($id);
        // join answers
        $answers = DB::table('response_answers as ra')
            ->join('questions as q', 'q.id', '=', 'ra.question_id')
            ->leftJoin('question_options as qo', 'qo.id', '=', 'ra.option_id')
            ->where('ra.response_id', $id)
            ->orderBy('q.order')
            ->get([
                'q.id as question_id',
                'q.question_text',
                'qo.option_text',
                'ra.answer_text',
            ])
            ->map(function ($row) {
                return [
                    'question_id' => (int) $row->question_id,
                    'question_text' => $row->question_text,
                    'answer' => $row->option_text ?? $row->answer_text ?? '',
                ];
            });

        return response()->json(['success' => true, 'data' => [
            'id' => $response->id,
            'name' => $response->name,
            'age' => $response->age,
            'location' => $response->location,
            'created_at' => optional($response->created_at)->toISOString(),
            'updated_at' => optional($response->updated_at)->toISOString(),
            'answers' => $answers,
        ]]);
    }

    public function statistics()
    {
        $total = DB::table('responses')->count();

        // Map old dashboard buckets to new schema using question order
        // education_statistics -> question order 1 (frequency of being asked to fill questionnaire)
        $education = DB::table('response_answers as ra')
            ->join('questions as q', 'q.id', '=', 'ra.question_id')
            ->join('question_options as qo', 'qo.id', '=', 'ra.option_id')
            ->select('qo.option_text as education_level', DB::raw('COUNT(*) as count'))
            ->where('q.order', 1)
            ->groupBy('qo.option_text')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($row) {
                return [
                    'education_level' => $row->education_level,
                    'count' => (int) $row->count,
                ];
            });

        // ai_usage_statistics -> question order 2 (jenis kuesioner)
        $usage = DB::table('response_answers as ra')
            ->join('questions as q', 'q.id', '=', 'ra.question_id')
            ->join('question_options as qo', 'qo.id', '=', 'ra.option_id')
            ->select('qo.option_text as ai_usage_frequency', DB::raw('COUNT(*) as count'))
            ->where('q.order', 2)
            ->groupBy('qo.option_text')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($row) {
                return [
                    'ai_usage_frequency' => $row->ai_usage_frequency,
                    'count' => (int) $row->count,
                ];
            });

        // ai_tool_statistics -> question order 17 (Pekerjaan)
        $tools = DB::table('response_answers as ra')
            ->join('questions as q', 'q.id', '=', 'ra.question_id')
            ->join('question_options as qo', 'qo.id', '=', 'ra.option_id')
            ->select('qo.option_text as ai_tool_used', DB::raw('COUNT(*) as count'))
            ->where('q.order', 9) // if your pekerjaan is order 9; update if different
            ->groupBy('qo.option_text')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($row) {
                return [
                    'ai_tool_used' => $row->ai_tool_used,
                    'count' => (int) $row->count,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'total_responses' => (int) $total,
                'education_statistics' => $education,
                'ai_usage_statistics' => $usage,
                'ai_tool_statistics' => $tools,
            ],
        ]);
    }

    public function export()
    {
        // Fetch question list ordered
        $questions = DB::table('questions')
            ->orderBy('order')
            ->get(['id','question_text','order']);

        $responses = DB::table('responses')->orderBy('id')->get();

        // Build header: base columns + one per question (q_{order} or question_text)
        $header = ['id','name','age','location','created_at','updated_at'];
        foreach ($questions as $q) {
            $header[] = $q->question_text;
        }

        $data = [];
        $data[] = $header;

        foreach ($responses as $r) {
            // Initialize map question_id -> option_text for this response
            $answers = DB::table('response_answers as ra')
                ->leftJoin('question_options as qo', 'qo.id', '=', 'ra.option_id')
                ->where('ra.response_id', $r->id)
                ->get(['ra.question_id', 'qo.option_text', 'ra.answer_text'])
                ->keyBy('question_id');

            // Format timestamps properly - handle both string and Carbon instances
            $createdAt = $r->created_at ? \Carbon\Carbon::parse($r->created_at)->format('Y-m-d H:i:s') : '';
            $updatedAt = $r->updated_at ? \Carbon\Carbon::parse($r->updated_at)->format('Y-m-d H:i:s') : '';

            $row = [
                $r->id,
                $r->name,
                $r->age,
                $r->location,
                $createdAt,
                $updatedAt,
            ];

            foreach ($questions as $q) {
                $ans = $answers->get($q->id);
                $val = $ans->option_text ?? $ans->answer_text ?? '';
                $row[] = $val;
            }

            $data[] = $row;
        }

        return response()->json(['success' => true, 'data' => $data]);
    }
}


