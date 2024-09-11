<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            // 'customer_id' => 'required|exists:customers,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        //if  fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $exists = Product::where('name', $request->input('name'))
            ->exists();
        // if product already have
        if ($exists) {
            return response()->json(['error' => 'Product already exists for this customer.'], 400);
        }

        // dont have the  product so it execute to create
        $product = Product::create([
            'name' => $request->input('name'),
            // 'customer_id' => $request->input('customer_id'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'stock' => $request->input('stock'),
        ]);
        return response()->json(['product' => $product], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $product;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            // 'customer_id' => 'required|exists:customers,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);
        $pd = $product->find($request->id);
        if (!$pd) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $pd->update($fields);
        return response()->json(['message' => 'Product Updated successfully.'], 200);
       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, $id)
    {
        $product = Product::find($id);

    if ($product) {
        $product->delete();
        return response()->json(['message' => 'product deleted successfully.'], 200);
    } else {
        return response()->json(['message' => 'product not found.'], 404);
    }
    }
}
