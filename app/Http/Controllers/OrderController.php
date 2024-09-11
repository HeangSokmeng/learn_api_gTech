<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Order::all();
    }

    public function customerValidate(Request $request)
    {
        return Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|string|max:20',
        ]);

    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request using the customerValidate method
        $validator = $this->customerValidate($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Calculate the total price
        $product = Product::find($request->input('product_id'));
        // $totalPrice = $request->input('quantity') * $request->input('price');
        if ($product) {
            $totalPrice = $request->input('quantity') * $product->price;
        } else {
            return response()->json(['error' => 'Product not found'], 404);
        }
        $order = Order::create([
            'product_id' => $request->input('product_id'),
            'customer_id' => $request->input('customer_id'),
            'quantity' => $request->input('quantity'),
            'price' => $product->price,
            'total_price' => $totalPrice,
        ]);
        return response()->json(['order' => $order], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Order $order)
    {
        $validator = $this->customerValidate($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Calculate the total price
        $product = Product::find($request->input('product_id'));
        // $totalPrice = $request->input('quantity') * $request->input('price');
        if ($product) {
            $totalPrice = $request->input('quantity') * $product->price;
        } else {
            return response()->json(['error' => 'Product not found'], 404);
        }
        $product = Product::find($request->id);
        // if(!$product){
        //     return response()->json(['error' => 'product already exists for this customer.'], 400);
        // }
        $product->update(
            [
                'product_id' => $request->input('product_id'),
                'customer_id' => $request->input('customer_id'),
                'quantity' => $request->input('quantity'),
                'price' => $product->price,
                'total_price' => $totalPrice,
            ]
        );
        return response()->json(['message'=> 'Updated success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    public function customerOrder(){
        // $cusOrder  = Customer::get();

        // $cusOrder  = Customer::with('orders')->get();
        // $cusWithOrder = [];
        // foreach($cusOrder as $aa){
        //     if(isset($aa->orders[0])){
        //         $cusWithOrder[] = $aa;
        //     }
        // }
        // return $cusWithOrder;


        // foreach($cusOrder as $ct){
        //     $ct->orders[] = Order::where('customer_id', $ct->id)->first();
        // }
        // return $cusOrder;

        $orders = Order::get();
        //  foreach($cusOrder as $ct){
        //     $ct->orders[] = $this->getOrder($orders, $ct->id);
        // }
        // return $cusOrder;


        $cust = DB::table('customers as c')->join('orders as o', 'o.customer_id','=','c.id')->selectRaw('DISTINCT o.customer_id,c.full_name as customer_name')->get();
         foreach($cust as $ct){
            $ct->orders= $this->getOrder($orders, $ct->customer_id);
        }
        return $cust;
    }

    function getOrder($order, $customer_id){
        $lst = [];
        foreach($order as $od){
            if($od->customer_id == $customer_id){
               $lst[]=  $od;
            }
        }
        return $lst;
    }
}
