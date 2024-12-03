<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use App\Models\Response;
use App\Models\ResponseDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Yajra\DataTables\Facades\DataTables;

class ResponseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $data = Form::with('responses')->get(); // Ganti dengan query sesuai kebutuhan
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('published_at', function ($row) {
                    return Carbon::parse($row->updated_at)->locale('id')->format('j F Y');
                })
                ->addColumn('total_response', function ($row) {
                    return count($row->responses);
                })
                ->addColumn('action', function ($row) {
                    return '<a class="btn btn-sm btn-secondary show" data-id="' . $row->id . '">Show</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('response.index');
    }

    public function show($id)
    {
        $responses = Response::with('details', 'form')->where('form_id', $id)->get();
        return view('response.show', compact('id', 'responses'));
    }

    public function detail($id)
    {
        // $responses = ResponseDetail::with('question')->where('response_id', $id)->get();
        $responses = DB::select("
            SELECT
                response_details.id,
                questions.question as question,
                response_details.answer as answer,
                response_details.additional_answer as additional_answer,
                question_types.type as type,
                questions.options as options
            FROM response_details
            JOIN questions ON questions.id = response_details.id
            JOIN question_types ON question_types.id = questions.question_type_id
            WHERE response_id = ?;
        ", [$id]);

        $user = Response::findOrFail($id);

        return response()->json([
            'response' => $responses,
            'user' => $user
        ]);
    }

    public function exportExcel($id)
    {
        $form = Form::with('responses.details.question')->findOrFail($id);

        $questions = [];
        foreach ($form->responses as $key => $response) {
            foreach ($response->details as $detail) {
                if (!in_array($detail->question_id, $questions)) {
                    $questions[] = $detail->question_id;
                }
            }
        }

        if (count($questions) > 0) {
            $question_forms = Question::whereIn('id', $questions)->get();

            $rows = [];
            foreach ($form->responses as $response) {
                foreach ($question_forms as $question) {
                    $answer = '';
                    foreach ($response->details as $detail) {
                        if ($detail->question_id == $question->id) {

                            if ($detail->question->question_type_id == 4) {
                                $data = json_decode($detail->answer);

                                if (isset($detail->additional_answer)) {
                                    $data[] = $detail->additional_answer;
                                }

                                $answer = implode(', ', $data);
                            } else {
                                $answer = $detail->answer;
                            }
                            // break;
                        }
                    }

                    $row[$question->question] = $answer;
                }

                $rows[] = $row;
            }

            $headers = array_merge($question_forms->map(function ($form) {
                return $form->question;
            })->toArray());

            $headers = array_values($headers);
            $path = date("Ymd", time()) . '-response-' . $form->slug . '.xlsx';

            // Menulis data ke dalam file Excel
            $writer =  SimpleExcelWriter::create(storage_path($path))
                ->addHeader(array_values($headers))
                ->addRows($rows);

            $writer->close();
            $filePath = storage_path($path);

            return response()->file($filePath, [
                'Content-Disposition' => 'attachment; filename="' . $path . '"'
            ])->deleteFileAfterSend(true);
        } else {
            return response()->json([
                'error' => 'No Data Response'
            ], 422);
        }
    }

    public function showForUser($unique_url)
    {
        $form = Form::where('unique_url', $unique_url)->firstOrFail();
        $questions = Question::with('type')->where('form_id', $form->id)->orderBy('order', 'asc')->get();
        return view('forms', compact('form', 'questions'));
    }

    public function success()
    {
        return view('success-forms');
    }

    public function successUnique($unique_url)
    {
        $form = Form::with('questions.type')->where('unique_url', $unique_url)->firstOrFail();
        return view('success-forms', compact('form'));
    }

    public function storeForUser(Request $request, $unique_url)
    {
        $form = Form::with('questions')->where('unique_url', $unique_url)->first();

        // Membuat aturan validasi dinamis
        $rules = [];

        foreach ($form->questions as $question) {
            if ($question->is_required) {
                $rules[$form->unique_url . '.' . $question->id] = 'required';
            }
        }

        // Validasi data yang diterima
        $request->validate($rules, [
            'required' => 'Harap isi :attribute.'
        ]);

        DB::beginTransaction();
        try {

            $res = Response::create([
                'form_id' => $form->id
            ]);

            foreach ($request[$unique_url] as $key => $value) {

                $additional_answer = null;

                if (is_array($value)) {
                    $intKeys = array_filter($value, function ($key) {
                        return is_int($key); // Mengambil elemen dengan key integer
                    }, ARRAY_FILTER_USE_KEY);

                    $nonIntegerKeys = array_filter($value, function ($key) {
                        return !is_int($key); // Mengambil elemen dengan key non-integer
                    }, ARRAY_FILTER_USE_KEY);

                    $value = $intKeys;
                    $additional_answer = isset($nonIntegerKeys['additional_answer']) ? $nonIntegerKeys['additional_answer'] : null;
                }

                ResponseDetail::create([
                    'response_id' => $res->id,
                    'question_id' => $key,
                    'answer' => is_array($value) ? json_encode($value) : $value,
                    'additional_answer' => isset($additional_answer) ? $additional_answer : null,
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
