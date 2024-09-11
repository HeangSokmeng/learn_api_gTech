<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['full_name', 'email', 'phone_number', 'address', 'gender', 'date_of_birth'];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
}
