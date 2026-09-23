<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quiz = Quiz::query()->updateOrCreate(
            ['title' => 'Mission One Warm-Up'],
            ['description' => 'Quiz pemanasan seputar Mission One & BSD.']
        );

        if ($quiz->questions()->count() > 0) {
            return;
        }

        $questions = [
            ['question' => 'Di mana lokasi toko Bread Papa?', 'options' => ['Lantai 1', 'Lantai 2', 'Lantai 3', 'Lantai 4'], 'correct' => 1],
            ['question' => 'Mission One diperuntukkan untuk pelajar di kota mana?', 'options' => ['Jakarta', 'BSD', 'Bandung', 'Surabaya'], 'correct' => 1],
            ['question' => 'Ada berapa coach di Mission One?', 'options' => ['2', '3', '4', '5'], 'correct' => 1],
            ['question' => 'Siapa nama coach yang ikonnya kompas?', 'options' => ['Yoyo', 'Stefani', 'Nichmatt', 'Rafael'], 'correct' => 2],
            ['question' => 'Jam berapa Mission One dimulai (21 Oktober 2026)?', 'options' => ['15:00', '17:00', '19:00', '21:00'], 'correct' => 2],
        ];

        foreach ($questions as $index => $q) {
            $quiz->questions()->create([
                'question' => $q['question'],
                'options' => $q['options'],
                'correct_option_index' => $q['correct'],
                'time_limit_seconds' => 10,
                'points' => 1000,
                'order_no' => $index,
            ]);
        }
    }
}
