<?php

namespace App\Http\Controllers;

use App\Models\Books;
use App\Models\BorrowDetail;
use App\Models\Borrows;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BorrowsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Borrows::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function BorrowValidate(Request $request)
    {
        return Validator::make($request->all(), [
            'books' => 'required|array',
            'borrower_id' => 'required|integer',
            'librarian_id' => 'required|integer',
            'borrow_date' => 'required|date_format:Y-m-d',
            'expect_return_date' => 'required|date_format:Y-m-d',
            'return_date' => 'nullable|date_format:Y-m-d',
            'borrow_status' => 'nullable|in:Borrow,Return',
            'number_of_borrow_books' => 'nullable|integer|max:255',
        ]);

    }
    public function store(Request $request)
    {
        $validator = $this->BorrowValidate($request);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // $borrow = Borrows::where('book_id', $request->book_id)
        //     ->where('borrow_status', 'Borrow')
        //     ->where('borrower_id', $request->borrower_id)
        //     ->first();
        // if ($borrow) {
        //     return response()->json(['error' => 'A borrow request already exists'], 409);
        // }

        // $book = DB::table('books')->where('id', $request->book_id)->first();
        // // return $book;
        // // return $request->number_of_borrow_books;
        // if (!$book) {
        //     return response()->json(['error' => 'Book not found'], 404);
        // }

        // DB::table('books')
        //     ->where('id', $request->book_id)
        //     ->update([
        //         'number_of_books' => $book->number_of_books - $request->number_of_borrow_books,
        //     ]);
        DB::beginTransaction();
        try {
            $borrow = Borrows::create([
                'borrower_id' => $request->borrower_id,
                'librarian_id' => $request->librarian_id,
                'borrow_date' => $request->borrow_date,
                'expect_return_date' => $request->expect_return_date,
                'borrow_status' => 'Borrow',
            ]);
            // return "yes";
            if ($borrow) {
                foreach ($request->books as $book) {
                    $existBook = Books::find($book['id']);
                    if (!$existBook) {
                        return response()->json(['error' => 'Book not found'], 404);
                    }
                    BorrowDetail::create([
                        'borrow_id' => $borrow->id,
                        'book_id' => $book['id'],
                        'qty' => $book['qty'],
                        'qty_borrow' => $book['qty'],
                        'status' => 'Borrowed'
                    ]);
                    $num_of_book = $existBook->number_of_books;
                    if($num_of_book < $book['qty']) return response()->json(["message" => "Do not have qty for borrow"]);
                    $existBook->number_of_books -= $book['qty'];
                    $existBook->save();
                }
            }
            DB::commit();
            return response()->json(["message" => "Success"]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getTraceAsString());
            return response()->json(["message" => "fail"], 500);
        }
        // return response()->json($borrow, 201);
    }
    //get borrow detail
    public function getBorrowDetails($rows, $borrow_id)
    {
        $list = [];
        foreach ($rows as $row) {
            if ($row->borrow_id == $borrow_id) {
                // $row->book_name = $row->book->title;
                // unset($row->book);
                $list[] = $rows;

            }
        }
        return $list;
    }
    public function getBorrowDetail($id)
    {
        $borrowDetail = Borrows::where('id', $id)->first();

        // $details = BorrowDetail::with('book')->get();
        // $details = DB::table('borrow_details as bd')
        //     ->join('books as b', 'bd.book_id', '=', 'b.id')->get();

        if (!$borrowDetail) {
            return response()->json(['error' => 'Borrow record not found'], 404);
        }

        foreach($borrowDetail->borrowDetails as $data){
            $data->book_name = $data->book->title;
            unset($data->book);
        }
        // $borrowDetail->books = $this->getBorrowDetails($details, $borrowDetail->id);
        return $borrowDetail;
    }
    public function show(Borrows $borrows)
    {
        return $borrows;
    }
    public function update(Request $request, $id)
{
    $borrows = Borrows::find($id);
    if (!$borrows) {
        return response()->json(['error' => 'Borrow record not found'], 404);
    }

    $totalRefund = 0;

    foreach ($request->books as $book) {
        $borrowDetail = BorrowDetail::where('borrow_id', $id)->where('book_id', $book['id'])->first();
        if (!$borrowDetail) {
            return response()->json(['error' => 'Borrow detail not found for book id ' . $book['id']], 404);
        }

        $exist_book = Books::find($book['id']);
        if (!$exist_book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        $returnQty = $book['qty'];
        $borrowedQty = $borrowDetail->qty;

        // If the return quantity matches the borrowed quantity
        if ($returnQty == $borrowedQty) {
            $exist_book->number_of_books += $returnQty;
            $exist_book->save();

            $borrowDetail->update([
                'qty' => 0,
                'status' => 'Returned All Book'
            ]);
        }
        elseif ($returnQty < $borrowedQty) {
            $exist_book->number_of_books += $returnQty;
            $exist_book->save();

            $borrowDetail->update([
                'qty' => $borrowedQty - $returnQty,
                'status' => 'Partially Returned'
            ]);

            $unreturnedQty = $borrowedQty - $returnQty;
            $refundPerBook = 5;
            $totalRefund += $unreturnedQty * $refundPerBook;
        } else {
            return response()->json(['error' => 'Returned quantity exceeds borrowed quantity'], 400);
        }
    }

    $allBooks = BorrowDetail::where('borrow_id', $id)->sum('qty');
    if ($allBooks == 0) {
        $borrows->update([
            'return_date' => $request->return_date,
            'borrow_status' => 'Return',
        ]);
    }

    return response()->json([
        'message' => 'Books returned successfully',
        'borrow' => $borrows,
        'total_refund' => $totalRefund
    ], 200);
}

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $borrows, $id)
    {
        $borrows = Borrows::find($id);
        $borrows->delete();
    }
    public function calculateFine($borrowId)
    {
        $borrow = DB::table('borrows as b')
            ->join('books as bk', 'b.book_id', '=', 'bk.id')
            ->join('user_threes as s', 'b.borrower_id', '=', 's.id')
            ->join('user_threes as u', 'b.librarian_id', '=', 'u.id')
            ->select(
                's.name as borrower_name',
                'u.name as librarian_name',
                'bk.title as book_name',
                'bk.number_of_books as number_of_books',
                'b.borrow_date',
                'b.return_date',
                'b.expect_return_date'
            )
            ->where('b.id', $borrowId)
            ->first();
        if (!$borrow) {
            return response()->json(['error' => 'Borrow id in record not found'], 404);
        }
        if (is_null($borrow->return_date)) {
            return response()->json(['error' => 'Dont have Return date'], 400);
        }
        $returnDate = Carbon::createFromFormat('Y-m-d', $borrow->return_date);
        $expectReturnDate = Carbon::createFromFormat('Y-m-d', $borrow->expect_return_date);
        $daysLate = $expectReturnDate->diffInDays($returnDate, false);
        $fine = 0;
        if ($daysLate > 5 && $daysLate <= 10) {
            $fine = 5;
        } elseif ($daysLate > 10) {
            $fine = 15;
        }
        return response()->json([
            'Borrower Name' => $borrow->borrower_name,
            'Librarian Name' => $borrow->librarian_name,
            'Book Name' => $borrow->book_name,
            'Borrow Date' => $borrow->borrow_date,
            'expect Return Date' => $borrow->expect_return_date,
            'Number of Book' => $borrow->number_of_books,
            'Return Date' => $borrow->return_date,
            'Late Days' => $daysLate > 0 ? $daysLate : 0,
            'Late Return Payment' => $fine,
        ]);
    }

    public function getListBorrow($borrowId)
    {
        $borrow = DB::table('borrows as b')
            ->join('books as bk', 'b.book_id', '=', 'bk.id')
            ->join('user_threes as s', 'b.borrower_id', '=', 's.id')
            ->join('user_threes as u', 'b.librarian_id', '=', 'u.id')
            ->select(
                's.name as borrower_name',
                'u.name as librarian_name',
                'bk.title as book_name',
                'b.borrow_date',
                'b.return_date',
                'b.expect_return_date'
            )
            ->where('b.id', $borrowId)
            ->first();

        return $borrow;
    }
}
