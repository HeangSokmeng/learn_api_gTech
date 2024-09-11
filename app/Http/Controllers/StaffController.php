<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Staff::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function staffValidate(Request $request)
    {
        return Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
        ]);

    }
    public function store(Request $request)
    {
        $validator = $this->staffValidate($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $staff = Staff::create([
            'full_name' => $request->input('full_name'),
            'position' => $request->input('position'),
        ]);
        return response()->json(['staff' => $staff], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff)
    {
        return $staff::all();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff)
    {
        $validator = $this->staffValidate($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $staff = Staff::find($request->id);
        if(!$staff){
            return response()->json(['error' => 'staff already exists for this customer.'], 400);
        }
        $staff->update(
            [
                'full_name' => $request->input('full_name'),
                'position' => $request->input('position'),
            ]
        );
        return response()->json(['message'=> 'Updated success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff, $id)
    {
        $staff = Staff::find($id);

        if ($staff) {
            $staff->delete();
            return response()->json(['message' => 'staff deleted successfully.'], 200);
        } else {
            return response()->json(['message' => 'staff not found.'], 404);
        }
    }
}
