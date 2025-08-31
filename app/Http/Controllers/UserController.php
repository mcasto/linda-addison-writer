<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(
            User::orderBy('name')
                ->get()
        );
    }

    public function show()
    {
        return response()->json(['request' => Auth::guard('admin')
            ->user()]);
    }

    public function store(Request $request)
    {
        $valid = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'string|max:255|required',
            'permissions' => 'required|boolean'
        ]);

        $user = User::create($valid);
        $user->permissions = $user->permissions ? 1 : 0;  // makes new user display properly in front end

        return response()->json(['status' => 'ok', 'user' => $user]);
    }

    public function update(int $id, Request $request)
    {
        $valid = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'string|max:255|sometimes',
            'permissions' => 'required|boolean'
        ]);

        $user = User::find($id);
        if (!$user) {
            return response(['status' => 'error', 'message' => 'User not found.']);
        }

        $user->update($valid);

        return response()->json(['status' => 'ok']);
    }

    public function destroy(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found.']);
        }

        $user->delete();

        return response()->json(['status' => 'ok']);
    }
}
