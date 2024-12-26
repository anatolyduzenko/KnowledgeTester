<?php

namespace App\Console\Commands;

use App\Models\Question;
use Illuminate\Console\Command;
use OpenAI;

class UpdateQuestionsWithOptions extends Command
{

    protected $client;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'questions:update-options {locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update questions table with options and correct answers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
        $locale = $this->argument('locale');

        $questions = Question::where('locale', $locale)
            ->where('correct_option', NULL)
            ->take(50)
            ->get();
        
        foreach ($questions as $question) {

            $response = $this->client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are an assistant that evaluates answers to technical questions. Use this locale for answers: {$locale}.",
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Generate 4-6 answers for "'.$question->question.'" question in JSON response.
                             Answers must be short, one correct option and other are wrong. Use numbers or letters to identify answers.
                             If there are many correct options, provide a way to select many. If there is no correct options provide empty structrure. Use Object as type.
                             Provide correct option(s) in new field "correct_options". Use aray type for that field. That field should be outside the "answers"
                             ',
                    ],
                ],
                'response_format' => [
                    'type' => 'json_object',
                ],
                // 'max_tokens' => 250,
            ]);

            $answers = [];
            $options = json_decode($response['choices'][0]['message']['content']);
            
            $question->options = json_encode($options->answers);
            $question->correct_option = json_encode($options->correct_options);
            $question->save();
        }

        $this->info('Questions table updated successfully!');
        return 0;
    }
}
