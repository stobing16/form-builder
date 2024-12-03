<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use App\Models\QuestionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    public function create($id)
    {
        $form = Form::with('questions.type')->findOrFail($id);
        $question_type = QuestionType::get();
        return view('questions.create', compact('form', 'question_type'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|min:5',
            'type' => 'required',
            'is_required' => 'nullable|in:yes',
        ]);

        try {
            $count = Question::where('form_id', $id)->count();
            Question::create([
                'form_id' => $id,
                'question' => $request->name,
                'slug' => Str::slug($request->name),
                'catatan' => $request->catatan,
                'question_type_id' => $request->type,
                'is_required' => isset($request->is_required) ? true :  false,
                'options' => !empty($request->options) ? json_encode($request->options) : null,
                'order' => $count + 1,
                'has_additional_question' => isset($request->has_additional_question) ? true : false,
            ]);

            return response()->json([
                "message" => "Form Successfully Inserted"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "error" => $th->getMessage()
            ], 422);
        }
    }

    public function edit($id)
    {
        $question = Question::findOrFail($id);
        $question_type = QuestionType::get();
        return view('questions.edit', compact('question', 'question_type'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|min:5',
            'type' => 'required',
            'is_required' => 'nullable|in:yes',
        ]);

        try {

            $question = Question::findOrFail($id);

            $question->question = $request->name;
            $question->slug = Str::slug($request->name);
            $question->question_type_id = $request->type;
            $question->catatan = $request->catatan;
            $question->is_required = isset($request->is_required) ? true :  false;
            $question->options = !empty($request->options) ? json_encode($request->options) : null;
            $question->has_additional_question = isset($request->has_additional_question) ? true : false;
            $question->save();

            return response()->json([
                "message" => "Form Successfully Updated"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "error" => $th->getMessage()
            ], 422);
        }
    }

    public function delete($id)
    {
        try {
            $question = Question::findOrFail($id);
            $question->delete();

            return response()->json([
                'message' => 'Form Successfully Deleted!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus pertanyaan. Silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
