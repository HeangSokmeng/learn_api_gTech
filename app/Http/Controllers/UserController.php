<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json(['users' => $users], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function userValidate(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => ['required','string','max:255'],
            'email' => ['required', 'string', 'max:255'],
        ]);

    }
    public function store(Request $request)
    {
        $validator = $this->userValidate($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ]);
        return response()->json(['users' => $user], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json(['users' => $user], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = $this->userValidate($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::find($request->id);
        if (!$user) {
            return response()->json(['error' => 'user already exists.'], 400);
        }
        $user->update(
            [
                'name' => $request->input('name'),
                'email' => $request->input('email')
            ]
        );
        return response()->json(['message' => 'Updated success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
