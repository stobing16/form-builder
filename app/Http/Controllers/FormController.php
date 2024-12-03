<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use App\Models\QuestionType;
use App\Models\Response;
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
                    // <a class="btn btn-sm btn-warning edit" data-id="' . $row->id . '">Edit</a>
                    return '
                        <a class="btn btn-sm btn-secondary show" data-id="' . $row->id . '">Show</a>
                        <a class="btn btn-sm btn-info response" data-id="' . $row->id . '">Response</a>
                        <a class="btn btn-sm btn-danger delete" data-id="' . $row->id . '">Delete</a>
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
                'is_active' => true
            ]);

            $no = 1;
            foreach ($request->questions as $question) {
                Question::create([
                    'form_id' => $form->id,
                    'question' => $question['name'],
                    'slug' => Str::slug($question['name']),
                    'catatan' => isset($question['catatan']) ? $question['catatan'] : null,
                    'question_type_id' => $question['type'],
                    'is_required' => isset($question['is_required']) ? true :  false,
                    'options' => !empty($question['options']) ? json_encode($question['options']) : null,
                    'order' => $no,
                    'has_additional_question' => isset($question['has_additional_question']) ? true : false,
                ]);
                $no++;
            }

            $no = 1;

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

    public function show(Request $request, $id)
    {
        $form = Form::with('questions.type')->findOrFail($id);
        $question_values = Question::with('type')->where('form_id', $id)->orderBy('order', 'asc');

        if ($request->expectsJson()) {
            return DataTables::of($question_values)
                ->addIndexColumn()
                ->addColumn('label', function ($row) {
                    return $row->type->label;
                })
                ->addColumn('required', function ($row) {
                    return $row->is_required ? 'Ya' : 'Tidak';
                })
                ->addColumn('option_values', function ($row) {
                    $data = json_decode($row->options);
                    if ($row->has_additional_question) {
                        $data[] = "Lainnya ...";
                    }
                    return $data;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button type="button" class="btn btn-sm btn-warning edit" data-id="' . $row->id . '">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger delete" data-id="' . $row->id . '">Delete</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // $questions = $question_values->get();
        return view('forms.show', compact('form'));
    }

    public function updateOrderForm(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:questions,id',
            'order' => 'required|integer',
        ]);

        $question = Question::find($request->id);

        $currentOrder = $question->order;
        $newOrder = $request->order;

        // SWAP ORDER
        $swappedQuestion = Question::where('order', $newOrder)->first();

        $question->order = $newOrder;
        $swappedQuestion->order = $currentOrder;

        $swappedQuestion->save();
        $question->save();

        return response()->json(['success' => true]);
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

    public function delete($id)
    {
        try {
            $form = Form::findOrFail($id);
            $form->delete();

            return response()->json([
                'message' => 'Form Successfully Deleted!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus forms. Silakan coba lagi.',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function preview($unique_url)
    {
        $form = Form::where('unique_url', $unique_url)->first();
        $questions = Question::where('form_id', $form->id)->orderBy('order', 'asc')->get();

        return view('forms.preview', compact('form', 'questions'));
    }

    // public function previewStore($unique_url, Request $request)
    // {
    //     $form = Form::with('questions')->where('unique_url', $unique_url)->first();

    //     // Membuat aturan validasi dinamis
    //     $rules = [];

    //     $rules['name'] = 'required';
    //     $rules['email'] = 'required|email';

    //     foreach ($form->questions as $question) {
    //         if ($question->is_required) {
    //             $rules[$form->unique_url . '.' . $question->id] = 'required';
    //         }
    //     }

    //     // Validasi data yang diterima
    //     $validatedData = $request->validate($rules, [
    //         'required' => 'Harap isi :attribute.'
    //     ]);

    //     DB::beginTransaction();
    //     try {

    //         $response = Response::create([
    //             'form_id' => $form->id,
    //             'name' => $request['name'],
    //             'email' => $request['email'],
    //             'phone' => $request['phone'],
    //         ]);

    //         foreach ($request[$unique_url] as $key => $value) {
    //             ResponseDetail::create([
    //                 'response_id' => $response->id,
    //                 'question_id' => $key,
    //                 'answer' => is_array($value) ? json_encode($value) : $value,
    //             ]);
    //         }

    //         DB::commit();
    //         return response()->json([
    //             "message" => "Response Successfully Submited"
    //         ], 200);
    //     } catch (\Throwable $th) {
    //         Log::error($th);
    //         DB::rollback();
    //         return response()->json([
    //             "error" => $th->getMessage()
    //         ], 500);
    //     }
    // }
}
