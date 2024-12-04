<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\UserResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenAI;

use function Termwind\parse;

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
            ->take((env('USE_AI_FOR_QUESTIONS'))?5:10)
            ->get();
        
            if(count($questions) < 10 && env('USE_AI_FOR_QUESTIONS') && $theme !== '') {
            $exclude_questions = "";
            foreach($questions as $question) {
                $exclude_questions .= $question->question."\n";
            }
            $response = $this->client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are an assistant that evaluates answers to technical questions. Use this locale for answers: {$locale}."
                    ],
                    [
                        'role' => 'user',
                        'content' => "Generate ".(10 - count($questions))." questions with answers for ".$request->theme." interview in JSON response. Like Question:..., Answer:... Exclude these questions:".$exclude_questions
                    ],
                ],
                'response_format' => [
                    "type" => "json_object",
                ]
                // 'max_tokens' => 250,
            ]);

            $questions_new = json_decode($response['choices'][0]['message']['content']);
            if(isset($questions_new->interview_questions)) {
                foreach($questions_new->interview_questions as $new_question) {
                    $new_record = new Question;
                    $new_record->question = $new_question->Question;
                    $new_record->correct_answer = $new_question->Answer;
                    $new_record->theme = $request->theme;
                    $new_record->locale = $locale;
                    $new_record->save();
                    $questions[] = $new_record;
                }
            }
        }


        return response()->json($questions);
    }

    public function evaluateAnswer(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en'); 
        $responses = $request->input('responses'); 
        $totalScore = 0;
        $results = [];

        foreach ($responses as $response) {
            $question = Question::find($response['question_id']);
            if (!$question) {
                continue;
            }

            $user_answer = $response['user_answer'];

            $prompt = "Question: {$question->question}\nUser Answer: {$user_answer}\nCorrect Answer: {$question->correct_answer}\nEvaluate the answer and provide a score and feedback. Use this locale for answers: {$locale}. Do not translate 'score'.";

            $response = $this->client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are an assistant that evaluates answers to technical questions. Provide a score from 0 to 10 based on accuracy and relevance, followed by feedback."
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'max_tokens' => 250,
            ]);

            $evaluation = $response['choices'][0]['message']['content'];

            preg_match('/(S|s)core: (\d+)(\/10|)/', $evaluation, $scoreMatch);
            $score = $scoreMatch[2] ?? 0;

            $feedback = trim(preg_replace('/(S|s)core: (\d+)(\/10|)/', '', $evaluation));

            $results[] = [
                'question_id' => $question->id,
                'question' => $question->question,
                'score' => $score,
                'feedback' => $feedback,
                'user_answer' => $user_answer,
                'correct_answer' => $question->correct_answer,
            ];
            
            $totalScore += $score;
        }

        $last = UserResult::latest('created_at')->first()->attempt_id;

        UserResult::create([
            'user_id' => auth()->id(),
            'attempt_id' => $last+1 ?? 0,
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
