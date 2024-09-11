<?php

namespace App\Http\Controllers;

use App\Models\UserThree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserThreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserThree::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function UserValidate(Request $request)
    {
        return Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'date_of_birth' => 'required|date_format:Y-m-d',
        ]);
    }

    public function store(Request $request)
    {
        $validator = $this->UserValidate($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        $user = UserThree::create([
            'name'          => $request->name,
            'date_of_birth' => $request->date_of_birth,
        ]);
        return response()->json(['users' => $user], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserThree $userThree)
    {
        //Dont show to view just in console
        return $userThree;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $validator = $this->UserValidate($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        $userThree = UserThree::find($id);
        if (!$userThree) return response()->json(['message' => 'User not found'], 404);
        $userThree->update([
            'name'          => $request->name,
            'date_of_birth' => $request->date_of_birth,
        ]);
        return response()->json(['users' => $userThree], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserThree $userThree, $id)
    {
       //delete by id
        UserThree::destroy($id);
        return response()->json(['message' => 'User deleted successfully'], 200);

    }
}
