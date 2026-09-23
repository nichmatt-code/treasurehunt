<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            ['title' => 'Misi 1: Petunjuk Pertama', 'description' => 'Temukan lokasi yang disebutkan dalam petunjuk pertama, lalu tuliskan nama tempatnya di sini.'],
            ['title' => 'Misi 2: Kode Rahasia', 'description' => 'Pecahkan kode rahasia yang kalian temukan dan tuliskan jawabannya.'],
            ['title' => 'Misi 3: Tantangan Tim', 'description' => 'Selesaikan tantangan tim dan laporkan hasilnya di sini.'],
        ];

        foreach ($questions as $index => $question) {
            Question::query()->updateOrCreate(
                ['title' => $question['title']],
                ['description' => $question['description'], 'order_no' => $index + 1]
            );
        }
    }
}
