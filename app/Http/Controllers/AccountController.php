<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    public function index()
    {
        return view('account.index');
    }

    public function changePassword()
    {
        return view('account.change-password');
    }
    public function changePasswordStore(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|same:new_password',
            'new_password_confirmation' => 'required|same:new_password',
        ]);

        try {
            $oldPassword = Auth::user()->password;
            if (Hash::check($request->old_password, $oldPassword)) {
                $user = User::findOrFail(Auth::id());
                $user->password = bcrypt($request->new_password);

                $user->save();

                return response()->json([
                    'message' => "Your password has been updated"
                ], 200);
            }

            return response()->json([
                'error' => "Old Password isn't match"
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
