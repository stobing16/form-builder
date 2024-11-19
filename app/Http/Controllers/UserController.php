<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:super-admin']);
    }

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
                    $col = '';
                    $col .= '<a class="btn btn-sm btn-warning edit" data-id="' . $row->id . '">Edit</a>';

                    if ($row->roles[0]->name != 'super-admin') {
                        $col .= '<a class="btn btn-sm btn-danger delete" data-id="' . $row->id . '">Delete</a>';
                    }

                    return $col;
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

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|min:5',
            'email' => 'required|email|unique:users,email',
            'role' => 'required',
        ]);

        try {
            $user = User::create([
                'name' => $request->username,
                'email' => $request->email,
                'password' => Hash::make("password"),
            ]);
            $user->assignRole($request->role);

            return response()->json([
                "message" => "User Successfully Inserted"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 422);
        }
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string|max:255|min:5',
            'email' => 'required|email',
            'role' => 'required',
        ]);

        try {
            $user = User::with('roles')->findOrFail($id);
            $user->name = $request->username;
            $user->email = $request->email;
            $user->syncRoles($request->role);

            $user->save();

            return response()->json([
                "message" => "User Successfully Updated"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 422);
        }
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            "message" => "User Successfully Deleted"
        ], 200);
    }
}
