<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrows extends Model
{
    use HasFactory;
    protected $fillable = [
        'borrower_id', 'librarian_id', 'borrow_date', 'expect_return_date', 'return_date','borrow_status','number_of_borrow_books'
    ];


    public function borrower() {
        return $this->belongsTo(UserThree::class, 'borrower_id');
    }

    public function librarian() {
        return $this->belongsTo(UserThree::class, 'librarian_id');
    }
    public function borrowDetails()
    {
        return $this->hasMany(BorrowDetail::class, 'borrow_id', 'id');
    }

}
