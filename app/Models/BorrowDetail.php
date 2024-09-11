<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowDetail extends Model
{
    use HasFactory;
    protected $fillable = ['borrow_id', 'book_id', 'qty', 'qty_borrow', 'status'];

    // Relationships
    public function borrow()
    {
        return $this->belongsTo(Borrows::class);
    }

    public function book()
    {
        return $this->belongsTo(Books::class);
    }
}
