<?php

namespace App\Console\Commands;

use App\Models\Question;
use Illuminate\Console\Command;

class CorrectOptiionsInQueries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'questions:correct-options';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Correct stored options in queries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $questions = Question::where('options', 'like', '%"text"%')
            ->get();

        foreach($questions as $question) {
            $correctedOptions = [];
            echo "Processing: {$question->id}\n";
            $decodedOptions = json_decode($question->options, true);
            foreach($decodedOptions as $id => $option) {
                $correctedOptions[$id] = $option['text'];
            }
            $question->options = $correctedOptions;
            $question->save();
            echo "Corrected: {$question->id}\n";
        }
    }
}
