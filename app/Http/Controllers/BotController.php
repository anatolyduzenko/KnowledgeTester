<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI;
use stdClass;

class BotController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
    }

    public function getQuestion()
    {
        $questions = [
            ['id' => 1, 'question' => 'What is PHP and what is it used for?'],
            ['id' => 2, 'question' => 'What function is used to include another PHP file?'],
            ['id' => 3, 'question' => 'Explain the difference between == and === in PHP.'],
        ];

        return response()->json($questions[array_rand($questions)]);
    }

    public function checkAnswer(Request $request)
    {
        $questionId = $request->input('id');
        $userAnswer = $request->input('answer');

        $prompt = "Question: {$request->input('question')}\nUser Answer: {$userAnswer}\nCorrect Answer: {$userAnswer}\nEvaluate the answer and provide a score and feedback.";

        $obj = new stdClass();
        $obj->role = 'user';
        $obj->content = $prompt;

        $response = $this->client->chat()->create([
            'model' => 'gpt-4-turbo',
            'messages' => [
                $obj
            ],
            'max_tokens' => 150,
        ]);

        $evaluation = $response['choices'][0]['message']['content'];

        preg_match('/Score: (\d+)\/10/', $evaluation, $scoreMatch);
        $score = $scoreMatch[1] ?? 0;

        $feedback = trim(str_replace("Score: $score/10", '', $evaluation));

        return response()->json([
            'evaluation' => $response['choices'][0]['message']['content'] ?? 'No feedback provided.',
            'feedback' => $feedback,
            'score' => $score
        ]);
    }
}
