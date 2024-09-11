<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserThree extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'date_of_birth'];
    public function borrows() {
        return $this->hasMany(Borrows::class, 'borrower_id');
    }

    public function librarianBorrows() {
        return $this->hasMany(Borrows::class, 'librarian_id');
    }
}
