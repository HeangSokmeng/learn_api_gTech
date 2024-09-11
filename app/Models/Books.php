<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Books extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'author', 'description', 'number_of_books'];
    public function borrows() {
        return $this->hasMany(Borrows::class);
    }
    public function borrowDetails()
    {
        return $this->hasMany(BorrowDetail::class);
    }
}
