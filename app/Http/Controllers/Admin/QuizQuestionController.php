<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class QuizQuestionController extends Controller
{
    public function index(Quiz $quiz)
    {
        return view('admin.quizzes.questions', [
            'quiz' => $quiz,
            'questions' => $quiz->questions,
        ]);
    }

    public function store(Request $request, Quiz $quiz)
    {
        $validated = $this->validateQuestion($request);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('quiz-questions', 'public');
        }

        $quiz->questions()->create([
            ...$validated,
            'order_no' => $quiz->questions()->count(),
        ]);

        return redirect()->route('admin.quizzes.questions', $quiz)->with('status', 'Soal berhasil ditambahkan.');
    }

    public function update(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        $validated = $this->validateQuestion($request);

        if ($request->hasFile('image')) {
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('quiz-questions', 'public');
        }

        $question->update($validated);

        return redirect()->route('admin.quizzes.questions', $quiz)->with('status', 'Soal berhasil diperbarui.');
    }

    public function destroy(Quiz $quiz, QuizQuestion $question)
    {
        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }

        $question->delete();

        return redirect()->route('admin.quizzes.questions', $quiz)->with('status', 'Soal berhasil dihapus.');
    }

    public function move(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        $direction = $request->input('direction');

        $questions = $quiz->questions()->orderBy('order_no')->get()->values();
        $currentPos = $questions->search(fn ($q) => $q->id === $question->id);
        $swapPos = $direction === 'up' ? $currentPos - 1 : $currentPos + 1;

        if ($swapPos >= 0 && $swapPos < $questions->count()) {
            $other = $questions[$swapPos];
            $tmp = $question->order_no;
            $question->update(['order_no' => $other->order_no]);
            $other->update(['order_no' => $tmp]);
        }

        return redirect()->route('admin.quizzes.questions', $quiz);
    }

    private function validateQuestion(Request $request): array
    {
        $optionCount = is_array($request->input('options')) ? count($request->input('options')) : 0;

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'options' => ['required', 'array', 'min:2', 'max:4'],
            'options.*' => ['required', 'string', 'max:150'],
            'correct_option_index' => ['required', 'integer', Rule::in(range(0, max(0, $optionCount - 1)))],
            'time_limit_seconds' => ['required', 'integer', 'min:5', 'max:120'],
            'points' => ['required', 'integer', 'min:100', 'max:5000'],
            'image' => ['nullable', 'image', 'max:4096'],
        ], [
            'question.required' => 'Pertanyaan wajib diisi.',
            'options.min' => 'Minimal 2 pilihan jawaban.',
            'options.*.required' => 'Semua pilihan jawaban wajib diisi.',
            'correct_option_index.in' => 'Pilih jawaban yang benar.',
        ]);

        unset($validated['image']);

        return $validated;
    }
}
