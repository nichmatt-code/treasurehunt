<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        return view('admin.quizzes.index', [
            'quizzes' => Quiz::withCount('questions')->orderByDesc('created_at')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'title.required' => 'Judul quiz wajib diisi.',
        ]);

        $quiz = Quiz::create($validated);

        return redirect()->route('admin.quizzes.questions', $quiz)->with('status', 'Quiz berhasil dibuat, sekarang tambahkan soal.');
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'title.required' => 'Judul quiz wajib diisi.',
        ]);

        $quiz->update($validated);

        return redirect()->route('admin.quizzes.index')->with('status', 'Quiz berhasil diperbarui.');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')->with('status', 'Quiz berhasil dihapus.');
    }
}
