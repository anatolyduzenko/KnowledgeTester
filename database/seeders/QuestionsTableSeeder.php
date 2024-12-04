<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('questions')->insert([
            // English Questions
            [
                'question' => 'What is PHP and what is it used for?',
                'correct_answer' => 'PHP is a server-side scripting language used for web development to create dynamic and interactive websites.',
                'locale' => 'en',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What function is used to include another PHP file?',
                'correct_answer' => 'The include() and require() functions are used to include one PHP file in another.',
                'locale' => 'en',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Russian Questions
            [
                'question' => 'Что такое PHP и для чего он используется?',
                'correct_answer' => 'PHP — это серверный язык сценариев, используемый для веб-разработки, чтобы создавать динамические и интерактивные сайты.',
                'locale' => 'ru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Какая функция используется для подключения другого PHP-файла?',
                'correct_answer' => 'Функции include() и require() используются для подключения одного PHP-файла к другому.',
                'locale' => 'ru',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
