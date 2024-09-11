<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'age', 'major'];
    // config with StudentDetail
    public function detail(){
        return $this->hasOne(StudentDetail::class);
    }
}
