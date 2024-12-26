<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\UserResult;
use Illuminate\Http\Request;
use OpenAI;

class QuizController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
    }

    public function getQuestions(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');
        $theme = $request->input('theme', '');
        $questions = Question::where('locale', $locale)
            ->where('theme', $theme)
            ->inRandomOrder()
            ->take((env('USE_AI_FOR_QUESTIONS')) ? 5 : 10)
            ->get();
        
        return response()->json($questions);
    }

    public function evaluateAnswer(Request $request)
    {

        $validatedData = $request->validate([
            'responses' => 'required|array', // Ensure answers is an array
        ]);

        $responses = $validatedData['responses'];
        $totalScore = 0;
        $results = [];

        foreach ($responses as $response) {
            $question = Question::find($response['question_id']);
            if (! $question) {
                continue;
            }

            $userAnswers = $response['user_answer'];
            $userAnswers = is_array($userAnswers) ? $userAnswers : [$userAnswers];

            // Decode correct options stored in the database
            $correctOptions = json_decode($question->correct_option, true);

            // Ensure userAnswers is an array
            $userAnswers = is_array($userAnswers) ? $userAnswers : [$userAnswers];

            // Calculate the intersection of user answers and correct options
            $matchedAnswers = array_intersect($userAnswers, $correctOptions);

            // Calculate the percentage of correct answers
            $percentCorrect = 0;
            $percentCorrect = round(count($matchedAnswers) / count($correctOptions) * 10);

            // Determine if the user's answer is fully correct
            $isCorrect = empty(array_diff($userAnswers, $correctOptions)) &&
                        empty(array_diff($correctOptions, $userAnswers));

            // Increment score if fully correct
            if ($isCorrect) {
                $totalScore += 10;
            } else {
                $totalScore += $percentCorrect;
            }

            // Prepare correct answers
            $options = json_decode($question->options, true);
            $correctAnswers = array_intersect_key($options, array_flip($correctOptions));

            $results[] = [
                'question_id' => $question->id,
                'question' => $question->question,
                'score' => $percentCorrect,
                'feedback' => $question->correct_answer,
                'user_answer' => $userAnswers,
                'correct_answers' => $correctAnswers,
            ];

        }
        // die();

        $userResultLast = UserResult::latest('created_at')->first();
        $last = (isset($userResultLast)) ? $userResultLast->attempt_id : 0;

        UserResult::create([
            'user_id' => auth()->id(),
            'attempt_id' => $last + 1  ?? 0,
            'user_answer' => '',
            'score' => $totalScore,
            'feedback' => json_encode($results),
        ]);

        return response()->json([
            'details' => $results,
            'totalScore' => $totalScore,
        ]);
    }

    public function getResults()
    {
        $results = UserResult::where('user_id', auth()->id())->get();

        $totalScore = $results->sum('score');

        return response()->json([
            'results' => $results,
            'total_score' => $totalScore,
        ]);
    }
}
