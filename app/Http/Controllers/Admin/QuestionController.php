<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index()
    {
        return view('admin.questions.index', [
            'questions' => Question::orderBy('order_no')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateQuestion($request);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('questions', 'public');
        }

        Question::create($validated);

        return redirect()->route('admin.questions.index')->with('status', 'Soal berhasil ditambahkan.');
    }

    public function update(Request $request, Question $question)
    {
        $validated = $this->validateQuestion($request);

        if ($request->hasFile('image')) {
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('questions', 'public');
        }

        $question->update($validated);

        return redirect()->route('admin.questions.index')->with('status', 'Soal berhasil diperbarui.');
    }

    public function destroy(Question $question)
    {
        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }

        $question->delete();

        return redirect()->route('admin.questions.index')->with('status', 'Soal berhasil dihapus.');
    }

    private function validateQuestion(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'order_no' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
        ], [
            'title.required' => 'Judul soal wajib diisi.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 4MB.',
        ]);
    }
}
