<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $data = User::with('roles')->whereHas('roles', function ($q) use ($request) {
                if ($request->has('role') && $request->role != '') {
                    $q->where('name',  $request->role);
                }
            })->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('role', function ($row) {
                    return $row->roles[0]->name;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-sm btn-warning edit" data-id="' . $row->id . '">Edit</a>
                        <a class="btn btn-sm btn-danger delete" data-id="' . $row->id . '">Delete</a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('users.index');
    }

    // public function show()
    // {
    //     return view('users.show');
    // }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request) {}
    public function edit()
    {
        return view('users.edit');
    }
    public function update(Request $request, $id) {}
}
