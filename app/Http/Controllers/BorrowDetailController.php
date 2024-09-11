<?php

namespace App\Http\Controllers;

use App\Models\BorrowDetail;
use Illuminate\Support\Facades\Request;

class BorrowDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return BorrowDetail::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show(BorrowDetail $borrowDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BorrowDetail $borrowDetail)
    {
        //
    }
}
