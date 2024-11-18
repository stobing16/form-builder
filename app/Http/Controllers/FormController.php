<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use App\Models\QuestionType;
use App\Models\Response;
use App\Models\ResponseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class FormController extends Controller
{
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $data = Form::all(); // Ganti dengan query sesuai kebutuhan
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-sm btn-secondary show" data-id="' . $row->id . '">Show</a>
                        <a class="btn btn-sm btn-warning edit" data-id="' . $row->id . '">Edit</a>
                        <a class="btn btn-sm btn-info response" data-id="' . $row->id . '">Response</a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('forms.index');
    }

    public function create()
    {
        $question_type = QuestionType::get();
        return view('forms.create', compact('question_type'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'form_name' => 'required|string|max:255|min:5',
            'description' => 'required|string|min:5',
            'questions.*.name' => 'required|string|max:255|min:5',
            'questions.*.type' => 'required',
            'questions.*.is_required' => 'nullable|in:yes',
        ]);

        DB::beginTransaction();

        try {
            $unique_url = $this->generateUniqueString();
            $form = Form::create([
                'unique_url' => $unique_url,
                'title' => $request->form_name,
                'slug' => Str::slug($request->form_name),
                'description' => $request->description,
                'is_active' => true,
            ]);

            foreach ($request->questions as $question) {
                Question::create([
                    'form_id' => $form->id,
                    'question' => $question['name'],
                    'slug' => Str::slug($question['name']),
                    'catatan' => isset($question['catatan']) ? $question['catatan'] : null,
                    'question_type_id' => $question['type'],
                    'is_required' => isset($question['is_required']) ? true :  false,
                    'options' => !empty($question['options']) ? json_encode($question['options']) : null,
                ]);
            }

            DB::commit();
            return response()->json([
                "message" => "Form Successfully Inserted"
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage()
            ], 422);
        }

        return $request->all();
    }

    protected function generateUniqueString()
    {
        do {
            $randomString = Str::random(20); // Generate a random string of length 20
        } while (Form::where('unique_url', $randomString)->exists());

        return $randomString;
    }

    public function show($id)
    {
        $form = Form::with('questions.type')->findOrFail($id);
        return view('forms.show', compact('form'));
    }

    public function edit($id)
    {
        $form = Form::findOrFail($id);
        return view('forms.edit', compact('form'));
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'form_name' => 'required|string|max:255|min:5',
            'description' => 'required|string|max:255|min:5',
        ]);

        try {
            $form = Form::findOrFail($id);

            $form->title = $request->form_name;
            $form->slug = Str::slug($request->form_name);
            $form->description = $request->description;
            $form->save();

            return response()->json([
                "message" => "Form Successfully Updated"
            ], 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                "error" => $th->getMessage()
            ], 500);
        }
    }

    public function preview($unique_url)
    {
        $form = Form::with('questions.type')->where('unique_url', $unique_url)->first();
        return view('forms.preview', compact('form'));
    }

    public function previewStore($unique_url, Request $request)
    {
        $form = Form::with('questions')->where('unique_url', $unique_url)->first();

        // Membuat aturan validasi dinamis
        $rules = [];

        $rules['name'] = 'required';
        $rules['email'] = 'required|email';

        foreach ($form->questions as $question) {
            if ($question->is_required) {
                $rules[$form->unique_url . '.' . $question->id] = 'required';
            }
        }

        // Validasi data yang diterima
        $validatedData = $request->validate($rules, [
            'required' => 'Harap isi :attribute.'
        ]);

        DB::beginTransaction();
        try {

            $response = Response::create([
                'form_id' => $form->id,
                'name' => $request['name'],
                'email' => $request['email'],
                'phone' => $request['phone'],
            ]);

            foreach ($request[$unique_url] as $key => $value) {
                ResponseDetail::create([
                    'response_id' => $response->id,
                    'question_id' => $key,
                    'answer' => is_array($value) ? json_encode($value) : $value,
                ]);
            }

            DB::commit();
            return response()->json([
                "message" => "Response Successfully Submited"
            ], 200);
        } catch (\Throwable $th) {
            Log::error($th);
            DB::rollback();
            return response()->json([
                "error" => $th->getMessage()
            ], 500);
        }
    }
}
