<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Customer::all();
    }

    public function customerValidate(Request $request)
    {
        return Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female,other',
            'date_of_birth' => 'required|date|before:today',
        ]);

    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $this->customerValidate($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $exists = Customer::where('phone_number', $request->input('phone_number'))
            ->first();
        // if Customer already have
        if ($exists) {
            return response()->json(['error' => 'Customer already exists for this customer.'], 400);
        }
        $existsEmail = Customer::where('email', $request->input('email'))
            ->first();
        // if Customer already have
        if ($existsEmail) {
            return response()->json(['error' => 'Email already exists for this customer.'], 400);
        }
        $customer = Customer::create([
            'full_name' => $request->input('full_name'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'address' => $request->input('address'),
            'gender' => $request->input('gender'),
            'date_of_birth' => $request->input('date_of_birth'),
        ]);
        return response()->json(['customer' => $customer], 201);
    }

    /**
     * Display the specified rsesource.
     */
    public function show(Customer $customer)
    {
        return $customer;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = $this->customerValidate($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $exists = Customer::where('phone_number', $request->input('phone_number'))->where('id', '!=', $request->id)->first();
        if ($exists) {
            return response()->json(['error' => 'Customer already exists for this customer.'], 400);
        }
        $customer = Customer::find($request->id);
        if (!$customer) {
            return response()->json(['error' => 'Customer already exists for this customer.'], 400);
        }
        $customer->update(
            [
                'full_name' => $request->input('full_name'),
                'email' => $request->input('email'),
                'phone_number' => $request->input('phone_number'),
                'address' => $request->input('address'),
                'gender' => $request->input('gender'),
                'date_of_birth' => $request->input('date_of_birth'),
            ]
        );
        return response()->json(['message' => 'Updated success']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $customer = Customer::find($id);

        if ($customer) {
            $customer->delete();
            return response()->json(['message' => 'Customer deleted successfully.'], 200);
        } else {
            return response()->json(['message' => 'Customer not found.'], 404);
        }
    }

}
