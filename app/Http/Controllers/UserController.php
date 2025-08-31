<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(
            User::orderBy('name')
                ->get()
        );
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
}
